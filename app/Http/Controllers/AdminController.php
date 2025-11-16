<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard principal du SaaS
     */
    public function dashboard()
    {
        // Statistiques générales
        $stats = $this->getGeneralStats();
        
        // Croissance mensuelle
        $monthlyGrowth = $this->getMonthlyGrowth();
        
        // Revenus par plan d'abonnement
        $revenueByPlan = $this->getRevenueByPlan();
        
        // Organisations récentes
        $recentOrganizations = DB::table('organizations')
                                ->leftJoin('saas_users', function($join) {
                                    $join->on('organizations.id', '=', 'saas_users.organization_id')
                                         ->where('saas_users.role', '=', 'owner');
                                })
                                ->select(
                                    'organizations.*',
                                    'saas_users.email as owner_email',
                                    'saas_users.first_name as owner_name'
                                )
                                ->orderBy('organizations.created_at', 'desc')
                                ->limit(10)
                                ->get();
        
        // Alertes système
        $alerts = $this->getSystemAlerts();
        
        // Activité récente
        $recentActivity = DB::table('organization_logs')
                           ->join('organizations', 'organization_logs.organization_id', '=', 'organizations.id')
                           ->leftJoin('saas_users', 'organization_logs.user_id', '=', 'saas_users.id')
                           ->select(
                               'organization_logs.*',
                               'organizations.org_name',
                               'saas_users.first_name',
                               'saas_users.last_name'
                           )
                           ->orderBy('organization_logs.created_at', 'desc')
                           ->limit(20)
                           ->get();
        
        return view('admin.dashboard', compact(
            'stats',
            'monthlyGrowth',
            'revenueByPlan',
            'recentOrganizations',
            'alerts',
            'recentActivity'
        ));
    }
    
    /**
     * Page de métriques et analytics
     */
    public function metrics(Request $request)
    {
        $period = $request->get('period', '30'); // 7, 30, 90, 365 jours
        
        // Métriques utilisateurs
        $userMetrics = $this->getUserMetrics($period);
        
        // Métriques événements
        $eventMetrics = $this->getEventMetrics($period);
        
        // Métriques financières
        $financialMetrics = $this->getFinancialMetrics($period);
        
        // Performance système
        $systemMetrics = $this->getSystemMetrics();
        
        // Top organisations
        $topOrganizations = $this->getTopOrganizations($period);
        
        // Rétention utilisateurs
        $retentionData = $this->getRetentionData($period);
        
        return view('admin.metrics', compact(
            'userMetrics',
            'eventMetrics',
            'financialMetrics',
            'systemMetrics',
            'topOrganizations',
            'retentionData',
            'period'
        ));
    }
    
    /**
     * Gestion des logs système
     */
    public function logs(Request $request)
    {
        $query = DB::table('organization_logs')
                  ->join('organizations', 'organization_logs.organization_id', '=', 'organizations.id')
                  ->leftJoin('saas_users', 'organization_logs.user_id', '=', 'saas_users.id')
                  ->select(
                      'organization_logs.*',
                      'organizations.org_name',
                      'organizations.org_key',
                      'saas_users.first_name',
                      'saas_users.last_name',
                      'saas_users.email'
                  );
        
        // Filtres
        if ($request->has('organization') && $request->organization) {
            $query->where('organizations.id', $request->organization);
        }
        
        if ($request->has('action') && $request->action) {
            $query->where('organization_logs.action', $request->action);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('organization_logs.created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('organization_logs.created_at', '<=', $request->date_to);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('organization_logs.description', 'like', "%{$search}%")
                  ->orWhere('organizations.org_name', 'like', "%{$search}%")
                  ->orWhere('saas_users.email', 'like', "%{$search}%");
            });
        }
        
        $logs = $query->orderBy('organization_logs.created_at', 'desc')
                     ->paginate(50);
        
        // Organisations pour le filtre
        $organizations = DB::table('organizations')
                          ->select('id', 'org_name')
                          ->orderBy('org_name')
                          ->get();
        
        // Actions pour le filtre
        $actions = DB::table('organization_logs')
                    ->select('action')
                    ->distinct()
                    ->orderBy('action')
                    ->pluck('action');
        
        return view('admin.logs', compact('logs', 'organizations', 'actions'));
    }
    
    /**
     * Gestion des templates de base de données
     */
    public function templates()
    {
        $templates = DB::table('database_templates')
                      ->orderBy('org_type')
                      ->orderBy('template_version', 'desc')
                      ->get();
        
        $orgTypes = ['jci', 'rotary', 'lions', 'association', 'company', 'other'];
        
        return view('admin.templates', compact('templates', 'orgTypes'));
    }
    
    /**
     * Créer un nouveau template
     */
    public function createTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:100',
            'org_type' => 'required|in:jci,rotary,lions,association,company,other',
            'template_version' => 'required|string|max:10',
            'sql_structure' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Vérifier que la version n'existe pas déjà
        $exists = DB::table('database_templates')
                   ->where('org_type', $request->org_type)
                   ->where('template_version', $request->template_version)
                   ->exists();
        
        if ($exists) {
            return back()->withErrors(['template_version' => 'Cette version existe déjà pour ce type d\'organisation.'])->withInput();
        }
        
        DB::table('database_templates')->insert([
            'template_name' => $request->template_name,
            'org_type' => $request->org_type,
            'template_version' => $request->template_version,
            'sql_structure' => $request->sql_structure,
            'is_active' => 1,
            'created_at' => now()
        ]);
        
        return back()->with('success', 'Template créé avec succès !');
    }
    
    /**
     * Mettre à jour un template
     */
    public function updateTemplate(Request $request, $id)
    {
        $template = DB::table('database_templates')->where('id', $id)->first();
        
        if (!$template) {
            abort(404, 'Template non trouvé');
        }
        
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:100',
            'sql_structure' => 'required|string',
            'is_active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        DB::table('database_templates')->where('id', $id)->update([
            'template_name' => $request->template_name,
            'sql_structure' => $request->sql_structure,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);
        
        return back()->with('success', 'Template mis à jour avec succès !');
    }
    
    /**
     * Maintenance et outils système
     */
    public function maintenance(Request $request)
    {
        if ($request->isMethod('post')) {
            $action = $request->input('action');
            
            switch ($action) {
                case 'clear_cache':
                    Cache::flush();
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    $message = 'Cache vidé avec succès !';
                    break;
                    
                case 'optimize':
                    Artisan::call('optimize');
                    $message = 'Application optimisée !';
                    break;
                    
                case 'backup_db':
                    $this->createDatabaseBackup();
                    $message = 'Sauvegarde de la base de données créée !';
                    break;
                    
                case 'cleanup_logs':
                    $this->cleanupOldLogs();
                    $message = 'Anciens logs supprimés !';
                    break;
                    
                default:
                    $message = 'Action non reconnue.';
            }
            
            return back()->with('success', $message);
        }
        
        // Informations système
        $systemInfo = $this->getSystemInfo();
        
        // Taille des logs
        $logSizes = $this->getLogSizes();
        
        // Sauvegardes disponibles
        $backups = $this->getAvailableBackups();
        
        return view('admin.maintenance', compact('systemInfo', 'logSizes', 'backups'));
    }
    
    /**
     * Gestion des paramètres système
     */
    public function systemSettings(Request $request)
    {
        if ($request->isMethod('post')) {
            $settings = $request->input('settings', []);
            
            foreach ($settings as $key => $value) {
                DB::table('system_settings')
                  ->updateOrInsert(
                      ['setting_key' => $key],
                      [
                          'setting_value' => $value,
                          'updated_at' => now()
                      ]
                  );
            }
            
            return back()->with('success', 'Paramètres mis à jour avec succès !');
        }
        
        $settings = DB::table('system_settings')
                     ->pluck('setting_value', 'setting_key');
        
        return view('admin.system_settings', compact('settings'));
    }
    
    /**
     * Rapports et exports
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('type', 'organizations');
        $period = $request->get('period', '30');
        $format = $request->get('format', 'view');
        
        $data = [];
        
        switch ($reportType) {
            case 'organizations':
                $data = $this->getOrganizationsReport($period);
                break;
                
            case 'revenue':
                $data = $this->getRevenueReport($period);
                break;
                
            case 'usage':
                $data = $this->getUsageReport($period);
                break;
                
            case 'events':
                $data = $this->getEventsReport($period);
                break;
        }
        
        if ($format === 'csv') {
            return $this->exportToCsv($data, $reportType);
        }
        
        if ($format === 'pdf') {
            return $this->exportToPdf($data, $reportType);
        }
        
        return view('admin.reports', compact('data', 'reportType', 'period'));
    }
    
    // ============================================
    // MÉTHODES PRIVÉES
    // ============================================
    
    /**
     * Obtenir les statistiques générales
     */
    private function getGeneralStats()
    {
        return [
            'total_organizations' => DB::table('organizations')->count(),
            'active_organizations' => DB::table('organizations')->where('subscription_status', 'active')->count(),
            'trial_organizations' => DB::table('organizations')->where('subscription_status', 'trial')->count(),
            'total_users' => DB::table('saas_users')->count(),
            'active_users' => DB::table('saas_users')->where('is_active', 1)->count(),
            'total_revenue' => $this->calculateTotalRevenue(),
            'monthly_revenue' => $this->calculateMonthlyRevenue(),
            'total_events' => $this->countTotalEvents(),
            'total_registrations' => $this->countTotalRegistrations(),
            'avg_events_per_org' => round($this->countTotalEvents() / max(1, DB::table('organizations')->count()), 2),
            'conversion_rate' => $this->calculateConversionRate()
        ];
    }
    
    /**
     * Obtenir la croissance mensuelle
     */
    private function getMonthlyGrowth()
    {
        $growth = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            $growth[] = [
                'month' => $date->format('M Y'),
                'organizations' => DB::table('organizations')
                                   ->whereMonth('created_at', $date->month)
                                   ->whereYear('created_at', $date->year)
                                   ->count(),
                'users' => DB::table('saas_users')
                            ->whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)
                            ->count(),
                'revenue' => $this->calculateMonthlyRevenueForDate($date)
            ];
        }
        
        return $growth;
    }
    
    /**
     * Obtenir les revenus par plan
     */
    private function getRevenueByPlan()
    {
        $plans = DB::table('subscription_plans')->get();
        $revenueByPlan = [];
        
        foreach ($plans as $plan) {
            $orgCount = DB::table('organizations')
                         ->where('subscription_plan', $plan->plan_code)
                         ->where('subscription_status', 'active')
                         ->count();
            
            $revenueByPlan[] = [
                'plan_name' => $plan->plan_name,
                'plan_code' => $plan->plan_code,
                'organizations' => $orgCount,
                'monthly_revenue' => $orgCount * $plan->monthly_price,
                'yearly_revenue' => $orgCount * $plan->yearly_price
            ];
        }
        
        return $revenueByPlan;
    }
    
    /**
     * Obtenir les alertes système
     */
    private function getSystemAlerts()
    {
        $alerts = [];
        
        // Organisations avec abonnement expiré
        $expiredCount = DB::table('organizations')
                         ->where('subscription_status', 'expired')
                         ->count();
        
        if ($expiredCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$expiredCount} organisation(s) avec abonnement expiré",
                'action' => route('admin.organizations.index', ['subscription_status' => 'expired'])
            ];
        }
        
        // Essais gratuits se terminant bientôt
        $trialEndingSoon = DB::table('organizations')
                            ->where('subscription_status', 'trial')
                            ->where('trial_ends_at', '<=', now()->addDays(3))
                            ->count();
        
        if ($trialEndingSoon > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$trialEndingSoon} essai(s) gratuit(s) se terminant dans 3 jours",
                'action' => route('admin.organizations.index', ['subscription_status' => 'trial'])
            ];
        }
        
        // Espace disque faible (simulation)
        $diskUsage = disk_free_space('/') / disk_total_space('/') * 100;
        if ($diskUsage > 85) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Espace disque faible (" . round(100 - $diskUsage, 1) . "% utilisé)",
                'action' => route('admin.maintenance')
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Calculer le revenu total
     */
    private function calculateTotalRevenue()
    {
        // Simulation basée sur les abonnements actifs
        $totalRevenue = 0;
        
        $plans = DB::table('subscription_plans')->get();
        
        foreach ($plans as $plan) {
            $orgCount = DB::table('organizations')
                         ->where('subscription_plan', $plan->plan_code)
                         ->where('subscription_status', 'active')
                         ->count();
            
            $totalRevenue += $orgCount * $plan->monthly_price;
        }
        
        return $totalRevenue;
    }
    
    /**
     * Calculer le revenu mensuel
     */
    private function calculateMonthlyRevenue()
    {
        return $this->calculateTotalRevenue(); // Simplification
    }
    
    /**
     * Calculer le revenu mensuel pour une date donnée
     */
    private function calculateMonthlyRevenueForDate($date)
    {
        // Logique similaire mais pour une date spécifique
        return rand(10000, 50000); // Simulation
    }
    
    /**
     * Compter le total des événements
     */
    private function countTotalEvents()
    {
        $total = 0;
        $organizations = DB::table('organizations')->get();
        
        foreach ($organizations as $org) {
            try {
                $count = DB::select("SELECT COUNT(*) as count FROM `{$org->database_name}`.events")[0]->count ?? 0;
                $total += $count;
            } catch (\Exception $e) {
                // Base de données non accessible
            }
        }
        
        return $total;
    }
    
    /**
     * Compter le total des inscriptions
     */
    private function countTotalRegistrations()
    {
        $total = 0;
        $organizations = DB::table('organizations')->get();
        
        foreach ($organizations as $org) {
            try {
                $count = DB::select("SELECT COUNT(*) as count FROM `{$org->database_name}`.registrations")[0]->count ?? 0;
                $total += $count;
            } catch (\Exception $e) {
                // Base de données non accessible
            }
        }
        
        return $total;
    }
    
    /**
     * Calculer le taux de conversion
     */
    private function calculateConversionRate()
    {
        $totalOrgs = DB::table('organizations')->count();
        $activeOrgs = DB::table('organizations')->where('subscription_status', 'active')->count();
        
        return $totalOrgs > 0 ? round(($activeOrgs / $totalOrgs) * 100, 2) : 0;
    }
    
    /**
     * Créer une sauvegarde de la base de données
     */
    private function createDatabaseBackup()
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        // Commande mysqldump (adapter selon votre configuration)
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE'),
            $path
        );
        
        exec($command);
    }
    
    /**
     * Nettoyer les anciens logs
     */
    private function cleanupOldLogs()
    {
        // Supprimer les logs de plus de 90 jours
        DB::table('organization_logs')
          ->where('created_at', '<', now()->subDays(90))
          ->delete();
    }
    
    /**
     * Obtenir les informations système
     */
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_free_space' => $this->formatBytes(disk_free_space('/')),
            'disk_total_space' => $this->formatBytes(disk_total_space('/'))
        ];
    }
    
    /**
     * Formater les bytes
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Obtenir la taille des logs
     */
    private function getLogSizes()
    {
        return [
            'organization_logs' => DB::table('organization_logs')->count(),
            'laravel_logs' => file_exists(storage_path('logs/laravel.log')) ? 
                             $this->formatBytes(filesize(storage_path('logs/laravel.log'))) : '0 B'
        ];
    }
    
    /**
     * Obtenir les sauvegardes disponibles
     */
    private function getAvailableBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            return [];
        }
        
        $files = scandir($backupPath);
        $backups = [];
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'filename' => $file,
                    'size' => $this->formatBytes(filesize($backupPath . '/' . $file)),
                    'created_at' => date('Y-m-d H:i:s', filemtime($backupPath . '/' . $file))
                ];
            }
        }
        
        return array_reverse($backups); // Plus récents en premier
    }
}