<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Referrer;
use App\Models\ReferrerCommission;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Helpers\TenantHelper;

class ReferrerController extends Controller
{
    /**
     * Afficher la liste des collaborateurs
     */
    public function index(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions : seulement admin, owner, organizer
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return TenantHelper::withTenantConnection(function() use ($user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();
            
            $referrers = Referrer::where('organization_id', $organization->id)
                ->withCount(['registrations', 'events'])
                ->withSum('registrations', 'commission_amount')
                ->orderBy('created_at', 'desc')
                ->get();

            // Statistiques globales
            $stats = [
                'total' => $referrers->count(),
                'active' => $referrers->where('is_active', true)->count(),
                'total_earnings' => Referrer::where('organization_id', $organization->id)
                    ->join('referrer_registrations', 'referrers.id', '=', 'referrer_registrations.referrer_id')
                    ->where('referrer_registrations.commission_status', 'paid')
                    ->sum('referrer_registrations.commission_amount'),
                'pending_earnings' => Referrer::where('organization_id', $organization->id)
                    ->join('referrer_registrations', 'referrers.id', '=', 'referrer_registrations.referrer_id')
                    ->where('referrer_registrations.commission_status', 'pending')
                    ->sum('referrer_registrations.commission_amount'),
            ];

            return view('organization.referrers.index', compact(
                'user',
                'orgSlug',
                'organization',
                'referrers',
                'stats'
            ));
        });
    }

    /**
     * Afficher le formulaire de création de collaborateur
     */
    public function create(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return TenantHelper::withTenantConnection(function() use ($user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();
            
            return view('organization.referrers.create', compact(
                'user',
                'orgSlug',
                'organization'
            ));
        });
    }

    /**
     * Enregistrer un nouveau collaborateur
     */
    public function store(Request $request)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        return TenantHelper::withTenantConnection(function() use ($request, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            DB::beginTransaction();
            try {
                // Générer un mot de passe aléatoire
                $password = Str::random(12);
                $hashedPassword = Hash::make($password);

                // Créer l'utilisateur dans la table users
                $userId = DB::connection('tenant')->table('users')->insertGetId([
                    'email' => $request->email,
                    'password' => $hashedPassword,
                    'nom' => $request->name,
                    'prenoms' => '',
                    'first_name' => explode(' ', $request->name)[0] ?? $request->name,
                    'last_name' => explode(' ', $request->name, 2)[1] ?? '',
                    'phone' => $request->phone,
                    'mobile' => $request->phone,
                    'role' => 'referrer',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Créer le collaborateur
                $referrer = Referrer::create([
                    'organization_id' => $organization->id,
                    'user_id' => $userId,
                    'referrer_code' => Referrer::generateReferrerCode(),
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'notes' => $request->notes,
                    'is_active' => true,
                ]);

                // Envoyer l'email avec les identifiants
                $this->sendCollaboratorCredentialsEmail($referrer, $password, $organization);

                DB::commit();

                Log::info('Collaborateur créé avec succès', [
                    'referrer_id' => $referrer->id,
                    'user_id' => $userId,
                    'referrer_code' => $referrer->referrer_code,
                    'organization_id' => $organization->id
                ]);

                return redirect()->route('org.collaborateurs.index', ['org_slug' => $orgSlug])
                    ->with('success', 'Collaborateur créé avec succès. Les identifiants ont été envoyés par email.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erreur lors de la création du collaborateur', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return back()->withErrors(['error' => 'Erreur lors de la création du collaborateur.'])->withInput();
            }
        });
    }

    /**
     * Afficher les détails d'un collaborateur
     */
    public function show(Request $request, $id)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        return TenantHelper::withTenantConnection(function() use ($id, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            $referrer = Referrer::where('id', $id)
                ->where('organization_id', $organization->id)
                ->with(['commissions.event', 'registrations.registration.event'])
                ->firstOrFail();

            // Statistiques du collaborateur
            $stats = [
                'total_registrations' => $referrer->registrations()->count(),
                'total_earnings' => $referrer->registrations()
                    ->where('commission_status', 'paid')
                    ->sum('commission_amount'),
                'pending_earnings' => $referrer->registrations()
                    ->where('commission_status', 'pending')
                    ->sum('commission_amount'),
                'total_events' => $referrer->events()->count(),
            ];

            // Événements avec commissions
            $eventsWithCommissions = Event::whereHas('referrerCommissions', function($q) use ($referrer) {
                $q->where('referrer_id', $referrer->id);
            })
            ->with(['referrerCommissions' => function($q) use ($referrer) {
                $q->where('referrer_id', $referrer->id);
            }])
            ->get();

            return view('organization.referrers.show', compact(
                'user',
                'orgSlug',
                'organization',
                'referrer',
                'stats',
                'eventsWithCommissions'
            ));
        });
    }

    /**
     * Afficher le formulaire d'attribution de commission pour un événement
     */
    public function assignCommission(Request $request, $eventId)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return TenantHelper::withTenantConnection(function() use ($eventId, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            $event = Event::where('id', $eventId)->firstOrFail();
            $referrers = Referrer::where('organization_id', $organization->id)
                ->where('is_active', true)
                ->get();

            // Commissions existantes pour cet événement
            $existingCommissions = ReferrerCommission::where('event_id', $eventId)
                ->with('referrer')
                ->get()
                ->keyBy('referrer_id');

            return view('organization.referrers.assign-commission', compact(
                'user',
                'orgSlug',
                'organization',
                'event',
                'referrers',
                'existingCommissions'
            ));
        });
    }

    /**
     * Enregistrer les commissions pour un événement
     */
    public function storeCommission(Request $request, $eventId)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        $validator = Validator::make($request->all(), [
            'commissions' => 'required|array',
            'commissions.*.referrer_id' => 'required|exists:referrers,id',
            'commissions.*.commission_type' => 'required|in:percentage,fixed',
            'commissions.*.commission_rate' => 'required_if:commissions.*.commission_type,percentage|nullable|numeric|min:0|max:100',
            'commissions.*.fixed_amount' => 'required_if:commissions.*.commission_type,fixed|nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        return TenantHelper::withTenantConnection(function() use ($request, $eventId, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            DB::beginTransaction();
            try {
                foreach ($request->commissions as $commissionData) {
                    ReferrerCommission::updateOrCreate(
                        [
                            'event_id' => $eventId,
                            'referrer_id' => $commissionData['referrer_id']
                        ],
                        [
                            'commission_type' => $commissionData['commission_type'],
                            'commission_rate' => $commissionData['commission_type'] === 'percentage' 
                                ? $commissionData['commission_rate'] 
                                : null,
                            'fixed_amount' => $commissionData['commission_type'] === 'fixed' 
                                ? $commissionData['fixed_amount'] 
                                : null,
                            'notes' => $commissionData['notes'] ?? null,
                        ]
                    );
                }

                DB::commit();

                return redirect()->route('org.events.show', [
                    'org_slug' => $orgSlug,
                    'event' => $eventId
                ])->with('success', 'Commissions attribuées avec succès.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erreur lors de l\'attribution des commissions', [
                    'error' => $e->getMessage(),
                    'event_id' => $eventId
                ]);

                return back()->withErrors(['error' => 'Erreur lors de l\'attribution des commissions.'])->withInput();
            }
        });
    }

    /**
     * Afficher le formulaire d'édition d'un collaborateur
     */
    public function edit(Request $request, $id)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return TenantHelper::withTenantConnection(function() use ($id, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            $referrer = Referrer::where('id', $id)
                ->where('organization_id', $organization->id)
                ->first();

            if (!$referrer) {
                Log::warning('Collaborateur non trouvé pour édition', [
                    'referrer_id' => $id,
                    'organization_id' => $organization->id ?? null
                ]);
                return redirect()->route('org.collaborateurs.index', ['org_slug' => $orgSlug])
                    ->with('error', 'Collaborateur non trouvé.');
            }

            // Vérifier s'il y a des inscriptions liées
            $hasRegistrations = $referrer->registrations()->count() > 0;

            if ($hasRegistrations) {
                return redirect()->route('org.collaborateurs.show', [
                    'org_slug' => $orgSlug,
                    'id' => $id
                ])->with('error', 'Ce collaborateur ne peut pas être modifié car des inscriptions ont été effectuées via son code.');
            }

            return view('organization.referrers.edit', compact(
                'user',
                'orgSlug',
                'organization',
                'referrer'
            ));
        });
    }

    /**
     * Mettre à jour un collaborateur
     */
    public function update(Request $request, $id)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        return TenantHelper::withTenantConnection(function() use ($request, $id, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            $referrer = Referrer::where('id', $id)
                ->where('organization_id', $organization->id)
                ->firstOrFail();

            // Vérifier s'il y a des inscriptions liées
            $hasRegistrations = $referrer->registrations()->count() > 0;

            if ($hasRegistrations) {
                return redirect()->route('org.collaborateurs.show', [
                    'org_slug' => $orgSlug,
                    'id' => $id
                ])->with('error', 'Ce collaborateur ne peut pas être modifié car des inscriptions ont été effectuées via son code.');
            }

            DB::beginTransaction();
            try {
                // Mettre à jour le collaborateur
                $referrer->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'notes' => $request->notes,
                ]);

                // Mettre à jour l'utilisateur associé si existe
                if ($referrer->user_id) {
                    DB::connection('tenant')->table('users')
                        ->where('id', $referrer->user_id)
                        ->update([
                            'email' => $request->email,
                            'nom' => $request->name,
                            'first_name' => explode(' ', $request->name)[0] ?? $request->name,
                            'last_name' => explode(' ', $request->name, 2)[1] ?? '',
                            'phone' => $request->phone,
                            'mobile' => $request->phone,
                            'updated_at' => now(),
                        ]);
                }

                DB::commit();

                Log::info('Collaborateur mis à jour', [
                    'referrer_id' => $referrer->id,
                    'organization_id' => $organization->id
                ]);

                return redirect()->route('org.collaborateurs.show', [
                    'org_slug' => $orgSlug,
                    'id' => $id
                ])->with('success', 'Collaborateur mis à jour avec succès.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erreur lors de la mise à jour du collaborateur', [
                    'error' => $e->getMessage(),
                    'referrer_id' => $id
                ]);

                return back()->withErrors(['error' => 'Erreur lors de la mise à jour du collaborateur.'])->withInput();
            }
        });
    }

    /**
     * Supprimer un collaborateur
     */
    public function destroy(Request $request, $id)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        // Vérifier les permissions
        $userRole = $user['role'] ?? 'user';
        if (!in_array($userRole, ['admin', 'owner', 'organizer'])) {
            return redirect()->route('org.dashboard', ['org_slug' => $orgSlug])
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return TenantHelper::withTenantConnection(function() use ($id, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            $referrer = Referrer::where('id', $id)
                ->where('organization_id', $organization->id)
                ->firstOrFail();

            // Vérifier s'il y a des inscriptions liées
            $hasRegistrations = $referrer->registrations()->count() > 0;

            if ($hasRegistrations) {
                return redirect()->route('org.collaborateurs.show', [
                    'org_slug' => $orgSlug,
                    'id' => $id
                ])->with('error', 'Ce collaborateur ne peut pas être supprimé car des inscriptions ont été effectuées via son code.');
            }

            DB::beginTransaction();
            try {
                // Supprimer les commissions associées (s'il n'y a pas d'inscriptions, on peut supprimer les commissions)
                $referrer->commissions()->delete();

                // Supprimer les notifications associées
                $referrer->notifications()->delete();

                // Supprimer l'utilisateur associé si existe
                if ($referrer->user_id) {
                    DB::connection('tenant')->table('users')
                        ->where('id', $referrer->user_id)
                        ->delete();
                }

                // Supprimer le collaborateur
                $referrer->delete();

                DB::commit();

                Log::info('Collaborateur supprimé', [
                    'referrer_id' => $id,
                    'organization_id' => $organization->id
                ]);

                return redirect()->route('org.collaborateurs.index', ['org_slug' => $orgSlug])
                    ->with('success', 'Collaborateur supprimé avec succès.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erreur lors de la suppression du collaborateur', [
                    'error' => $e->getMessage(),
                    'referrer_id' => $id
                ]);

                return back()->withErrors(['error' => 'Erreur lors de la suppression du collaborateur.']);
            }
        });
    }

    /**
     * Toggle le statut actif/inactif d'un apporteur
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        return TenantHelper::withTenantConnection(function() use ($id, $user, $orgSlug) {
            $organization = TenantHelper::getCurrentOrganization();

            $referrer = Referrer::where('id', $id)
                ->where('organization_id', $organization->id)
                ->firstOrFail();

            $referrer->update([
                'is_active' => !$referrer->is_active
            ]);

            return back()->with('success', 'Statut du collaborateur mis à jour.');
        });
    }

    /**
     * Envoyer les identifiants de connexion au collaborateur par email
     */
    private function sendCollaboratorCredentialsEmail($referrer, $password, $organization)
    {
        try {
            if (empty($referrer->email)) {
                Log::warning('Email non fourni pour le collaborateur', [
                    'referrer_id' => $referrer->id
                ]);
                return;
            }

            $orgKey = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $organization->id)
                ->value('org_key');

            $loginUrl = url("/org/{$orgKey}/login");

            Mail::send('emails.collaborator-credentials', [
                'collaboratorName' => $referrer->name,
                'email' => $referrer->email,
                'password' => $password,
                'referrerCode' => $referrer->referrer_code,
                'organizationName' => $organization->org_name,
                'loginUrl' => $loginUrl,
            ], function ($message) use ($referrer, $organization) {
                $message->to($referrer->email, $referrer->name)
                    ->subject("🎉 Vos identifiants de connexion - {$organization->org_name}");
            });

            Log::info('Email d\'identifiants envoyé au collaborateur', [
                'referrer_id' => $referrer->id,
                'email' => $referrer->email
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email d\'identifiants', [
                'referrer_id' => $referrer->id,
                'error' => $e->getMessage()
            ]);
            // Ne pas bloquer la création du collaborateur en cas d'erreur d'email
        }
    }
}

