<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

            // Statistiques générales
            $stats = $this->getDashboardStats();
            
            // Événements récents
            $recentEvents = $this->getRecentEvents(5);
            
            // Événements à venir
            $upcomingEvents = $this->getUpcomingEvents(5);

            Log::info('Dashboard loaded successfully', [
                'user_id' => $user['id'],
                'org_slug' => $orgSlug,
                'stats_loaded' => !empty($stats)
            ]);

            return view('organization.dashboard.index', compact(
                'user',
                'orgSlug', 
                'organization',
                'stats',
                'recentEvents',
                'upcomingEvents'
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
     * Obtenir les statistiques du dashboard
     */
    private function getDashboardStats()
    {
        try {
            // Statistiques des événements
            $totalEvents = $this->safeCount('events');
            $publishedEvents = $this->safeCount('events', [['is_published', '=', 1]]);
            $upcomingEvents = $this->safeCount('events', [
                ['event_date', '>=', now()->toDateString()],
                ['is_published', '=', 1]
            ]);
            
            // Statistiques des inscriptions
            $totalRegistrations = $this->safeCount('registrations');
            $confirmedRegistrations = $this->safeCount('registrations', [['status', '=', 'confirmed']]);
            $pendingRegistrations = $this->safeCount('registrations', [['status', '=', 'pending']]);
            
            // Statistiques de revenus
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
            
            // Statistiques de vérification
            $totalVerifications = $this->safeCount('ticket_verifications');
            $successfulVerifications = $this->safeCount('ticket_verifications', [['status', '=', 'success']]);
            
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
                'successful_verifications' => $successfulVerifications
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->getEmptyStats();
        }
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
     * Obtenir les événements récents
     */
    private function getRecentEvents($limit = 5)
    {
        try {
            return DB::connection('tenant')
                ->table('events')
                ->select('id', 'event_title', 'event_date', 'is_published', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($event) {
                    $event->event_date = $event->event_date ? Carbon::parse($event->event_date) : null;
                    $event->created_at = Carbon::parse($event->created_at);
                    return $event;
                });

        } catch (\Exception $e) {
            Log::error('Error getting recent events', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Obtenir les événements à venir
     */
    private function getUpcomingEvents($limit = 5)
    {
        try {
            return DB::connection('tenant')
                ->table('events')
                ->select('id', 'event_title', 'event_date', 'event_location', 'max_participants')
                ->where('is_published', true)
                ->where('event_date', '>=', now()->toDateString())
                ->orderBy('event_date', 'asc')
                ->limit($limit)
                ->get()
                ->map(function ($event) {
                    $event->event_date = $event->event_date ? Carbon::parse($event->event_date) : null;
                    return $event;
                });

        } catch (\Exception $e) {
            Log::error('Error getting upcoming events', ['error' => $e->getMessage()]);
            return collect();
        }
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
