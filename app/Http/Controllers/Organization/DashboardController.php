<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\TenantHelper;
use App\Models\Referrer;
use App\Models\Notification;

class DashboardController extends Controller
{
    /**
     * Afficher le dashboard principal de l'organisation
     */
    public function index(Request $request)
    {
        Log::info('=== DASHBOARD INDEX START ===');

        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            return redirect()->route('org.login', ['org_slug' => $orgSlug]);
        }

        try {
            // Récupérer l'organisation
            $organization = $this->getOrganization($user);
            
            if (!$organization) {
                session()->forget('organization_user');
                return redirect()->route('saas.home')
                    ->withErrors(['organization' => 'Organisation non trouvée.']);
            }

            // Détecter le rôle de l'utilisateur
            $userRole = $user['role'] ?? 'user';
            
            // Vérifier si l'utilisateur est un collaborateur
            $referrer = null;
            if (in_array($userRole, ['referrer', 'user', 'admin', 'organizer'])) {
                $referrer = $this->getUserReferrer($user['id'], $organization->id);
                if ($referrer) {
                    $userRole = 'referrer';
                }
            }

            // Adapter les données selon le rôle
            $stats = $this->getDashboardStats($userRole, $referrer);
            $recentEvents = $this->getRecentEvents(5, $userRole, $referrer);
            $upcomingEvents = $this->getUpcomingEvents(5, $userRole, $referrer);
            
            // Données spécifiques selon le rôle
            $roleSpecificData = $this->getRoleSpecificData($userRole, $referrer, $user);

            Log::info('Dashboard loaded successfully', [
                'user_id' => $user['id'],
                'org_slug' => $orgSlug,
                'user_role' => $userRole,
                'is_referrer' => $referrer ? true : false
            ]);

            return view('organization.dashboard.index', compact(
                'user',
                'orgSlug', 
                'organization',
                'stats',
                'recentEvents',
                'upcomingEvents',
                'userRole',
                'referrer',
                'roleSpecificData'
            ));

        } catch (\Exception $e) {
            Log::error('=== DASHBOARD ERROR ===', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->showErrorDashboard($user, $orgSlug, $e->getMessage());
        }
    }

    /**
     * Obtenir les inscriptions filtrées (AJAX)
     */
    public function getFilteredRegistrations(Request $request)
    {
        try {
            $eventId = $request->input('event_id');
            $status = $request->input('status');
            $search = $request->input('search');

            $query = DB::connection('tenant')
                ->table('registrations as r')
                ->leftJoin('events as e', 'r.event_id', '=', 'e.id')
                ->select(
                    'r.id',
                    'r.registration_number',
                    'r.fullname',
                    'r.email',
                    'r.phone',
                    'r.status',
                    'r.payment_status',
                    'r.ticket_price',
                    'r.amount_paid',
                    'r.registration_date',
                    'e.event_title'
                );

            if ($eventId) {
                $query->where('r.event_id', $eventId);
            }

            if ($status) {
                $query->where('r.status', $status);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('r.fullname', 'like', "%{$search}%")
                      ->orWhere('r.email', 'like', "%{$search}%")
                      ->orWhere('r.registration_number', 'like', "%{$search}%");
                });
            }

            $registrations = $query->orderBy('r.registration_date', 'desc')
                ->limit(100)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $registrations
            ]);

        } catch (\Exception $e) {
            Log::error('Error filtering registrations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du filtrage des inscriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques du dashboard selon le rôle
     */
    private function getDashboardStats($userRole = 'user', $referrer = null)
    {
        try {
            $stats = [];

            if ($userRole === 'admin' || $userRole === 'owner') {
                // Admin/Owner : voit tout
                $stats = $this->getAdminStats();
            } elseif ($userRole === 'organizer') {
                // Organisateur : voit ses événements et statistiques
                $stats = $this->getOrganizerStats();
            } elseif ($userRole === 'referrer' && $referrer) {
                // Collaborateur : voit ses événements et gains
                $stats = $this->getReferrerStats($referrer);
            } else {
                // User par défaut
                $stats = $this->getDefaultStats();
            }
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->getEmptyStats();
        }
    }

    /**
     * Statistiques pour l'administrateur (voit tout)
     */
    private function getAdminStats()
    {
        $totalEvents = $this->safeCount('events');
        $publishedEvents = $this->safeCount('events', [['is_published', '=', 1]]);
        $upcomingEvents = $this->safeCount('events', [
            ['event_date', '>=', now()->toDateString()],
            ['is_published', '=', 1]
        ]);
        
        $totalRegistrations = $this->safeCount('registrations');
        $confirmedRegistrations = $this->safeCount('registrations', [['status', '=', 'confirmed']]);
        $pendingRegistrations = $this->safeCount('registrations', [['status', '=', 'pending']]);
        
        $totalRevenue = DB::connection('tenant')
            ->table('registrations')
            ->where('payment_status', 'paid')
            ->sum('amount_paid') ?? 0;
            
        $monthlyRevenue = DB::connection('tenant')
            ->table('registrations')
            ->where('payment_status', 'paid')
            ->whereMonth('registration_date', now()->month)
            ->whereYear('registration_date', now()->year)
            ->sum('amount_paid') ?? 0;
            
        $monthlyRegistrations = DB::connection('tenant')
            ->table('registrations')
            ->whereMonth('registration_date', now()->month)
            ->whereYear('registration_date', now()->year)
            ->count();
            
        $pendingRevenue = DB::connection('tenant')
            ->table('registrations')
            ->where('payment_status', 'pending')
            ->sum('ticket_price') ?? 0;
        
        $totalVerifications = $this->safeCount('ticket_verifications');
        $successfulVerifications = $this->safeCount('ticket_verifications', [['status', '=', 'success']]);
        
        // Statistiques collaborateurs
        $totalReferrers = $this->safeCount('referrers');
        $activeReferrers = $this->safeCount('referrers', [['is_active', '=', 1]]);
        $totalReferrerEarnings = DB::connection('tenant')
            ->table('referrer_registrations')
            ->where('commission_status', 'paid')
            ->sum('commission_amount') ?? 0;
        
        return [
            'total_events' => $totalEvents,
            'published_events' => $publishedEvents,
            'upcoming_events' => $upcomingEvents,
            'total_registrations' => $totalRegistrations,
            'confirmed_registrations' => $confirmedRegistrations,
            'pending_registrations' => $pendingRegistrations,
            'total_revenue' => number_format($totalRevenue, 2, '.', ''),
            'monthly_revenue' => number_format($monthlyRevenue, 2, '.', ''),
            'monthly_registrations' => $monthlyRegistrations,
            'pending_revenue' => $pendingRevenue,
            'total_verifications' => $totalVerifications,
            'successful_verifications' => $successfulVerifications,
            'total_referrers' => $totalReferrers,
            'active_referrers' => $activeReferrers,
            'total_referrer_earnings' => number_format($totalReferrerEarnings, 2, '.', '')
        ];
    }

    /**
     * Statistiques pour l'organisateur
     */
    private function getOrganizerStats()
    {
        $totalEvents = $this->safeCount('events');
        $publishedEvents = $this->safeCount('events', [['is_published', '=', 1]]);
        $upcomingEvents = $this->safeCount('events', [
            ['event_date', '>=', now()->toDateString()],
            ['is_published', '=', 1]
        ]);
        
        $totalRegistrations = $this->safeCount('registrations');
        $confirmedRegistrations = $this->safeCount('registrations', [['status', '=', 'confirmed']]);
        $pendingRegistrations = $this->safeCount('registrations', [['status', '=', 'pending']]);
        
        $totalRevenue = DB::connection('tenant')
            ->table('registrations')
            ->where('payment_status', 'paid')
            ->sum('amount_paid') ?? 0;
            
        $monthlyRevenue = DB::connection('tenant')
            ->table('registrations')
            ->where('payment_status', 'paid')
            ->whereMonth('registration_date', now()->month)
            ->whereYear('registration_date', now()->year)
            ->sum('amount_paid') ?? 0;
            
        $monthlyRegistrations = DB::connection('tenant')
            ->table('registrations')
            ->whereMonth('registration_date', now()->month)
            ->whereYear('registration_date', now()->year)
            ->count();
        
        // Statistiques collaborateurs
        $totalReferrers = $this->safeCount('referrers');
        $activeReferrers = $this->safeCount('referrers', [['is_active', '=', 1]]);
        
        return [
            'total_events' => $totalEvents,
            'published_events' => $publishedEvents,
            'upcoming_events' => $upcomingEvents,
            'total_registrations' => $totalRegistrations,
            'confirmed_registrations' => $confirmedRegistrations,
            'pending_registrations' => $pendingRegistrations,
            'total_revenue' => number_format($totalRevenue, 2, '.', ''),
            'monthly_revenue' => number_format($monthlyRevenue, 2, '.', ''),
            'monthly_registrations' => $monthlyRegistrations,
            'total_referrers' => $totalReferrers,
            'active_referrers' => $activeReferrers
        ];
    }

    /**
     * Statistiques pour le collaborateur
     */
    private function getReferrerStats($referrer)
    {
        // Événements avec commissions pour ce collaborateur
        $eventIds = DB::connection('tenant')
            ->table('referrer_commissions')
            ->where('referrer_id', $referrer->id)
            ->pluck('event_id');

        $totalEvents = count($eventIds);
        $upcomingEvents = DB::connection('tenant')
            ->table('events')
            ->whereIn('id', $eventIds)
            ->where('is_published', true)
            ->where('event_date', '>=', now()->toDateString())
            ->count();
        
        // Inscriptions de l'apporteur
        $totalRegistrations = DB::connection('tenant')
            ->table('referrer_registrations')
            ->where('referrer_id', $referrer->id)
            ->count();
        
        $confirmedRegistrations = DB::connection('tenant')
            ->table('referrer_registrations')
            ->where('referrer_id', $referrer->id)
            ->join('registrations', 'referrer_registrations.registration_id', '=', 'registrations.id')
            ->where('registrations.status', 'confirmed')
            ->count();
        
        // Gains
        $totalEarnings = DB::connection('tenant')
            ->table('referrer_registrations')
            ->where('referrer_id', $referrer->id)
            ->where('commission_status', 'paid')
            ->sum('commission_amount') ?? 0;
        
        $pendingEarnings = DB::connection('tenant')
            ->table('referrer_registrations')
            ->where('referrer_id', $referrer->id)
            ->where('commission_status', 'pending')
            ->sum('commission_amount') ?? 0;
        
        $monthlyEarnings = DB::connection('tenant')
            ->table('referrer_registrations')
            ->where('referrer_id', $referrer->id)
            ->where('commission_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount') ?? 0;
        
        return [
            'total_events' => $totalEvents,
            'upcoming_events' => $upcomingEvents,
            'total_registrations' => $totalRegistrations,
            'confirmed_registrations' => $confirmedRegistrations,
            'total_earnings' => number_format($totalEarnings, 2, '.', ''),
            'pending_earnings' => number_format($pendingEarnings, 2, '.', ''),
            'monthly_earnings' => number_format($monthlyEarnings, 2, '.', '')
        ];
    }

    /**
     * Statistiques par défaut
     */
    private function getDefaultStats()
    {
        return [
            'total_events' => 0,
            'published_events' => 0,
            'upcoming_events' => 0,
            'total_registrations' => 0,
            'confirmed_registrations' => 0,
            'pending_registrations' => 0,
            'total_revenue' => '0.00',
            'monthly_revenue' => '0.00',
            'monthly_registrations' => 0
        ];
    }

    /**
     * Méthode utilitaire pour compter de manière sécurisée
     */
    private function safeCount($table, $conditions = [])
    {
        try {
            $query = DB::connection('tenant')->table($table);
            
            if (!empty($conditions)) {
                if (is_array($conditions) && isset($conditions[0]) && is_array($conditions[0])) {
                    // Conditions multiples avec opérateurs
                    foreach ($conditions as $condition) {
                        if (count($condition) === 3) {
                            $query->where($condition[0], $condition[1], $condition[2]);
                        } elseif (count($condition) === 2) {
                            $query->where($condition[0], $condition[1]);
                        }
                    }
                } else {
                    // Conditions simples
                    foreach ($conditions as $key => $value) {
                        $query->where($key, $value);
                    }
                }
            }
            
            return $query->count();
            
        } catch (\Exception $e) {
            Log::warning("Error counting table: {$table}", ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Récupérer l'organisation
     */
    private function getOrganization($user)
    {
        try {
            $organization = DB::connection('saas_master')
                ->table('organizations')
                ->where('id', $user['org_id'])
                ->select('id', 'org_name', 'subdomain', 'organization_logo', 'database_name')
                ->first();

            return $organization;
            
        } catch (\Exception $e) {
            Log::error('Error fetching organization from saas_master', [
                'org_id' => $user['org_id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Obtenir les événements récents selon le rôle
     */
    private function getRecentEvents($limit = 5, $userRole = 'user', $referrer = null)
    {
        try {
            $query = DB::connection('tenant')
                ->table('events')
                ->select('id', 'event_title', 'event_date', 'is_published', 'created_at', 'event_slug');

            // Filtrer selon le rôle
            if ($userRole === 'referrer' && $referrer) {
                $eventIds = DB::connection('tenant')
                    ->table('referrer_commissions')
                    ->where('referrer_id', $referrer->id)
                    ->pluck('event_id');
                $query->whereIn('id', $eventIds);
            }

            return $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($event) use ($userRole, $referrer) {
                    $event->event_date = $event->event_date ? Carbon::parse($event->event_date) : null;
                    $event->created_at = Carbon::parse($event->created_at);
                    
                    // Ajouter l'URL de partage pour les collaborateurs
                    if ($userRole === 'referrer' && $referrer && $event->event_slug) {
                        $org = $this->getOrganization(session('organization_user'));
                        if ($org) {
                            $event->share_url = url("/{$org->org_key}/{$event->event_slug}?ref={$referrer->referrer_code}");
                        }
                    }
                    
                    return $event;
                });

        } catch (\Exception $e) {
            Log::error('Error getting recent events', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Obtenir les événements à venir selon le rôle
     */
    private function getUpcomingEvents($limit = 5, $userRole = 'user', $referrer = null)
    {
        try {
            $query = DB::connection('tenant')
                ->table('events')
                ->select('id', 'event_title', 'event_date', 'event_location', 'max_participants', 'event_slug')
                ->where('is_published', true)
                ->where('event_date', '>=', now()->toDateString());

            // Filtrer selon le rôle
            if ($userRole === 'referrer' && $referrer) {
                $eventIds = DB::connection('tenant')
                    ->table('referrer_commissions')
                    ->where('referrer_id', $referrer->id)
                    ->pluck('event_id');
                $query->whereIn('id', $eventIds);
            }

            return $query->orderBy('event_date', 'asc')
                ->limit($limit)
                ->get()
                ->map(function ($event) use ($userRole, $referrer) {
                    $event->event_date = $event->event_date ? Carbon::parse($event->event_date) : null;
                    
                    // Ajouter l'URL de partage pour les collaborateurs
                    if ($userRole === 'referrer' && $referrer && $event->event_slug) {
                        $org = $this->getOrganization(session('organization_user'));
                        if ($org) {
                            $event->share_url = url("/{$org->org_key}/{$event->event_slug}?ref={$referrer->referrer_code}");
                        }
                    }
                    
                    return $event;
                });

        } catch (\Exception $e) {
            Log::error('Error getting upcoming events', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Obtenir le collaborateur lié à l'utilisateur
     */
    private function getUserReferrer($userId, $organizationId)
    {
        try {
            return DB::connection('tenant')
                ->table('referrers')
                ->where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->where('is_active', true)
                ->first();
        } catch (\Exception $e) {
            Log::error('Error getting user referrer', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Obtenir les données spécifiques au rôle
     */
    private function getRoleSpecificData($userRole, $referrer, $user)
    {
        $data = [];

        if ($userRole === 'referrer' && $referrer) {
            // Inscriptions récentes du collaborateur
            $data['recent_registrations'] = DB::connection('tenant')
                ->table('referrer_registrations')
                ->join('registrations', 'referrer_registrations.registration_id', '=', 'registrations.id')
                ->join('events', 'referrer_registrations.event_id', '=', 'events.id')
                ->where('referrer_registrations.referrer_id', $referrer->id)
                ->select(
                    'referrer_registrations.*',
                    'registrations.fullname',
                    'registrations.email',
                    'registrations.registration_number',
                    'events.event_title',
                    'events.event_date'
                )
                ->orderBy('referrer_registrations.created_at', 'desc')
                ->limit(10)
                ->get();

            // Notifications non lues
            $data['notifications'] = DB::connection('tenant')
                ->table('notifications')
                ->where('referrer_id', $referrer->id)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } elseif (in_array($userRole, ['admin', 'owner', 'organizer'])) {
            // Notifications pour organisateurs/admins
            $data['notifications'] = DB::connection('tenant')
                ->table('notifications')
                ->where('user_id', $user['id'])
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return $data;
    }

    /**
     * Statistiques vides par défaut
     */
    private function getEmptyStats()
    {
        return [
            'total_events' => 0,
            'published_events' => 0,
            'upcoming_events' => 0,
            'total_registrations' => 0,
            'confirmed_registrations' => 0,
            'pending_registrations' => 0,
            'total_revenue' => '0.00',
            'monthly_revenue' => '0.00',
            'monthly_registrations' => 0,
            'pending_revenue' => 0,
            'total_verifications' => 0,
            'successful_verifications' => 0
        ];
    }

    /**
     * Afficher le dashboard d'erreur
     */
    private function showErrorDashboard($user, $orgSlug, $error)
    {
        return view('organization.dashboard.error', compact('user', 'orgSlug', 'error'));
    }
}
