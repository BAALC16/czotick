<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SimpleDashboardController extends Controller
{
    /**
     * Dashboard principal
     */
    public function index()
    {
        // Vérifier l'authentification
        if (!session()->has('super_admin_logged_in')) {
            return redirect()->route('super-admin.login');
        }

        $stats = $this->getBasicStats();
        
        return view('super-admin.dashboard.index', compact('stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | GESTION DES ORGANISATIONS
    |--------------------------------------------------------------------------
    */

    public function organizations(Request $request)
    {
        $query = DB::table('organizations')
            ->leftJoin('saas_users', function($join) {
                $join->on('organizations.id', '=', 'saas_users.organization_id')
                     ->where('saas_users.role', '=', 'owner');
            })
            ->select(
                'organizations.*',
                'saas_users.email as owner_email',
                'saas_users.first_name as owner_first_name',
                'saas_users.last_name as owner_last_name'
            );

        // Filtres
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('organizations.org_name', 'like', "%{$search}%")
                  ->orWhere('organizations.contact_email', 'like', "%{$search}%")
                  ->orWhere('organizations.org_key', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('organizations.subscription_status', $request->status);
        }

        if ($request->has('plan')) {
            $query->where('organizations.subscription_plan', $request->plan);
        }

        $organizations = $query->orderBy('organizations.created_at', 'desc')
                              ->paginate(20);

        return view('super-admin.organizations.index', compact('organizations'));
    }

    public function createOrganization(Request $request)
    {
        $request->validate([
            'org_key' => 'required|unique:organizations|alpha_dash|max:50',
            'org_name' => 'required|max:255',
            'org_type' => 'required|in:jci,rotary,lions,association,company,other',
            'contact_name' => 'required|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|max:20',
            'subdomain' => 'required|unique:organizations|alpha_dash|max:50',
            'subscription_plan' => 'required|in:trial,basic,premium,enterprise'
        ]);

        try {
            DB::beginTransaction();

            // Créer l'organisation
            $orgId = DB::table('organizations')->insertGetId([
                'org_key' => $request->org_key,
                'org_name' => $request->org_name,
                'org_type' => $request->org_type,
                'contact_name' => $request->contact_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'database_name' => 'org_' . $request->org_key,
                'subdomain' => $request->subdomain,
                'subscription_plan' => $request->subscription_plan,
                'subscription_status' => 'active',
                'trial_ends_at' => $request->subscription_plan === 'trial' ? now()->addDays(14) : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Créer l'utilisateur propriétaire
            DB::table('saas_users')->insert([
                'organization_id' => $orgId,
                'email' => $request->contact_email,
                'password' => bcrypt('password123'), // Mot de passe temporaire
                'first_name' => $request->contact_name,
                'last_name' => '',
                'phone' => $request->contact_phone,
                'role' => 'owner',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log de l'action
            DB::table('admin_activity_logs')->insert([
                'admin_id' => session('super_admin_id'),
                'action' => 'create_organization',
                'resource_type' => 'organization',
                'resource_id' => $orgId,
                'description' => "Organisation '{$request->org_name}' créée",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            DB::commit();

            return redirect()->route('super-admin.organizations.index')
                           ->with('success', 'Organisation créée avec succès');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création organisation: ' . $e->getMessage());
            
            return back()->withInput()
                        ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    public function showOrganization($id)
    {
        $organization = DB::table('organizations')
            ->leftJoin('saas_users', function($join) {
                $join->on('organizations.id', '=', 'saas_users.organization_id')
                     ->where('saas_users.role', '=', 'owner');
            })
            ->select(
                'organizations.*',
                'saas_users.email as owner_email',
                'saas_users.first_name as owner_first_name',
                'saas_users.last_name as owner_last_name',
                'saas_users.last_login_at as owner_last_login'
            )
            ->where('organizations.id', $id)
            ->first();

        if (!$organization) {
            return redirect()->route('super-admin.organizations.index')
                           ->with('error', 'Organisation non trouvée');
        }

        // Statistiques de l'organisation
        $stats = [
            'users_count' => DB::table('saas_users')
                              ->where('organization_id', $id)
                              ->where('is_active', 1)
                              ->count(),
            'total_users' => DB::table('saas_users')
                              ->where('organization_id', $id)
                              ->count(),
        ];

        return view('super-admin.organizations.show', compact('organization', 'stats'));
    }

    public function toggleOrganizationStatus($id)
    {
        $organization = DB::table('organizations')->where('id', $id)->first();
        
        if (!$organization) {
            return response()->json(['error' => 'Organisation non trouvée'], 404);
        }

        $newStatus = $organization->subscription_status === 'active' ? 'suspended' : 'active';
        
        DB::table('organizations')
          ->where('id', $id)
          ->update(['subscription_status' => $newStatus]);

        // Log de l'action
        DB::table('admin_activity_logs')->insert([
            'admin_id' => session('super_admin_id'),
            'action' => 'toggle_organization_status',
            'resource_type' => 'organization',
            'resource_id' => $id,
            'description' => "Statut organisation changé vers: {$newStatus}",
            'ip_address' => request()->ip(),
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'new_status' => $newStatus,
            'message' => "Organisation {$newStatus}"
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GESTION DES UTILISATEURS
    |--------------------------------------------------------------------------
    */

    public function users(Request $request)
    {
        $query = DB::table('saas_users')
            ->join('organizations', 'saas_users.organization_id', '=', 'organizations.id')
            ->select(
                'saas_users.*',
                'organizations.org_name',
                'organizations.org_key',
                'organizations.subscription_status'
            );

        // Filtres
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('saas_users.email', 'like', "%{$search}%")
                  ->orWhere('saas_users.first_name', 'like', "%{$search}%")
                  ->orWhere('saas_users.last_name', 'like', "%{$search}%")
                  ->orWhere('organizations.org_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->where('saas_users.role', $request->role);
        }

        if ($request->has('status')) {
            $active = $request->status === 'active';
            $query->where('saas_users.is_active', $active);
        }

        $users = $query->orderBy('saas_users.created_at', 'desc')
                      ->paginate(20);

        return view('super-admin.users.index', compact('users'));
    }

    public function showUser($id)
    {
        $user = DB::table('saas_users')
            ->join('organizations', 'saas_users.organization_id', '=', 'organizations.id')
            ->select(
                'saas_users.*',
                'organizations.org_name',
                'organizations.org_key',
                'organizations.subscription_status',
                'organizations.subscription_plan'
            )
            ->where('saas_users.id', $id)
            ->first();

        if (!$user) {
            return redirect()->route('super-admin.users.index')
                           ->with('error', 'Utilisateur non trouvé');
        }

        return view('super-admin.users.show', compact('user'));
    }

    public function toggleUserStatus($id)
    {
        $user = DB::table('saas_users')->where('id', $id)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $newStatus = !$user->is_active;
        
        DB::table('saas_users')
          ->where('id', $id)
          ->update(['is_active' => $newStatus]);

        // Log de l'action
        DB::table('admin_activity_logs')->insert([
            'admin_id' => session('super_admin_id'),
            'action' => 'toggle_user_status',
            'resource_type' => 'user',
            'resource_id' => $id,
            'description' => "Statut utilisateur changé vers: " . ($newStatus ? 'actif' : 'inactif'),
            'ip_address' => request()->ip(),
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'new_status' => $newStatus,
            'message' => "Utilisateur " . ($newStatus ? 'activé' : 'désactivé')
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS ET STATISTIQUES
    |--------------------------------------------------------------------------
    */

    public function analytics()
    {
        $stats = [
            'organizations' => [
                'total' => DB::table('organizations')->count(),
                'active' => DB::table('organizations')->where('subscription_status', 'active')->count(),
                'trial' => DB::table('organizations')->where('subscription_plan', 'trial')->count(),
                'growth_week' => DB::table('organizations')
                    ->where('created_at', '>=', now()->subWeek())
                    ->count(),
                'growth_month' => DB::table('organizations')
                    ->where('created_at', '>=', now()->subMonth())
                    ->count(),
            ],
            'users' => [
                'total' => DB::table('saas_users')->count(),
                'active' => DB::table('saas_users')->where('is_active', 1)->count(),
                'owners' => DB::table('saas_users')->where('role', 'owner')->count(),
            ],
            'revenue' => $this->calculateRevenue(),
        ];

        return view('super-admin.analytics.index', compact('stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | SYSTÈME ET CONFIGURATION
    |--------------------------------------------------------------------------
    */

    public function settings()
    {
        $settings = DB::table('system_settings')
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        return view('super-admin.system.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = $request->except(['_token', '_method']);
        
        foreach ($settings as $key => $value) {
            DB::table('system_settings')
              ->updateOrInsert(
                  ['setting_key' => $key],
                  ['setting_value' => $value, 'updated_at' => now()]
              );
        }

        // Log de l'action
        DB::table('admin_activity_logs')->insert([
            'admin_id' => session('super_admin_id'),
            'action' => 'update_system_settings',
            'description' => 'Paramètres système mis à jour',
            'ip_address' => $request->ip(),
            'created_at' => now()
        ]);

        return back()->with('success', 'Paramètres mis à jour avec succès');
    }

    public function systemHealth()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
            'sessions' => $this->checkSessionsHealth(),
        ];

        return view('super-admin.system.health', compact('health'));
    }

    /*
    |--------------------------------------------------------------------------
    | LOGS ET SURVEILLANCE
    |--------------------------------------------------------------------------
    */

    public function logs(Request $request)
    {
        $query = DB::table('admin_activity_logs')
            ->join('system_admins', 'admin_activity_logs.admin_id', '=', 'system_admins.id')
            ->select(
                'admin_activity_logs.*',
                'system_admins.username',
                'system_admins.first_name',
                'system_admins.last_name'
            );

        if ($request->has('action')) {
            $query->where('admin_activity_logs.action', $request->action);
        }

        if ($request->has('admin_id')) {
            $query->where('admin_activity_logs.admin_id', $request->admin_id);
        }

        if ($request->has('date_from')) {
            $query->where('admin_activity_logs.created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('admin_activity_logs.created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->orderBy('admin_activity_logs.created_at', 'desc')
                     ->paginate(50);

        return view('super-admin.logs.index', compact('logs'));
    }

    /*
    |--------------------------------------------------------------------------
    | API ENDPOINTS
    |--------------------------------------------------------------------------
    */

    public function searchOrganizations(Request $request)
    {
        $search = $request->get('q', '');
        
        $organizations = DB::table('organizations')
            ->where('org_name', 'like', "%{$search}%")
            ->orWhere('org_key', 'like', "%{$search}%")
            ->select('id', 'org_name', 'org_key', 'subscription_status')
            ->limit(10)
            ->get();

        return response()->json($organizations);
    }

    public function organizationsChartData()
    {
        $data = DB::table('organizations')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    public function validateOrgKey(Request $request)
    {
        $orgKey = $request->org_key;
        $exists = DB::table('organizations')->where('org_key', $orgKey)->exists();
        
        return response()->json(['available' => !$exists]);
    }

    public function validateSubdomain(Request $request)
    {
        $subdomain = $request->subdomain;
        $exists = DB::table('organizations')->where('subdomain', $subdomain)->exists();
        
        return response()->json(['available' => !$exists]);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES HELPER PRIVÉES
    |--------------------------------------------------------------------------
    */

    private function getBasicStats()
    {
        return [
            'organizations' => [
                'total' => DB::table('organizations')->count(),
                'active' => DB::table('organizations')->where('subscription_status', 'active')->count(),
                'trial' => DB::table('organizations')->where('subscription_plan', 'trial')->count(),
                'suspended' => DB::table('organizations')->where('subscription_status', 'suspended')->count(),
            ],
            'users' => [
                'total' => DB::table('saas_users')->count(),
                'active' => DB::table('saas_users')->where('is_active', 1)->count(),
                'owners' => DB::table('saas_users')->where('role', 'owner')->count(),
            ],
            'recent_activity' => DB::table('organizations')
                ->select('org_name', 'created_at', 'subscription_plan')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    private function calculateRevenue()
    {
        return [
            'monthly' => DB::table('organizations')
                ->join('subscription_plans', 'organizations.subscription_plan', '=', 'subscription_plans.plan_code')
                ->where('organizations.subscription_status', 'active')
                ->sum('subscription_plans.monthly_price'),
            'annual' => DB::table('organizations')
                ->join('subscription_plans', 'organizations.subscription_plan', '=', 'subscription_plans.plan_code')
                ->where('organizations.subscription_status', 'active')
                ->sum('subscription_plans.yearly_price'),
        ];
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'OK', 'message' => 'Connexion base de données active'];
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function checkStorageHealth()
    {
        $path = storage_path();
        return [
            'status' => is_writable($path) ? 'OK' : 'WARNING',
            'message' => is_writable($path) ? 'Dossier storage accessible en écriture' : 'Problème permissions storage',
            'free_space' => disk_free_space($path)
        ];
    }

    private function checkCacheHealth()
    {
        try {
            cache()->put('health_check', 'ok', 60);
            $value = cache()->get('health_check');
            return [
                'status' => $value === 'ok' ? 'OK' : 'ERROR',
                'message' => $value === 'ok' ? 'Cache fonctionnel' : 'Problème cache'
            ];
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function checkSessionsHealth()
    {
        try {
            $sessionId = session()->getId();
            return [
                'status' => $sessionId ? 'OK' : 'ERROR',
                'message' => $sessionId ? 'Sessions fonctionnelles' : 'Problème sessions',
                'session_id' => $sessionId
            ];
        } catch (\Exception $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    // Méthodes stub pour les autres fonctionnalités
    // À implémenter selon vos besoins spécifiques

    public function events() { return view('super-admin.events.index'); }
    public function forms() { return view('super-admin.forms.index'); }
    public function notifications() { return view('super-admin.notifications.index'); }
    public function subscriptionPlans() { return view('super-admin.billing.plans'); }
    public function invoices() { return view('super-admin.billing.invoices'); }
    
    // ... autres méthodes selon vos routes
}