<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VerifierController extends Controller
{
    /**
     * 🔧 VERSION SIMPLIFIÉE - Afficher le formulaire d'authentification
     */
    public function showAuthForm(Request $request)
    {
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        Log::info('=== VERIFIER AUTH FORM ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'session_authenticated' => session()->has('verifier_authenticated'),
            'session_id' => session('verifier_id'),
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug
        ]);
    
        // 🔧 LOGIQUE SIMPLE: Si authentifié, rediriger vers scan
        if (session()->has('verifier_authenticated') && session('verifier_id')) {
            Log::info('Utilisateur déjà authentifié, redirection vers scan');
            return redirect()->route('event.verifier.scan', [
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ]);
        }
        
        try {
            $organization = DB::table('organizations')
                             ->where('org_key', $orgSlug)
                             ->first();
            
            if (!$organization) {
                return $this->renderAuthView($orgSlug, $eventSlug, null, null, "Organisation non trouvée");
            }
            
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->select('*')
                       ->where('event_slug', $eventSlug)
                       ->first();
            
            if (!$event) {
                return $this->renderAuthView($orgSlug, $eventSlug, $organization, null, "Événement non trouvé");
            }
            
            Log::info('Événement récupéré avec couleurs dans showAuthForm', [
                'event_id' => $event->id,
                'event_title' => $event->event_title,
                'primary_color' => $event->primary_color,
                'secondary_color' => $event->secondary_color,
                'org_slug' => $orgSlug
            ]);
            
            Log::info('Affichage du formulaire d\'authentification');
            return $this->renderAuthView($orgSlug, $eventSlug, $organization, $event);
            
        } catch (\Exception $e) {
            Log::error('Erreur dans showAuthForm', [
                'error' => $e->getMessage(),
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ]);
            
            return $this->renderAuthView($orgSlug, $eventSlug, null, null, 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * 🔧 AUTHENTIFICATION SIMPLIFIÉE
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string|max:10'
        ]);

        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        Log::info('Tentative d\'authentification', [
            'access_code' => $request->access_code,
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug
        ]);
        
        try {
            $organization = DB::table('organizations')
                             ->where('org_key', $orgSlug)
                             ->first();
            
            if (!$organization) {
                return back()->withErrors(['access_code' => 'Organisation non trouvée.']);
            }
            
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->select('*')
                       ->where('event_slug', $eventSlug)
                       ->first();
            
            if (!$event) {
                return back()->withErrors(['access_code' => 'Événement non trouvé.']);
            }
            
            $verifier = DB::connection('tenant')
                         ->table('verifiers')
                         ->where('event_id', $event->id)
                         ->where('access_code', $request->access_code)
                         ->where('is_active', true)
                         ->first();

            if (!$verifier) {
                Log::warning('Code d\'accès invalide', [
                    'access_code' => $request->access_code,
                    'ip' => $request->ip()
                ]);
                
                return back()->withErrors(['access_code' => 'Code d\'accès invalide.']);
            }

            // Mettre à jour la dernière connexion
            DB::connection('tenant')
              ->table('verifiers')
              ->where('id', $verifier->id)
              ->update(['last_login' => now()]);

            // 🔧 CRÉER LA SESSION SIMPLE
            session([
                'verifier_authenticated' => true,
                'verifier_id' => $verifier->id,
                'verifier_name' => $verifier->name,
                'verifier_email' => $verifier->email,
                'verifier_role' => $verifier->role,
                'verifier_allowed_zones' => $verifier->allowed_zones ? explode(',', $verifier->allowed_zones) : [],
                'verifier_last_activity' => now(),
                'verifier_event_id' => $event->id,
                'verifier_org_slug' => $orgSlug,
                'verifier_event_slug' => $eventSlug
            ]);

            Log::info('Authentification réussie', [
                'verifier_id' => $verifier->id,
                'verifier_name' => $verifier->name
            ]);

            return redirect()->route('event.verifier.scan', [
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ])->with('success', "Bienvenue {$verifier->name} !");

        } catch (\Exception $e) {
            Log::error('Erreur authentification', [
                'error' => $e->getMessage(),
                'access_code' => $request->access_code
            ]);

            return back()->withErrors(['access_code' => 'Erreur système.']);
        }
    }

    /**
     * 🔧 INTERFACE DE SCAN SIMPLIFIÉE
     */
    public function showScanInterface(Request $request)
    {
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        Log::info('Affichage interface scan', [
            'verifier_id' => session('verifier_id'),
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug
        ]);
        
        // Vérifier l'authentification
        if (!$this->isVerifierAuthenticated()) {
            return redirect()->route('event.verifier.auth', [
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ])->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }
        
        try {
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->select('*')
                       ->where('event_slug', $eventSlug)
                       ->first();
            
            if (!$event) {
                return back()->with('error', 'Événement non trouvé.');
            }

            // Récupérer les zones d'accès configurées pour cet événement
            $configuredZones = $this->getEventAccessZones($event->id);
            $allowedZones = $this->getVerifierAllowedZones(session('verifier_id'));
            
            // Statistiques rapides
            $stats = $this->getVerificationStats($event->id);
            
            return view('verifier.scan', [
                'event' => (object) $event,
                'organization' => (object) $organization,
                'configured_zones' => $configuredZones,
                'allowed_zones' => $allowedZones,
                'current_event' => session('current_zone', 'gala'),
                'stats' => $stats,
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug,
                'verifier' => [
                    'id' => session('verifier_id'),
                    'name' => session('verifier_name'),
                    'role' => session('verifier_role')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur interface scan', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur de chargement.');
        }
    }

    /**
     * 🔧 VÉRIFICATION SIMPLE SANS ZONE (pour compatibilité)
     */
    public function verifyTicket($org_slug, $event_slug, $ticket_hash)
    {
        Log::info('🔄 Redirection vers vérification avec zone', [
            'org_slug' => $org_slug,
            'event_slug' => $event_slug,
            'ticket_hash' => $ticket_hash
        ]);

        // Rediriger vers la méthode avec zone par défaut
        return redirect()->route('event.verifier.verify-zone', [
            'org_slug' => $org_slug,
            'event_slug' => $event_slug,
            'ticket_hash' => $ticket_hash
        ] + request()->query());
    }

    public function logout(Request $request)
    {
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        Log::info('Déconnexion vérificateur', [
            'verifier_id' => session('verifier_id')
        ]);

        session()->flush();

        return redirect()->route('event.verifier.auth', [
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug
        ])->with('success', 'Déconnexion réussie.');
    }

    public function verificationHistory(Request $request)
    {
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        if (!$this->isVerifierAuthenticated()) {
            return redirect()->route('event.verifier.auth', [
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ]);
        }
        
        try {
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                    ->table('events')
                    ->select('*')
                    ->where('event_slug', $eventSlug)
                    ->first();
            
            if (!$event) {
                return back()->with('error', 'Événement non trouvé.');
            }

            // Récupérer l'ID du vérificateur connecté
            $verifierId = session('verifier_id');
            $verifierName = session('verifier_name');
            
            if (!$verifierId) {
                return back()->with('error', 'Session vérificateur invalide.');
            }

            // Récupérer uniquement les vérifications de ce vérificateur
            $verifications = $this->getPersonalVerifications($event->id, $verifierId);
            $stats = $this->getPersonalVerificationStats($event->id, $verifierId);
            
            return view('verifier.history', [
                'verifications' => $verifications,
                'stats' => $stats,
                'event' => (object) $event,
                'organization' => (object) $organization,
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug,
                'verifier' => [
                    'id' => $verifierId,
                    'name' => $verifierName,
                    'role' => session('verifier_role')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur historique vérifications', [
                'verifier_id' => session('verifier_id'),
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Erreur lors du chargement de l\'historique.');
        }
    }

    private function getPersonalVerifications($eventId, $verifierId)
    {
        return DB::connection('tenant')
            ->table('ticket_verifications as tv')
            ->leftJoin('registrations as r', 'tv.registration_id', '=', 'r.id')
            ->leftJoin('verifiers as v', 'tv.verifier_id', '=', 'v.id')
            ->select([
                'tv.*',
                'r.fullname',
                'r.email', 
                'r.organization',
                'r.phone',
                'v.name as verifier_name'
            ])
            ->where('tv.event_id', $eventId)
            ->where('tv.verifier_id', $verifierId) // Filtrer par vérificateur
            ->orderBy('tv.verified_at', 'desc')
            ->limit(50) // Limiter à 50 dernières vérifications
            ->get();
    }

    private function getPersonalVerificationStats($eventId, $verifierId)
    {
        $baseQuery = DB::connection('tenant')
            ->table('ticket_verifications')
            ->where('event_id', $eventId)
            ->where('verifier_id', $verifierId);

        return (object) [
            'total_verifications' => $baseQuery->count(),
            'successful_verifications' => $baseQuery->where('status', 'success')->count(),
            'already_used_attempts' => $baseQuery->where('status', 'already_used')->count(),
            'failed_verifications' => $baseQuery->whereIn('status', [
                'not_paid', 'not_confirmed', 'registration_not_found', 
                'invalid_hash', 'zone_access_denied'
            ])->count(),
            'time_restricted_attempts' => $baseQuery->whereIn('status', [
                'event_not_active', 'access_time_restricted', 
                'access_not_started', 'access_ended'
            ])->count(),
            'zones_verified' => $baseQuery->distinct('access_zone')->count('access_zone'),
            'first_verification' => $baseQuery->min('verified_at'),
            'last_verification' => $baseQuery->max('verified_at'),
            // Statistiques par zone pour ce vérificateur
            'zone_stats' => $this->getPersonalZoneStats($eventId, $verifierId)
        ];
    }

    private function getPersonalZoneStats($eventId, $verifierId)
    {
        return DB::connection('tenant')
            ->table('ticket_verifications')
            ->select([
                'access_zone',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful_entries'),
                DB::raw('SUM(CASE WHEN status = "already_used" THEN 1 ELSE 0 END) as duplicate_attempts'),
                DB::raw('MAX(verified_at) as last_verification')
            ])
            ->where('event_id', $eventId)
            ->where('verifier_id', $verifierId)
            ->groupBy('access_zone')
            ->orderBy('total_attempts', 'desc')
            ->get()
            ->keyBy('access_zone');
    }

    public function getRealtimeStats(Request $request)
    {
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        if (!$this->isVerifierAuthenticated()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        try {
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->where('event_slug', $eventSlug)
                       ->first();
            
            if (!$event) {
                return response()->json(['error' => 'Événement non trouvé'], 404);
            }

            $stats = $this->getVerificationStats($event->id);
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur stats temps réel', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function searchRegistration(Request $request)
    {
        if (!$this->isVerifierAuthenticated()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $orgSlug = $request->route('org_slug');
            $eventSlug = $request->route('event_slug');
            $query = $request->input('query');

            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->where('event_slug', $eventSlug)
                       ->first();

            if (!$event) {
                return response()->json(['error' => 'Événement non trouvé'], 404);
            }

            $registrations = DB::connection('tenant')
                              ->table('registrations')
                              ->where('event_id', $event->id)
                              ->where(function($q) use ($query) {
                                  $q->where('fullname', 'LIKE', "%{$query}%")
                                    ->orWhere('email', 'LIKE', "%{$query}%")
                                    ->orWhere('phone', 'LIKE', "%{$query}%")
                                    ->orWhere('registration_number', 'LIKE', "%{$query}%")
                                    ->orWhere('organization', 'LIKE', "%{$query}%");
                              })
                              ->limit(20)
                              ->get()
                              ->map(function($registration) {
                                  return $this->enrichRegistrationData($registration);
                              });

            return response()->json([
                'success' => true,
                'registrations' => $registrations
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur recherche inscription', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function exportVerifications(Request $request)
    {
        if (!$this->isVerifierAuthenticated()) {
            return redirect()->route('event.verifier.auth', [
                'org_slug' => $request->route('org_slug'),
                'event_slug' => $request->route('event_slug')
            ]);
        }

        try {
            $orgSlug = $request->route('org_slug');
            $eventSlug = $request->route('event_slug');

            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->where('event_slug', $eventSlug)
                       ->first();

            if (!$event) {
                return back()->with('error', 'Événement non trouvé.');
            }

            $verifications = $this->getDetailedVerifications($event->id, 1000);

            $filename = "verifications_{$event->event_slug}_" . date('Y-m-d_H-i-s') . ".csv";
            
            $headers = [
                'Date/Heure',
                'Nom du participant',
                'Email',
                'Organisation', 
                'Numéro d\'inscription',
                'Zone d\'accès',
                'Statut',
                'Vérificateur',
                'Adresse IP'
            ];

            $callback = function() use ($headers, $verifications) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, $headers, ';');
                
                foreach ($verifications as $verification) {
                    $row = [
                        Carbon::parse($verification->verified_at)->format('d/m/Y H:i:s'),
                        $verification->fullname ?? 'N/A',
                        $verification->email ?? 'N/A',
                        $verification->organization ?? 'N/A',
                        $verification->registration_number ?? $verification->ticket_hash,
                        $this->getEventName($verification->access_zone),
                        $verification->status === 'success' ? 'Succès' : 'Échec',
                        $verification->verifier_name ?? 'N/A',
                        $verification->ip_address ?? 'N/A'
                    ];
                    fputcsv($file, $row, ';');
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur export vérifications', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erreur lors de l\'export.');
        }
    }

    public function getAvailableZones(Request $request)
    {
        if (!$this->isVerifierAuthenticated()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        try {
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->where('event_slug', $eventSlug)
                       ->first();
            
            $zones = $this->getEventAccessZones($event->id);
            $allowedZones = $this->getVerifierAllowedZones(session('verifier_id'));
            
            return response()->json([
                'success' => true,
                'zones' => $zones,
                'allowed_zones' => $allowedZones,
                'current_time' => now()->format('H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function checkZoneAccess(Request $request, $zone)
    {
        if (!$this->isVerifierAuthenticated()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        
        try {
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->where('event_slug', $eventSlug)
                       ->first();
            
            $accessCheck = $this->checkZoneAccessTime($event, $zone);
            
            return response()->json([
                'success' => true,
                'zone' => $zone,
                'access_allowed' => $accessCheck['allowed'],
                'message' => $accessCheck['message'],
                'current_time' => now()->format('H:i:s'),
                'starts_at' => $accessCheck['starts_at'] ?? null,
                'ends_at' => $accessCheck['ends_at'] ?? null
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function keepAlive(Request $request)
    {
        session(['verifier_last_activity' => now()]);
        
        return response()->json([
            'success' => true,
            'timestamp' => now()->toISOString(),
            'session_valid' => $this->isVerifierAuthenticated()
        ]);
    }

    public function publicTicketStatus(Request $request, $orgSlug, $eventSlug, $ticketHash)
    {
        try {
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->where('event_slug', $eventSlug)
                       ->first();
            
            $inscription = $this->findRegistration($ticketHash, $event->id);
            
            if (!$inscription) {
                return response()->json(['error' => 'Ticket non trouvé'], 404);
            }
            
            return response()->json([
                'valid' => $inscription->status === 'confirmed' && $inscription->payment_status === 'paid',
                'status' => $inscription->status,
                'payment_status' => $inscription->payment_status,
                'participant_name' => $inscription->fullname,
                'event_title' => $event->event_title,
                'event_date' => $event->event_date
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    // =====================================================
    // MÉTHODES PRIVÉES UTILITAIRES
    // =====================================================

    /**
     * 🔧 MÉTHODE UTILITAIRE: Cache et retourne le résultat
     */
    private function cacheAndReturnResult($cacheKey, $status, $message, $inscription = null, $event = null, $organization = null, $previousUsage = null)
    {
        $result = [
            'status' => $status,
            'message' => $message,
            'inscription' => $inscription,
            'event' => $event,
            'organization' => $organization,
            'previous_usage' => $previousUsage
        ];
        
        // Mettre en cache le résultat final (5 minutes)
        cache()->put($cacheKey, $result, now()->addMinutes(5));
        
        return $this->renderVerificationResult($status, $message, $inscription, $event, $organization, $previousUsage);
    }

    private function getPreviousUsageDetails($registrationId, $eventType)
    {
        try {
            return \DB::connection('tenant')
                    ->table('ticket_verifications')
                    ->leftJoin('verifiers', 'ticket_verifications.verifier_id', '=', 'verifiers.id')
                    ->where('ticket_verifications.registration_id', $registrationId)
                    ->where('ticket_verifications.access_zone', $eventType)
                    ->where('ticket_verifications.status', 'success')
                    ->select(
                        'ticket_verifications.verified_at',
                        'ticket_verifications.ip_address',
                        'verifiers.name as verifier_name'
                    )
                    ->orderBy('ticket_verifications.verified_at', 'desc')
                    ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function configureTenantConnection($organization)
    {
        $tenantConfig = [
            'driver' => 'mysql',
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => $organization->database_name,
            'username' => $organization->database_name,
            'password' => env('TENANT_DB_PASSWORD', 'Une@Vie@2route'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ];
        
        config(['database.connections.tenant' => $tenantConfig]);
        \DB::purge('tenant');
    }

    private function isVerifierAuthenticated()
    {
        return session()->has('verifier_authenticated') && 
               session('verifier_id') && 
               session('verifier_last_activity') &&
               now()->diffInMinutes(session('verifier_last_activity')) < 480; // 8 heures
    }

    private function canAccessZone($zone)
    {
        $verifierRole = session('verifier_role');
        $allowedZones = session('verifier_allowed_zones', []);
        
        if ($verifierRole === 'admin') {
            return true;
        }
        
        if ($verifierRole === 'verifier') {
            return in_array($zone, $allowedZones) || empty($allowedZones);
        }
        
        if ($verifierRole === 'zone_specific') {
            return in_array($zone, $allowedZones);
        }
        
        return false;
    }

    private function checkZoneAccessTime($event, $eventType)
    {
        try {
            // Chercher les contrôles d'accès pour cette zone
            $accessControl = \DB::connection('tenant')
                              ->table('event_access_controls')
                              ->where('event_id', $event->id)
                              ->where('access_zone', $eventType)
                              ->where('is_active', true)
                              ->first();

            if (!$accessControl) {
                // Pas de contrôle spécifique, accès autorisé
                return ['allowed' => true, 'message' => ''];
            }

            $now = now();
            $currentTime = $now->format('H:i:s');

            if ($accessControl->access_date) {
                $accessDate = Carbon::parse($accessControl->access_date)->format('Y-m-d');
                $todayDate = $now->format('Y-m-d');
                
                if ($accessDate !== $todayDate) {
                    $accessDateFormatted = Carbon::parse($accessControl->access_date)
                    ->locale('fr')
                    ->isoFormat('D MMMM YYYY');
                    
                    return [
                        'allowed' => false,
                        'message' => "La vérification débute le {$accessDateFormatted}"
                    ];
                }
            }

            if ($accessControl->access_start_time && $accessControl->access_end_time) {
                $startTime = $accessControl->access_start_time;
                $endTime = $accessControl->access_end_time;
                
                if ($currentTime < $startTime) {
                    return [
                        'allowed' => false,
                        'message' => "La verification débute à {$startTime}"
                    ];
                }
                
                if ($currentTime > $endTime) {
                    return [
                        'allowed' => false,
                        'message' => "L'accès à l'évènement est terminé."
                    ];
                }
            }

            return ['allowed' => true, 'message' => ''];
            
        } catch (\Exception $e) {
            // En cas d'erreur, autoriser l'accès par défaut
            \Log::warning('Erreur vérification horaire accès', [
                'error' => $e->getMessage(),
                'event_type' => $eventType
            ]);
            
            return ['allowed' => true, 'message' => ''];
        }
    }

    private function findRegistration($uniqueId, $eventId)
    {
        // D'abord chercher dans ticket_hash_mappings
        $ticketMapping = \DB::connection('tenant')
                        ->table('ticket_hash_mappings')
                        ->where('ticket_hash', $uniqueId)
                        ->first();

        if ($ticketMapping) {
            return \DB::connection('tenant')
                    ->table('registrations')
                    ->where('id', $ticketMapping->registration_id)
                    ->where('event_id', $eventId)
                    ->first();
        }

        // Sinon chercher directement par registration_number ou id
        return \DB::connection('tenant')
                ->table('registrations')
                ->where(function($query) use ($uniqueId) {
                    $query->where('registration_number', $uniqueId)
                        ->orWhere('id', $uniqueId);
                })
                ->where('event_id', $eventId)
                ->first();
    }

    private function getEventAccessZones($eventId)
    {
        try {
            $zones = DB::connection('tenant')
                      ->table('event_access_controls')
                      ->where('event_id', $eventId)
                      ->where('is_active', true)
                      ->orderBy('access_zone')
                      ->get();

            $zonesList = [];
            foreach ($zones as $zone) {
                $zonesList[$zone->access_zone] = $zone->zone_name;
            }

            if (empty($zonesList)) {
                $zonesList = [
                    'gala' => 'Gala de Clôture'
                ];
            }

            return $zonesList;
        } catch (\Exception $e) {
            return [
                'gala' => 'Gala de Clôture'
            ];
        }
    }

    private function getVerifierAllowedZones($verifierId)
    {
        try {
            $verifier = DB::connection('tenant')
                         ->table('verifiers')
                         ->where('id', $verifierId)
                         ->first();

            if (!$verifier) {
                return [];
            }

            if ($verifier->role === 'admin') {
                return $this->getEventAccessZones(session('verifier_event_id'));
            }

            $allowedZones = $verifier->allowed_zones ? explode(',', $verifier->allowed_zones) : [];
            $eventZones = $this->getEventAccessZones(session('verifier_event_id'));
            
            $result = [];
            foreach ($allowedZones as $zone) {
                if (isset($eventZones[$zone])) {
                    $result[$zone] = $eventZones[$zone];
                }
            }

            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getVerificationStats($eventId)
    {
        try {
            $tableExists = DB::connection('tenant')
                            ->select("SHOW TABLES LIKE 'ticket_verifications'");
            
            if (empty($tableExists)) {
                return [
                    'total_registrations' => 0,
                    'total_verifications' => 0,
                    'successful_verifications' => 0,
                    'failed_verifications' => 0,
                    'zones_stats' => [],
                    'recent_verifications' => 0
                ];
            }

            $totalRegistrations = DB::connection('tenant')
                                   ->table('registrations')
                                   ->where('event_id', $eventId)
                                   ->where('status', 'confirmed')
                                   ->count();

            $totalVerifications = DB::connection('tenant')
                                   ->table('ticket_verifications')
                                   ->where('event_id', $eventId)
                                   ->count();

            $successfulVerifications = DB::connection('tenant')
                                        ->table('ticket_verifications')
                                        ->where('event_id', $eventId)
                                        ->where('status', 'success')
                                        ->count();

            $failedVerifications = $totalVerifications - $successfulVerifications;

            return [
                'total_registrations' => $totalRegistrations,
                'total_verifications' => $totalVerifications,
                'successful_verifications' => $successfulVerifications,
                'failed_verifications' => $failedVerifications,
                'zones_stats' => [],
                'recent_verifications' => 0,
                'verification_rate' => $totalRegistrations > 0 ? round(($successfulVerifications / $totalRegistrations) * 100, 1) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total_registrations' => 0,
                'total_verifications' => 0,
                'successful_verifications' => 0,
                'failed_verifications' => 0,
                'zones_stats' => [],
                'recent_verifications' => 0,
                'verification_rate' => 0
            ];
        }
    }

    private function getDetailedVerifications($eventId, $limit = 50)
    {
        try {
            $tableExists = DB::connection('tenant')
                            ->select("SHOW TABLES LIKE 'ticket_verifications'");
            
            if (empty($tableExists)) {
                return collect();
            }

            return DB::connection('tenant')
                    ->table('ticket_verifications')
                    ->leftJoin('registrations', 'ticket_verifications.registration_id', '=', 'registrations.id')
                    ->leftJoin('verifiers', 'ticket_verifications.verifier_id', '=', 'verifiers.id')
                    ->where('ticket_verifications.event_id', $eventId)
                    ->select(
                        'ticket_verifications.*',
                        'registrations.fullname',
                        'registrations.email',
                        'registrations.organization',
                        'registrations.registration_number',
                        'verifiers.name as verifier_name'
                    )
                    ->orderBy('ticket_verifications.verified_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function($verification) {
                        if ($verification->fullname === null && isset($verification->form_data)) {
                            $formData = json_decode($verification->form_data, true);
                            $verification->fullname = $formData['full_name'] ?? $formData['fullname'] ?? 'Participant';
                            $verification->email = $formData['email'] ?? null;
                            $verification->organization = $formData['organization'] ?? null;
                        }
                        return $verification;
                    });
                    
        } catch (\Exception $e) {
            return collect();
        }
    }

    /* private function logVerification($registrationId, $uniqueId, $eventType, $status, $ipAddress, $eventId)
    {
        try {
            $tableExists = \DB::connection('tenant')
                            ->select("SHOW TABLES LIKE 'ticket_verifications'");
            
            if (empty($tableExists)) {
                \DB::connection('tenant')->statement("
                    CREATE TABLE ticket_verifications (
                        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        event_id bigint(20) UNSIGNED NOT NULL,
                        registration_id bigint(20) UNSIGNED DEFAULT NULL,
                        ticket_hash varchar(255) NOT NULL,
                        access_zone varchar(100) NOT NULL,
                        status enum('success','already_used','not_paid','not_confirmed','invalid_hash','registration_not_found','zone_access_denied','event_not_active','access_time_restricted') NOT NULL,
                        ip_address varchar(45) DEFAULT NULL,
                        verifier_id bigint(20) UNSIGNED DEFAULT NULL,
                        verified_at timestamp NOT NULL DEFAULT current_timestamp(),
                        created_at timestamp NOT NULL DEFAULT current_timestamp(),
                        PRIMARY KEY (id),
                        KEY idx_event_id (event_id),
                        KEY idx_status (status),
                        KEY idx_verified_at (verified_at)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }

            \DB::connection('tenant')->table('ticket_verifications')->insert([
                'event_id' => $eventId,
                'registration_id' => $registrationId,
                'verifier_id' => session('verifier_id'),
                'ticket_hash' => $uniqueId,
                'access_zone' => $eventType,
                'status' => $status,
                'ip_address' => $ipAddress,
                'verified_at' => now(),
                'created_at' => now()
            ]);

            session(['verifier_last_activity' => now()]);

        } catch (\Exception $e) {
            \Log::error('Erreur log vérification', ['error' => $e->getMessage()]);
        }
    } */

    private function logVerification($registrationId, $uniqueId, $eventType, $status, $ipAddress, $eventId)
{
    \Log::info('=== DÉBUT logVerification ===', [
        'registration_id' => $registrationId,
        'unique_id' => $uniqueId,
        'event_type' => $eventType,
        'status' => $status,
        'ip_address' => $ipAddress,
        'event_id' => $eventId,
        'verifier_id' => session('verifier_id'),
        'timestamp' => now()->toDateTimeString()
    ]);

    try {
        // Log avant vérification de la table
        \Log::info('Vérification existence table ticket_verifications');
        
        $tableExists = \DB::connection('tenant')
                        ->select("SHOW TABLES LIKE 'ticket_verifications'");
        
        \Log::info('Résultat vérification table', [
            'table_exists' => !empty($tableExists),
            'result' => $tableExists
        ]);
        
        if (empty($tableExists)) {
            \Log::warning('Table ticket_verifications inexistante - Création en cours');
            
            \DB::connection('tenant')->statement("
                CREATE TABLE ticket_verifications (
                    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    event_id bigint(20) UNSIGNED NOT NULL,
                    registration_id bigint(20) UNSIGNED DEFAULT NULL,
                    ticket_hash varchar(255) NOT NULL,
                    access_zone varchar(100) NOT NULL,
                    status enum('success','already_used','not_paid','not_confirmed','invalid_hash','registration_not_found','zone_access_denied','event_not_active','access_time_restricted') NOT NULL,
                    ip_address varchar(45) DEFAULT NULL,
                    verifier_id bigint(20) UNSIGNED DEFAULT NULL,
                    verified_at timestamp NOT NULL DEFAULT current_timestamp(),
                    created_at timestamp NOT NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (id),
                    KEY idx_event_id (event_id),
                    KEY idx_status (status),
                    KEY idx_verified_at (verified_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            \Log::info('✓ Table ticket_verifications créée avec succès');
        }

        // Préparation des données d'insertion
        $insertData = [
            'event_id' => $eventId,
            'registration_id' => $registrationId,
            'verifier_id' => session('verifier_id'),
            'ticket_hash' => $uniqueId,
            'access_zone' => $eventType,
            'status' => $status,
            'ip_address' => $ipAddress,
            'verified_at' => now(),
            'created_at' => now()
        ];

        \Log::info('Préparation insertion vérification', [
            'data' => $insertData
        ]);

        // Insertion dans la base de données
        \Log::info('Début insertion dans ticket_verifications');
        
        $inserted = \DB::connection('tenant')
            ->table('ticket_verifications')
            ->insert($insertData);

        if ($inserted) {
            \Log::info('✓ Vérification loguée avec SUCCÈS', [
                'registration_id' => $registrationId,
                'ticket_hash' => $uniqueId,
                'status' => $status,
                'insert_result' => $inserted
            ]);

            // Vérification post-insertion
            $verifyInsert = \DB::connection('tenant')
                ->table('ticket_verifications')
                ->where('ticket_hash', $uniqueId)
                ->where('event_id', $eventId)
                ->orderBy('id', 'desc')
                ->first();

            if ($verifyInsert) {
                \Log::info('✓ Vérification post-insertion réussie', [
                    'verification_id' => $verifyInsert->id ?? null,
                    'ticket_hash' => $uniqueId
                ]);
            } else {
                \Log::error('✗ Vérification post-insertion ÉCHOUÉE - Record non trouvé', [
                    'ticket_hash' => $uniqueId,
                    'event_id' => $eventId
                ]);
            }
        } else {
            \Log::error('✗ Insertion retournée FALSE', [
                'ticket_hash' => $uniqueId,
                'insert_result' => $inserted
            ]);
        }

        // Mise à jour de la session
        \Log::info('Mise à jour session verifier_last_activity', [
            'old_activity' => session('verifier_last_activity'),
            'new_activity' => now()->toDateTimeString()
        ]);
        
        session(['verifier_last_activity' => now()]);

        \Log::info('=== FIN logVerification (succès) ===', [
            'registration_id' => $registrationId,
            'status' => $status
        ]);

    } catch (\Exception $e) {
        \Log::error('✗✗✗ EXCEPTION dans logVerification ✗✗✗', [
            'registration_id' => $registrationId,
            'unique_id' => $uniqueId,
            'event_type' => $eventType,
            'status' => $status,
            'event_id' => $eventId,
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        \Log::info('=== FIN logVerification (erreur) ===', [
            'error' => $e->getMessage()
        ]);
    }
}

    private function enrichRegistrationData($inscription)
    {
        if (!$inscription->form_data) {
            return $inscription;
        }

        try {
            $formData = json_decode($inscription->form_data, true);
            
            if (!$inscription->fullname && isset($formData['full_name'])) {
                $inscription->fullname = $formData['full_name'];
            }
            
            if (!$inscription->email && isset($formData['email'])) {
                $inscription->email = $formData['email'];
            }
            
            if (!$inscription->phone && isset($formData['phone'])) {
                $inscription->phone = $formData['phone'];
            }
            
            if (!$inscription->organization && isset($formData['organization'])) {
                $inscription->organization = $formData['organization'];
            }

        } catch (\Exception $e) {
            \Log::error('Erreur enrichissement données inscription', ['error' => $e->getMessage()]);
        }

        return $inscription;
    }

    private function extractTicketHashFromQR($qrData)
    {
        if (preg_match('/\/verify\/([a-f0-9]+)/', $qrData, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/\/verify\/([a-f0-9]+)\?/', $qrData, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/^[a-f0-9]{32}$/', $qrData) || preg_match('/^[A-Z0-9\-]+$/', $qrData)) {
            return $qrData;
        }
        
        return null;
    }

    private function renderVerificationResult($status, $message, $inscription = null, $event = null, $organization = null, $previousUsage = null)
    {
        if ($event && (!isset($event->primary_color) || !isset($event->secondary_color))) {
            Log::warning('Événement sans couleurs, rechargement depuis la base', [
                'event_id' => $event->id ?? 'null',
                'has_primary_color' => isset($event->primary_color),
                'has_secondary_color' => isset($event->secondary_color)
            ]);
            
            try {
                $fullEvent = DB::connection('tenant')
                              ->table('events')
                              ->select('*')
                              ->where('id', $event->id)
                              ->first();
                
                if ($fullEvent) {
                    $event = $fullEvent;
                    Log::info('Événement rechargé avec couleurs', [
                        'event_id' => $event->id,
                        'primary_color' => $event->primary_color,
                        'secondary_color' => $event->secondary_color
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Erreur rechargement événement', ['error' => $e->getMessage()]);
            }
        }
        
        if ($inscription && !$inscription->fullname) {
            $inscription = $this->enrichRegistrationData($inscription);
        }

        $alertData = [
            'status' => $status,
            'message' => $message,
            'user' => $inscription->fullname ?? null
        ];

        if ($status === 'already_used' && $previousUsage) {
            $alertData['previous_usage'] = [
                'verified_at' => Carbon::parse($previousUsage->verified_at)->format('d/m/Y à H:i:s'),
                'verifier_name' => $previousUsage->verifier_name ?? 'Vérificateur inconnu',
                'ip_address' => $previousUsage->ip_address
            ];
        }

        return view('verifier.scan', [
            'alert' => $alertData,
            'inscription' => $inscription,
            'event' => $event,
            'organization' => $organization,
            'org_slug' => request()->route('org_slug'),
            'event_slug' => request()->route('event_slug'),
            'verifier' => [
                'name' => session('verifier_name'),
                'role' => session('verifier_role')
            ]
        ]);
    }

    private function renderAuthView($orgSlug, $eventSlug, $organization = null, $event = null, $error = null)
    {
        $organizationObj = $organization ? (object) [
            'id' => $organization->id,
            'org_name' => $organization->org_name,
            'org_key' => $organization->org_key
        ] : (object) [
            'id' => null,
            'org_name' => 'Organisation',
            'org_key' => $orgSlug
        ];
        
        $eventObj = $event ? (object) [
            'id' => $event->id,
            'event_title' => $event->event_title,
            'event_date' => $event->event_date,
            'event_location' => $event->event_location ?? '',
            'primary_color' => $event->primary_color ?? '#174e4b',
            'secondary_color' => $event->secondary_color ?? '#2d6a65'
        ] : (object) [
            'id' => null,
            'event_title' => 'Événement',
            'event_date' => null,
            'event_location' => '',
            'primary_color' => '#174e4b',
            'secondary_color' => '#2d6a65'
        ];
        
        $viewData = [
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug,
            'event' => $eventObj,
            'organization' => $organizationObj
        ];
        
        if ($error) {
            $viewData['error'] = $error;
        }
        
        return view('verifier.auth', $viewData);
    }

    public function verifyTicketWithZone(Request $request)
    {
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        $ticketHash = $request->route('ticket_hash');
        
        $eventType = $request->query('zone') ?: 
                    $request->query('event') ?: 
                    $request->input('event_type') ?: 
                    session('current_zone') ?: 
                    session('selected_zone') ?: 
                    'gala'; 

        $uniqueId = $request->query('data', $ticketHash);

        session(['current_zone' => $eventType, 'selected_zone' => $eventType]);
        $cacheKey = "verify_{$uniqueId}_{$eventType}_" . date('Y-m-d-H-i');
        if (cache()->has($cacheKey)) {
            $cachedResult = cache()->get($cacheKey);
            return $this->renderVerificationResult(
                $cachedResult['status'], 
                $cachedResult['message'], 
                $cachedResult['inscription'] ?? null, 
                $cachedResult['event'] ?? null, 
                $cachedResult['organization'] ?? null,
                $cachedResult['previous_usage'] ?? null
            );
        }

        cache()->put($cacheKey, ['status' => 'processing'], now()->addMinutes(5));

        try {
            $organization = \DB::table('organizations')->where('org_key', $orgSlug)->first();
            if (!$organization) {
                return $this->cacheAndReturnResult($cacheKey, 'error', 'Organisation non trouvée.', null, null, $organization);
            }
            
            $this->configureTenantConnection($organization);
            
            $event = \DB::connection('tenant')
                    ->table('events')
                    ->select('*')
                    ->where('event_slug', $eventSlug)
                    ->first();

            if (!$event) {
                return $this->cacheAndReturnResult($cacheKey, 'error', 'Événement non trouvé.', null, $event, $organization);
            }

            $availableZones = $this->getEventAccessZones($event->id);
            if (!array_key_exists($eventType, $availableZones)) {
                $eventType = !empty($availableZones) ? array_key_first($availableZones) : 'gala';
                session(['current_zone' => $eventType, 'selected_zone' => $eventType]);
            }

            $accessCheck = $this->checkZoneAccessTime($event, $eventType);
            if (!$accessCheck['allowed']) {
                $this->logVerification(null, $uniqueId, $eventType, 'access_time_restricted', $request->ip(), $event->id);
                return $this->cacheAndReturnResult($cacheKey, 'error', $accessCheck['message'], null, $event, $organization);
            }

            if (session()->has('verifier_authenticated') && !$this->canAccessZone($eventType)) {
                $this->logVerification(null, $uniqueId, $eventType, 'zone_access_denied', $request->ip(), $event->id);
                return $this->cacheAndReturnResult($cacheKey, 'error', 'Vous n\'avez pas accès à cette zone d\'événement: ' . $this->getEventName($eventType), null, $event, $organization);
            }

            $inscription = $this->findRegistration($uniqueId, $event->id);
            if (!$inscription) {
                $this->logVerification(null, $uniqueId, $eventType, 'registration_not_found', $request->ip(), $event->id);
                return $this->cacheAndReturnResult($cacheKey, 'error', 'Inscription non trouvée.', null, $event, $organization);
            }

            $inscription = $this->enrichRegistrationData($inscription);
            $usageCheck = $this->checkTicketUsage($inscription, $eventType);
            if ($usageCheck['already_used']) {
                $this->logVerification($inscription->id, $uniqueId, $eventType, 'already_used', $request->ip(), $event->id);
                $previousUsage = $this->getPreviousUsageDetails($inscription->id, $eventType);
                return $this->cacheAndReturnResult($cacheKey, 'already_used', 
                    'Ce ticket a déjà été utilisé pour ' . $this->getEventName($eventType) . '.',
                    $inscription, $event, $organization, $previousUsage);
            }

            if ($inscription->payment_status !== 'paid') {
                $this->logVerification($inscription->id, $uniqueId, $eventType, 'not_paid', $request->ip(), $event->id);
                return $this->cacheAndReturnResult($cacheKey, 'error', 'Le ticket n\'est pas payé.', $inscription, $event, $organization);
            }
            
            if ($inscription->status !== 'confirmed') {
                $this->logVerification($inscription->id, $uniqueId, $eventType, 'not_confirmed', $request->ip(), $event->id);
                return $this->cacheAndReturnResult($cacheKey, 'error', 'L\'inscription n\'est pas confirmée.', $inscription, $event, $organization);
            }

            $this->markTicketAsUsedWithStatus($inscription, $eventType);
            $this->logVerification($inscription->id, $uniqueId, $eventType, 'success', $request->ip(), $event->id);

            return $this->cacheAndReturnResult($cacheKey, 'success', 
                'Accès autorisé pour ' . $this->getEventName($eventType) . '.', 
                $inscription, $event, $organization);
        } catch (\Exception $e) {
            cache()->forget($cacheKey);
            return $this->renderVerificationResult('error', 'Erreur lors de la vérification.', null, null, null);
        }
    }

    public function processScan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string',
            'event_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        $qrData = $request->qr_data;
        $eventType = $request->event_type;

        session(['current_zone' => $eventType, 'selected_zone' => $eventType]);

        $ticketHash = $this->extractTicketHashFromQR($qrData);
        
        if (!$ticketHash) {
            return back()->withErrors(['qr_data' => 'Format de QR code invalide.']);
        }
        
        return redirect()->route('event.verifier.verify-zone', [
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug,
            'ticket_hash' => $ticketHash
        ])->with([
            'event_type' => $eventType,
            'scan_source' => 'qr'
        ])->withInput(['zone' => $eventType]);
    }

    public function verifyManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string',
            'event_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');
        $registrationNumber = trim($request->registration_number);
        $eventType = $request->event_type;
        session(['current_zone' => $eventType, 'selected_zone' => $eventType]);

        return redirect()->route('event.verifier.verify-zone', [
            'org_slug' => $orgSlug,
            'event_slug' => $eventSlug,
            'ticket_hash' => $registrationNumber
        ] + ['zone' => $eventType])->with([
            'event_type' => $eventType,
            'scan_source' => 'manual'
        ]);
    }

    public function setActiveZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Zone invalide'], 422);
        }

        $zone = $request->input('zone');
        $orgSlug = $request->route('org_slug');
        $eventSlug = $request->route('event_slug');

        try {
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            $this->configureTenantConnection($organization);
            
            $event = DB::connection('tenant')
                       ->table('events')
                       ->where('event_slug', $eventSlug)
                       ->first();

            $availableZones = $this->getEventAccessZones($event->id);
            
            if (!array_key_exists($zone, $availableZones)) {
                return response()->json(['error' => 'Zone non disponible'], 400);
            }

            if (!$this->canAccessZone($zone)) {
                return response()->json(['error' => 'Accès non autorisé à cette zone'], 403);
            }

            session(['current_zone' => $zone, 'selected_zone' => $zone]);

            return response()->json([
                'success' => true,
                'zone' => $zone,
                'zone_name' => $availableZones[$zone],
                'message' => 'Zone active: ' . $availableZones[$zone]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    private function markTicketAsUsedWithStatus($inscription, $eventType)
    {
        $normalizedZone = strtolower($eventType);
        $usedField = 'used_' . $normalizedZone;

        try {
            $columns = \DB::connection('tenant')
                        ->select("SHOW COLUMNS FROM registrations LIKE 'used_%'");
            
            $availableFields = array_column($columns, 'Field');
            
            if (!in_array($usedField, $availableFields)) {
                Log::warning('Champ d\'utilisation non trouvé', [
                    'searched_field' => $usedField,
                    'available_fields' => $availableFields
                ]);
                
                $usedField = 'used_gala'; // Champ par défaut
            }

            \DB::connection('tenant')
            ->table('registrations')
            ->where('id', $inscription->id)
            ->update([
                $usedField => 1,
                'updated_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur marquage ticket utilisé', [
                'error' => $e->getMessage(),
                'registration_id' => $inscription->id,
                'event_type' => $eventType
            ]);
        }

        try {
            $tableExists = \DB::connection('tenant')
                            ->select("SHOW TABLES LIKE 'registration_access_status'");
                            
            if (!empty($tableExists)) {
                \DB::connection('tenant')
                ->table('registration_access_status')
                ->updateOrInsert(
                    [
                        'registration_id' => $inscription->id,
                        'access_zone' => $eventType
                    ],
                    [
                        'status' => 'used',
                        'granted_at' => now(),
                        'used_at' => now(),
                        'verifier_id' => session('verifier_id'),
                        'updated_at' => now()
                    ]
                );
            }
        } catch (\Exception $e) {
            \Log::warning('Erreur mise à jour statut accès', [
                'error' => $e->getMessage(),
                'registration_id' => $inscription->id,
                'event_type' => $eventType
            ]);
        }
    }

    /**
     * 🔧 CORRECTION: Amélioration de checkTicketUsage pour vérifier la zone spécifique
     */
    private function checkTicketUsage($inscription, $eventType)
    {
        $normalizedZone = strtolower($eventType);
        $usedField = 'used_' . $normalizedZone;
        
        // ✅ CORRECTION 13: Vérifier d'abord dans registration_access_status
        try {
            $tableExists = \DB::connection('tenant')
                            ->select("SHOW TABLES LIKE 'registration_access_status'");
                            
            if (!empty($tableExists)) {
                $accessStatus = \DB::connection('tenant')
                               ->table('registration_access_status')
                               ->where('registration_id', $inscription->id)
                               ->where('access_zone', $eventType)
                               ->where('status', 'used')
                               ->first();
                
                if ($accessStatus) {
                    return ['already_used' => true, 'field' => 'registration_access_status'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Erreur vérification statut accès', ['error' => $e->getMessage()]);
        }

        // ✅ Vérification dans la table registrations
        $alreadyUsed = isset($inscription->$usedField) && $inscription->$usedField == 1;
        
        return [
            'already_used' => $alreadyUsed,
            'field' => $usedField
        ];
    }

    /**
     * 🔧 AMÉLIORATION: Noms de zones plus complets
     */
    private function getEventName($eventType)
    {
        $eventNames = [
            'opening' => 'la cérémonie d\'ouverture',
            'conference' => 'la conférence',
            'networking' => 'le cocktail networking', 
            'photos' => 'la séance photos',
            'gala' => 'le gala',
            'ag' => 'l\'assemblée générale',
            'lunch' => 'le déjeuner',
            'dinner' => 'le dîner',
            'vip' => 'la zone VIP',
            'workshop' => 'l\'atelier',
            'exhibition' => 'l\'exposition'
        ];
        
        $normalizedType = strtolower($eventType);
        return $eventNames[$normalizedType] ?? $eventType;
    }

    public function verifyZoneTicket(Request $request, $orgSlug, $eventSlug, $ticketHash)
    {
        try {
            // 1. Récupérer l'organisation
            $organization = DB::table('organizations')->where('org_key', $orgSlug)->first();
            if (!$organization) {
                return redirect()->route('event.registration', [$orgSlug, $eventSlug])
                            ->with('error', 'Organisation non trouvée');
            }

            // 2. Configurer la connexion tenant
            $this->configureTenantConnection($organization);

            // 3. Récupérer l'événement
            $event = DB::connection('tenant')
                    ->table('events')
                    ->select('*')
                    ->where('event_slug', $eventSlug)
                    ->first();

            if (!$event) {
                return redirect()->route('event.registration', [$orgSlug, $eventSlug])
                            ->with('error', 'Événement non trouvé');
            }

            // 4. Récupérer la zone depuis les paramètres
            $zoneName = $request->query('event');
            
            if (!$zoneName) {
                return redirect()->route('event.registration', [$orgSlug, $eventSlug])
                            ->with('error', 'Zone d\'accès non spécifiée');
            }

            $normalizedZoneName = strtolower(trim($zoneName));
            $originalZoneName = trim($zoneName);
            
            // 6. Trouver le contrôle d'accès pour cette zone
            $accessControl = DB::connection('tenant')
                            ->table('event_access_controls')
                            ->where('event_id', $event->id)
                            ->where(function($query) use ($normalizedZoneName, $originalZoneName) {
                                $query->whereRaw('LOWER(access_zone) = ?', [$normalizedZoneName])
                                    ->orWhereRaw('LOWER(zone_name) = ?', [$normalizedZoneName])
                                    ->orWhere('access_zone', $originalZoneName)
                                    ->orWhere('zone_name', $originalZoneName);
                            })
                            ->where('is_active', true)
                            ->first();

            if (!$accessControl) {
                Log::info('Aucun contrôle d\'accès trouvé, création temporaire', [
                    'zone_requested' => $zoneName,
                    'normalized' => $normalizedZoneName,
                    'original' => $originalZoneName
                ]);
                
                $accessControl = (object) [
                    'id' => 0,
                    'access_zone' => $originalZoneName,
                    'zone_name' => ucfirst($originalZoneName),
                    'zone_description' => null,
                    'access_start_time' => null,
                    'access_end_time' => null,
                    'access_date' => null
                ];
            }

            // 7. Rechercher l'inscription
            $registration = $this->findRegistrationByMultipleMethods($ticketHash, $event, $accessControl);

            if (!$registration) {
                Log::warning('Inscription non trouvée', [
                    'ticket_hash' => $ticketHash,
                    'zone' => $normalizedZoneName,
                    'event_id' => $event->id
                ]);
                
                $this->logVerification(null, $ticketHash, $normalizedZoneName, 'invalid_hash', $request->ip(), $event->id);
                
                // 🎯 POUR LES REQUÊTES WEB : Afficher une vue avec modal d'erreur
                return view('verifier.scan', [
                    'alert' => [
                        'status' => 'error',
                        'message' => 'Ticket invalide ou non trouvé'
                    ],
                    'inscription' => null,
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone,
                    'event' => $event,
                    'organization' => $organization,
                    'participant' => null,
                    'org_slug' => $orgSlug,
                    'event_slug' => $eventSlug
                ]);
            }

            Log::info('Inscription trouvée', [
                'registration_id' => $registration->id,
                'participant' => $registration->fullname,
                'status' => $registration->status,
                'payment_status' => $registration->payment_status
            ]);

            // 8. Vérifier l'état de l'inscription
            if ($registration->status !== 'confirmed' || $registration->payment_status !== 'paid') {
                $this->logVerification($registration->id, $ticketHash, $normalizedZoneName, 'not_confirmed', $request->ip(), $event->id);

                return view('verifier.scan', [
                    'alert' => [
                        'status' => 'error',
                        'message' => 'Inscription non confirmée ou paiement en attente',
                        'user' => $registration->fullname ?? null
                    ],
                    'inscription' => $registration,
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone,
                    'event' => $event,
                    'organization' => $organization,
                    'participant' => $registration,
                    'org_slug' => $orgSlug,
                    'event_slug' => $eventSlug
                ]);
            }

            // 9. Vérifier si déjà utilisé
            if ($this->isTicketAlreadyUsedForZone($registration->id, $accessControl->access_zone)) {
                $this->logVerification($registration->id, $ticketHash, $normalizedZoneName, 'already_used', $request->ip(), $event->id);

                // Récupérer les détails de l'utilisation précédente
                $previousUsage = $this->getPreviousUsageDetails($registration->id, $accessControl->access_zone);

                return view('verifier.scan', [
                    'alert' => [
                        'status' => 'already_used',
                        'message' => "Ce ticket a déjà été utilisé pour {$accessControl->zone_name}",
                        'user' => $registration->fullname ?? null,
                        'previous_usage' => $previousUsage ? [
                            'verified_at' => $previousUsage->verified_at ? date('d/m/Y H:i:s', strtotime($previousUsage->verified_at)) : 'Date inconnue',
                            'verifier_name' => $previousUsage->verifier_name ?? 'Vérificateur inconnu'
                        ] : null
                    ],
                    'inscription' => $registration,
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone,
                    'event' => $event,
                    'organization' => $organization,
                    'participant' => $registration,
                    'org_slug' => $orgSlug,
                    'event_slug' => $eventSlug
                ]);
            }

            // 10. Vérifier l'accès temporel
            $timeCheck = $this->checkZoneAccessTime($event, $accessControl->access_zone);
            if (!$timeCheck['allowed']) {
                $this->logVerification($registration->id, $ticketHash, $normalizedZoneName, 'access_time_restricted', $request->ip(), $event->id);

                return view('verifier.scan', [
                    'alert' => [
                        'status' => 'warning',
                        'message' => $timeCheck['message'],
                        'user' => $registration->fullname ?? null
                    ],
                    'inscription' => $registration,
                    'zone_name' => $accessControl->zone_name,
                    'zone' => $accessControl->access_zone,
                    'event' => $event,
                    'organization' => $organization,
                    'participant' => $registration,
                    'org_slug' => $orgSlug,
                    'event_slug' => $eventSlug
                ]);
            }

            // 11. SUCCÈS : Marquer comme utilisé
            $this->markTicketAsUsedWithStatus($registration, $accessControl->access_zone);
            $this->logVerification($registration->id, $ticketHash, $normalizedZoneName, 'success', $request->ip(), $event->id);

            // 🎯 SUCCÈS POUR LES REQUÊTES WEB : Afficher vue avec modal de succès
            return view('verifier.scan', [
                'alert' => [
                    'status' => 'success',
                    'message' => "Accès autorisé à {$accessControl->zone_name}",
                    'user' => $registration->fullname ?? null
                ],
                'inscription' => $registration,
                'zone_name' => $accessControl->zone_name,
                'zone' => $accessControl->access_zone,
                'event' => $event,
                'organization' => $organization,
                'participant' => $registration,
                'access_info' => [
                    'time' => now()->format('H:i:s'),
                    'date' => now()->format('d/m/Y'),
                    'zone_verified' => $accessControl->access_zone
                ],
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur vérification zone ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug,
                'ticket_hash' => $ticketHash
            ]);

            return view('verifier.scan', [
                'alert' => [
                    'status' => 'error',
                    'message' => 'Une erreur est survenue lors de la vérification'
                ],
                'inscription' => null,
                'zone_name' => null,
                'zone' => null,
                'event' => null,
                'organization' => null,
                'participant' => null,
                'org_slug' => $orgSlug,
                'event_slug' => $eventSlug
            ]);
        }
    }

    private function findRegistrationByMultipleMethods($ticketHash, $event, $accessControl)
    {
        Log::info('Recherche inscription par plusieurs méthodes', [
            'ticket_hash' => $ticketHash,
            'event_id' => $event->id,
            'access_zone' => $accessControl->access_zone
        ]);

        // Normaliser les noms de zone pour la recherche
        $normalizedZoneName = strtolower(trim($accessControl->access_zone));
        $originalZoneName = trim($accessControl->access_zone);

        // Méthode 1: Recherche dans ticket_hash_mappings (avec les deux casses)
        try {
            $ticketMapping = DB::connection('tenant')
                            ->table('ticket_hash_mappings')
                            ->where('ticket_hash', $ticketHash)
                            ->where(function($query) use ($accessControl, $normalizedZoneName, $originalZoneName) {
                                $query->where('access_zone', $accessControl->access_zone)
                                    ->orWhere('access_zone', $normalizedZoneName)
                                    ->orWhere('access_zone', $originalZoneName);
                            })
                            ->first();

            if ($ticketMapping) {
                Log::info('Trouvé via ticket_hash_mappings', ['mapping_id' => $ticketMapping->id]);
                
                $registration = DB::connection('tenant')
                                ->table('registrations')
                                ->where('id', $ticketMapping->registration_id)
                                ->where('event_id', $event->id)
                                ->first();
                
                if ($registration) {
                    return $registration;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Erreur recherche dans ticket_hash_mappings', ['error' => $e->getMessage()]);
        }

        // Méthode 2: Recherche directe par registration_number ou ID
        try {
            $registration = DB::connection('tenant')
                            ->table('registrations')
                            ->where(function($query) use ($ticketHash) {
                                $query->where('registration_number', $ticketHash)
                                    ->orWhere('id', $ticketHash);
                            })
                            ->where('event_id', $event->id)
                            ->first();

            if ($registration) {
                Log::info('Trouvé via recherche directe', ['registration_id' => $registration->id]);
                return $registration;
            }
        } catch (\Exception $e) {
            Log::warning('Erreur recherche directe', ['error' => $e->getMessage()]);
        }

        // Méthode 3: Recherche par hash SHA256 standard
        try {
            $registrations = DB::connection('tenant')
                            ->table('registrations')
                            ->where('event_id', $event->id)
                            ->where('status', 'confirmed')
                            ->where('payment_status', 'paid')
                            ->get();

            foreach ($registrations as $registration) {
                // Tester plusieurs algorithmes de génération de hash
                $possibleHashes = $this->generatePossibleHashes($registration, $accessControl, $event);
                
                foreach ($possibleHashes as $hashMethod => $calculatedHash) {
                    if (hash_equals($calculatedHash, $ticketHash)) {
                        Log::info('Trouvé via recalcul de hash', [
                            'registration_id' => $registration->id,
                            'hash_method' => $hashMethod
                        ]);
                        return $registration;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Erreur recalcul hash', ['error' => $e->getMessage()]);
        }

        // Méthode 4: Recherche partielle du hash (premiers 32 caractères)
        if (strlen($ticketHash) > 32) {
            $shortHash = substr($ticketHash, 0, 32);
            return $this->findRegistrationByMultipleMethods($shortHash, $event, $accessControl);
        }

        Log::warning('Aucune inscription trouvée avec tous les méthodes', [
            'ticket_hash' => $ticketHash,
            'event_id' => $event->id
        ]);

        return null;
    }

    private function generatePossibleHashes($registration, $accessControl, $event)
    {
        $hashes = [];
        
        // Hash v1: Version originale
        $data1 = [
            'registration_id' => $registration->id,
            'registration_number' => $registration->registration_number,
            'access_zone' => $accessControl->access_zone,
            'access_control_id' => $accessControl->id,
            'event_id' => $event->id,
            'event_slug' => $event->event_slug,
            'participant_phone' => $registration->phone,
            'generated_at' => now()->timestamp,
            'salt' => config('app.key', 'default_salt')
        ];
        $hashes['v1_full'] = hash('sha256', json_encode($data1));

        // Hash v2: Version simplifiée
        $data2 = [
            'registration_id' => $registration->id,
            'access_zone' => $accessControl->access_zone,
            'event_id' => $event->id
        ];
        $hashes['v2_simple'] = hash('sha256', json_encode($data2));

        // Hash v3: Avec registration_number
        $data3 = $registration->registration_number . '_' . $accessControl->access_zone . '_' . $event->id;
        $hashes['v3_concat'] = hash('sha256', $data3);

        // Hash v4: MD5 pour compatibilité
        $hashes['v4_md5'] = md5($registration->registration_number . '_' . $accessControl->access_zone);

        // Hash v5: Sans access_control_id (au cas où il n'existe pas)
        $data5 = [
            'registration_id' => $registration->id,
            'access_zone' => $accessControl->access_zone,
            'event_id' => $event->id,
            'participant_phone' => $registration->phone
        ];
        $hashes['v5_no_control_id'] = hash('sha256', json_encode($data5));

        return $hashes;
    }

    private function findRegistrationByZoneHash($ticketHash, $event, $accessControl)
    {
        $ticketMapping = DB::connection('tenant')
                        ->table('ticket_hash_mappings')
                        ->where('ticket_hash', $ticketHash)
                        ->where('access_zone', $accessControl->access_zone)
                        ->first();

        if ($ticketMapping) {
            return DB::connection('tenant')
                    ->table('registrations')
                    ->where('id', $ticketMapping->registration_id)
                    ->where('event_id', $event->id)
                    ->first();
        }

        // Méthode alternative : recalculer le hash pour toutes les inscriptions
        $registrations = DB::connection('tenant')
                        ->table('registrations')
                        ->where('event_id', $event->id)
                        ->where('status', 'confirmed')
                        ->where('payment_status', 'paid')
                        ->get();

        foreach ($registrations as $registration) {
            // Simuler le même algorithme de génération que dans PaymentController
            $calculatedHash = $this->generateZoneTicketHash($registration, $accessControl, $event);
            if (hash_equals($calculatedHash, $ticketHash)) {
                return $registration;
            }
        }

        return null;
    }

    /**
     * Générer le hash de zone (compatible avec PaymentController)
     */
    private function generateZoneTicketHash($registration, $accessControl, $event)
    {
        $data = [
            'registration_id' => $registration->id,
            'registration_number' => $registration->registration_number,
            'access_zone' => $accessControl->access_zone,
            'access_control_id' => $accessControl->id,
            'event_id' => $event->id,
            'event_slug' => $event->event_slug,
            'participant_phone' => $registration->phone,
            'generated_at' => now()->timestamp,
            'salt' => config('app.key', 'default_salt')
        ];
        
        return hash('sha256', json_encode($data));
    }

    /**
     * Vérifie si le ticket a déjà été utilisé pour cette zone spécifique
     */
    private function isTicketAlreadyUsedForZone($registrationId, $accessZone)
    {
        try {
            // Vérifier dans ticket_verifications
            $verification = DB::connection('tenant')
                            ->table('ticket_verifications')
                            ->where('registration_id', $registrationId)
                            ->where('access_zone', $accessZone)
                            ->where('status', 'success')
                            ->first();

            if ($verification) {
                return true;
            }

            // Vérifier aussi dans registration_access_status si la table existe
            $tableExists = DB::connection('tenant')
                            ->select("SHOW TABLES LIKE 'registration_access_status'");
                            
            if (!empty($tableExists)) {
                $accessStatus = DB::connection('tenant')
                            ->table('registration_access_status')
                            ->where('registration_id', $registrationId)
                            ->where('access_zone', $accessZone)
                            ->where('status', 'used')
                            ->first();
                
                return $accessStatus !== null;
            }

            return false;

        } catch (\Exception $e) {
            Log::warning('Erreur vérification usage ticket pour zone', [
                'error' => $e->getMessage(),
                'registration_id' => $registrationId,
                'access_zone' => $accessZone
            ]);
            return false;
        }
    }

}
