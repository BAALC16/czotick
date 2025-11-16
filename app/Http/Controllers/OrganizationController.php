<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * Afficher les événements publics de l'organisation
     */
    public function showEvents(Request $request)
    {
        $organization = $this->resolveCurrentOrganization($request);
        
        return view('errors.404');
    }
    
    /**
     * Dashboard administratif de l'organisation
     */
    public function dashboard(Request $request)
    {
        $organization = $this->resolveCurrentOrganization($request);
        $user = app('current.org_user');
        
        // Statistiques générales
        $stats = $this->getOrganizationStats();
        
        // Événements récents
        $recentEvents = DB::connection('tenant')
                         ->table('events')
                         ->orderBy('created_at', 'desc')
                         ->limit(5)
                         ->get();
        
        // Inscriptions récentes
        $recentRegistrations = DB::connection('tenant')
                                ->table('registrations')
                                ->join('events', 'registrations.event_id', '=', 'events.id')
                                ->join('ticket_types', 'registrations.ticket_type_id', '=', 'ticket_types.id')
                                ->select(
                                    'registrations.*',
                                    'events.event_title',
                                    'ticket_types.ticket_name'
                                )
                                ->orderBy('registrations.registration_date', 'desc')
                                ->limit(10)
                                ->get();
        
        // Revenus par mois (derniers 6 mois)
        $monthlyRevenue = $this->getMonthlyRevenue();
        
        return view('organization.dashboard', compact(
            'organization', 
            'user', 
            'stats', 
            'recentEvents', 
            'recentRegistrations',
            'monthlyRevenue'
        ));
    }
    
    /**
     * Afficher la liste des organisations (Admin SaaS)
     */
    public function index(Request $request)
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
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('organizations.org_name', 'like', "%{$search}%")
                  ->orWhere('organizations.contact_email', 'like', "%{$search}%")
                  ->orWhere('organizations.org_key', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('org_type') && $request->org_type) {
            $query->where('organizations.org_type', $request->org_type);
        }
        
        if ($request->has('subscription_status') && $request->subscription_status) {
            $query->where('organizations.subscription_status', $request->subscription_status);
        }
        
        $organizations = $query->orderBy('organizations.created_at', 'desc')
                              ->paginate(20);
        
        // Ajouter les statistiques pour chaque organisation
        foreach ($organizations as $org) {
            $org->events_count = $this->getOrganizationEventsCount($org->database_name);
            $org->users_count = DB::table('saas_users')
                                 ->where('organization_id', $org->id)
                                 ->where('is_active', 1)
                                 ->count();
        }
        
        return view('admin.organizations.index', compact('organizations'));
    }
    
    /**
     * Afficher les détails d'une organisation (Admin SaaS)
     */
    public function show($id)
    {
        $organization = DB::table('organizations')->where('id', $id)->first();
        
        if (!$organization) {
            abort(404, 'Organisation non trouvée');
        }
        
        // Utilisateurs de l'organisation
        $users = DB::table('saas_users')
                  ->where('organization_id', $id)
                  ->orderBy('created_at', 'desc')
                  ->get();
        
        // Statistiques détaillées
        $stats = $this->getDetailedOrganizationStats($organization->database_name);
        
        // Logs récents
        $logs = DB::table('organization_logs')
                 ->where('organization_id', $id)
                 ->orderBy('created_at', 'desc')
                 ->limit(50)
                 ->get();
        
        return view('admin.organizations.show', compact('organization', 'users', 'stats', 'logs'));
    }
    
    /**
     * Créer une nouvelle organisation (Admin SaaS)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'org_name' => 'required|string|max:255',
            'org_type' => 'required|in:jci,rotary,lions,association,company,other',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:organizations,contact_email',
            'contact_phone' => 'nullable|string|max:20',
            'subdomain' => 'required|string|max:50|unique:organizations,subdomain|alpha_dash',
            'subscription_plan' => 'required|in:trial,basic,premium,enterprise'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Générer l'org_key
            $orgKey = Str::slug($request->subdomain);
            
            // Créer l'organisation
            $organizationId = DB::table('organizations')->insertGetId([
                'org_key' => $orgKey,
                'org_name' => $request->org_name,
                'org_type' => $request->org_type,
                'contact_name' => $request->contact_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'database_name' => 'org_' . $orgKey,
                'subdomain' => $request->subdomain,
                'subscription_plan' => $request->subscription_plan,
                'subscription_status' => 'active',
                'trial_ends_at' => now()->addDays(14),
                'max_events' => $this->getMaxEventsByPlan($request->subscription_plan),
                'max_participants_per_event' => $this->getMaxParticipantsByPlan($request->subscription_plan),
                'max_storage_mb' => $this->getMaxStorageByPlan($request->subscription_plan),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Créer l'utilisateur propriétaire
            $userId = DB::table('saas_users')->insertGetId([
                'organization_id' => $organizationId,
                'email' => $request->contact_email,
                'password' => bcrypt('password123'), // Mot de passe temporaire
                'first_name' => $request->contact_name,
                'last_name' => '',
                'phone' => $request->contact_phone,
                'role' => 'owner',
                'is_active' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Créer la base de données tenant
            $this->createTenantDatabase($orgKey, $request->org_type);
            
            // Log de création
            DB::table('organization_logs')->insert([
                'organization_id' => $organizationId,
                'user_id' => $userId,
                'action' => 'organization_created',
                'description' => 'Organisation créée via interface admin',
                'created_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.organizations.show', $organizationId)
                           ->with('success', 'Organisation créée avec succès !');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Mettre à jour une organisation
     */
    public function update(Request $request, $id)
    {
        $organization = DB::table('organizations')->where('id', $id)->first();
        
        if (!$organization) {
            abort(404, 'Organisation non trouvée');
        }
        
        $validator = Validator::make($request->all(), [
            'org_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:organizations,contact_email,' . $id,
            'contact_phone' => 'nullable|string|max:20',
            'subscription_plan' => 'required|in:trial,basic,premium,enterprise',
            'subscription_status' => 'required|in:active,suspended,cancelled,expired'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        DB::table('organizations')->where('id', $id)->update([
            'org_name' => $request->org_name,
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'subscription_plan' => $request->subscription_plan,
            'subscription_status' => $request->subscription_status,
            'max_events' => $this->getMaxEventsByPlan($request->subscription_plan),
            'max_participants_per_event' => $this->getMaxParticipantsByPlan($request->subscription_plan),
            'max_storage_mb' => $this->getMaxStorageByPlan($request->subscription_plan),
            'updated_at' => now()
        ]);
        
        // Log de modification
        DB::table('organization_logs')->insert([
            'organization_id' => $id,
            'action' => 'organization_updated',
            'description' => 'Organisation mise à jour via interface admin',
            'created_at' => now()
        ]);
        
        return back()->with('success', 'Organisation mise à jour avec succès !');
    }
    
    /**
     * Gestion des paramètres de l'organisation
     */
    public function settings(Request $request)
    {
        $organization = $this->resolveCurrentOrganization($request);
        $user = app('current.org_user');
        
        return view('organization.settings', compact('organization', 'user'));
    }
    
    /**
     * Mettre à jour les paramètres de l'organisation
     */
    public function updateSettings(Request $request)
    {
        $organization = $this->resolveCurrentOrganization($request);
        
        $validator = Validator::make($request->all(), [
            'org_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $updateData = [
            'org_name' => $request->org_name,
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'updated_at' => now()
        ];
        
        // Gestion de l'upload du logo
        if ($request->hasFile('logo')) {
            // Créer le répertoire s'il n'existe pas
            $directory = public_path('organizations/' . $organization->org_key . '/logo');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Nom du fichier : logo.png
            $filename = 'logo.png';
            $fullPath = $directory . '/' . $filename;
            
            // Déplacer le fichier
            $request->file('logo')->move($directory, $filename);
            
            // Chemin relatif pour la base de données
            $logoPath = 'organizations/' . $organization->org_key . '/logo/' . $filename;
            $updateData['organization_logo'] = $logoPath;
        }
        
        DB::table('organizations')->where('id', $organization->id)->update($updateData);
        
        return back()->with('success', 'Paramètres mis à jour avec succès !');
    }
    
    // ============================================
    // MÉTHODES PRIVÉES
    // ============================================
    
    /**
     * Obtenir les statistiques de l'organisation
     */
    private function getOrganizationStats()
    {
        $stats = [
            'total_events' => DB::connection('tenant')->table('events')->count(),
            'published_events' => DB::connection('tenant')->table('events')->where('is_published', 1)->count(),
            'total_registrations' => DB::connection('tenant')->table('registrations')->count(),
            'paid_registrations' => DB::connection('tenant')->table('registrations')->where('payment_status', 'paid')->count(),
            'pending_registrations' => DB::connection('tenant')->table('registrations')->where('payment_status', 'pending')->count(),
            'total_revenue' => DB::connection('tenant')
                                ->table('registrations')
                                ->where('payment_status', 'paid')
                                ->sum('amount_paid'),
            'this_month_registrations' => DB::connection('tenant')
                                           ->table('registrations')
                                           ->whereMonth('registration_date', now()->month)
                                           ->whereYear('registration_date', now()->year)
                                           ->count(),
            'this_month_revenue' => DB::connection('tenant')
                                     ->table('registrations')
                                     ->where('payment_status', 'paid')
                                     ->whereMonth('registration_date', now()->month)
                                     ->whereYear('registration_date', now()->year)
                                     ->sum('amount_paid')
        ];
        
        return $stats;
    }
    
    /**
     * Obtenir les revenus par mois
     */
    private function getMonthlyRevenue()
    {
        $monthlyRevenue = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = DB::connection('tenant')
                        ->table('registrations')
                        ->where('payment_status', 'paid')
                        ->whereMonth('registration_date', $date->month)
                        ->whereYear('registration_date', $date->year)
                        ->sum('amount_paid');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }
        
        return $monthlyRevenue;
    }
    
    /**
     * Créer la base de données tenant
     */
    private function createTenantDatabase($orgKey, $orgType)
    {
        $databaseName = 'org_' . $orgKey;
        
        // Créer la base de données
        DB::statement("CREATE DATABASE `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Récupérer le template SQL approprié
        $template = DB::table('database_templates')
                     ->where('org_type', $orgType)
                     ->where('is_active', 1)
                     ->orderBy('template_version', 'desc')
                     ->first();
        
        if ($template) {
            // Appliquer le template
            DB::statement("USE `{$databaseName}`; " . $template->sql_structure);
        }
        
        // Revenir à la base maître
        DB::statement("USE `" . env('DB_DATABASE') . "`");
    }
    
    /**
     * Obtenir le nombre maximum d'événements selon le plan
     */
    private function getMaxEventsByPlan($plan)
    {
        return match($plan) {
            'trial' => 1,
            'basic' => 5,
            'premium' => 20,
            'enterprise' => -1,
            default => 1
        };
    }
    
    /**
     * Obtenir le nombre maximum de participants selon le plan
     */
    private function getMaxParticipantsByPlan($plan)
    {
        return match($plan) {
            'trial' => 50,
            'basic' => 200,
            'premium' => 1000,
            'enterprise' => -1,
            default => 50
        };
    }
    
    /**
     * Obtenir l'espace de stockage maximum selon le plan
     */
    private function getMaxStorageByPlan($plan)
    {
        return match($plan) {
            'trial' => 50,
            'basic' => 500,
            'premium' => 2000,
            'enterprise' => 10000,
            default => 50
        };
    }
    
    /**
     * Obtenir le nombre d'événements d'une organisation
     */
    private function getOrganizationEventsCount($databaseName)
    {
        try {
            return DB::connection('mysql')
                    ->select("SELECT COUNT(*) as count FROM `{$databaseName}`.events")[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Obtenir les statistiques détaillées d'une organisation
     */
    private function getDetailedOrganizationStats($databaseName)
    {
        try {
            $stats = DB::connection('mysql')->select("
                SELECT 
                    (SELECT COUNT(*) FROM `{$databaseName}`.events) as total_events,
                    (SELECT COUNT(*) FROM `{$databaseName}`.events WHERE is_published = 1) as published_events,
                    (SELECT COUNT(*) FROM `{$databaseName}`.registrations) as total_registrations,
                    (SELECT COUNT(*) FROM `{$databaseName}`.registrations WHERE payment_status = 'paid') as paid_registrations,
                    (SELECT SUM(amount_paid) FROM `{$databaseName}`.registrations WHERE payment_status = 'paid') as total_revenue
            ");
            
            return (array) $stats[0];
        } catch (\Exception $e) {
            return [
                'total_events' => 0,
                'published_events' => 0,
                'total_registrations' => 0,
                'paid_registrations' => 0,
                'total_revenue' => 0
            ];
        }
    }

    /**
     * Récupérer l'organisation courante depuis le container ou via le paramètre de route
     */
    private function resolveCurrentOrganization(Request $request)
    {
        if (app()->bound('current.organization')) {
            return app('current.organization');
        }

        $slug = $request->route('org_slug');
        if ($slug) {
            return \DB::table('organizations')
                ->where('org_key', $slug)
                ->orWhere('subdomain', $slug)
                ->first();
        }

        return null;
    }
}