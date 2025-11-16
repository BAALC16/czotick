<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuperAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        $superAdmin = session('super_admin');
        
        if (!$superAdmin) {
            return $this->handleUnauthorized($request);
        }
        
        // Super Admin avec permissions '*' a accès à tout
        if (in_array('*', $superAdmin['permissions'] ?? [])) {
            return $next($request);
        }
        
        // Vérifier la permission spécifique si fournie
        if ($permission && !$this->hasPermission($superAdmin, $permission)) {
            return $this->handleForbidden($request, $permission);
        }
        
        // Vérifier les permissions basées sur la route
        $routePermission = $this->getRoutePermission($request);
        if ($routePermission && !$this->hasPermission($superAdmin, $routePermission)) {
            return $this->handleForbidden($request, $routePermission);
        }
        
        return $next($request);
    }
    
    /**
     * Vérifier si le Super Admin a une permission
     */
    protected function hasPermission(array $superAdmin, string $permission): bool
    {
        $permissions = $superAdmin['permissions'] ?? [];
        
        // Vérifier permission exacte
        if (in_array($permission, $permissions)) {
            return true;
        }
        
        // Vérifier permission wildcard (ex: "organizations.*" pour "organizations.create")
        $parts = explode('.', $permission);
        for ($i = count($parts) - 1; $i > 0; $i--) {
            $wildcard = implode('.', array_slice($parts, 0, $i)) . '.*';
            if (in_array($wildcard, $permissions)) {
                return true;
            }
        }
        
        // Vérifier permissions de niveau supérieur
        return $this->checkHierarchicalPermissions($permissions, $permission);
    }
    
    /**
     * Vérifier les permissions hiérarchiques
     */
    protected function checkHierarchicalPermissions(array $userPermissions, string $requiredPermission): bool
    {
        $hierarchicalPermissions = [
            // Super permissions
            'system.admin' => ['organizations.*', 'users.*', 'billing.*', 'reports.*', 'system.*'],
            'system.manage' => ['organizations.view', 'organizations.edit', 'users.view', 'reports.view'],
            
            // Permissions organisations
            'organizations.admin' => ['organizations.*'],
            'organizations.manage' => ['organizations.view', 'organizations.edit', 'organizations.suspend'],
            
            // Permissions utilisateurs
            'users.admin' => ['users.*'],
            'users.manage' => ['users.view', 'users.edit', 'users.suspend'],
            
            // Permissions facturation
            'billing.admin' => ['billing.*'],
            'billing.manage' => ['billing.view', 'billing.invoices', 'billing.process'],
            
            // Permissions rapports
            'reports.admin' => ['reports.*'],
            'reports.manage' => ['reports.view', 'reports.export'],
            
            // Permissions système
            'system.full' => ['system.*', 'logs.*', 'templates.*', 'backups.*'],
            'system.monitor' => ['system.health', 'logs.view', 'monitoring.*'],
        ];
        
        foreach ($userPermissions as $userPerm) {
            if (isset($hierarchicalPermissions[$userPerm])) {
                $allowedPerms = $hierarchicalPermissions[$userPerm];
                if (in_array($requiredPermission, $allowedPerms)) {
                    return true;
                }
                
                // Vérifier les wildcards dans les permissions hiérarchiques
                foreach ($allowedPerms as $allowedPerm) {
                    if (str_ends_with($allowedPerm, '.*')) {
                        $prefix = str_replace('.*', '', $allowedPerm);
                        if (str_starts_with($requiredPermission, $prefix)) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Obtenir la permission requise basée sur la route
     */
    protected function getRoutePermission(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }
        
        $routeName = $route->getName();
        if (!$routeName) {
            return null;
        }
        
        // Mapping des routes vers les permissions
        $routePermissions = [
            // Organisations
            'super-admin.organizations.index' => 'organizations.view',
            'super-admin.organizations.create' => 'organizations.create',
            'super-admin.organizations.store' => 'organizations.create',
            'super-admin.organizations.show' => 'organizations.view',
            'super-admin.organizations.edit' => 'organizations.edit',
            'super-admin.organizations.update' => 'organizations.edit',
            'super-admin.organizations.delete' => 'organizations.delete',
            'super-admin.organizations.suspend' => 'organizations.suspend',
            'super-admin.organizations.activate' => 'organizations.activate',
            'super-admin.organizations.bulk' => 'organizations.bulk',
            'super-admin.organizations.database' => 'organizations.database',
            'super-admin.organizations.migrate' => 'organizations.migrate',
            'super-admin.organizations.inspect-database' => 'organizations.inspect',
            
            // Utilisateurs
            'super-admin.users.index' => 'users.view',
            'super-admin.users.show' => 'users.view',
            'super-admin.users.edit' => 'users.edit',
            'super-admin.users.update' => 'users.edit',
            'super-admin.users.toggle-status' => 'users.suspend',
            'super-admin.users.impersonate' => 'users.impersonate',
            
            // Métriques et monitoring
            'super-admin.metrics' => 'reports.view',
            'super-admin.metrics.export' => 'reports.export',
            'super-admin.health' => 'system.health',
            'super-admin.monitoring.realtime' => 'monitoring.view',
            'super-admin.monitoring.performance' => 'monitoring.performance',
            'super-admin.monitoring.errors' => 'monitoring.errors',
            
            // Logs
            'super-admin.logs' => 'logs.view',
            'super-admin.logs.clear' => 'logs.clear',
            
            // Templates
            'super-admin.templates' => 'templates.view',
            'super-admin.templates.create' => 'templates.create',
            'super-admin.templates.update' => 'templates.edit',
            'super-admin.templates.delete' => 'templates.delete',
            
            // Paramètres
            'super-admin.settings' => 'system.settings',
            'super-admin.settings.update' => 'system.settings',
            
            // Sauvegardes
            'super-admin.backups' => 'backups.view',
            'super-admin.backups.create' => 'backups.create',
            'super-admin.backups.restore' => 'backups.restore',
            
            // Analytics
            'super-admin.analytics.global' => 'analytics.global',
            'super-admin.analytics.revenue' => 'analytics.revenue',
            'super-admin.analytics.users' => 'analytics.users',
            
            // Facturation
            'super-admin.billing.index' => 'billing.view',
            'super-admin.billing.invoices' => 'billing.invoices',
            'super-admin.billing.plans' => 'billing.plans',
            'super-admin.billing.generate-invoice' => 'billing.generate',
        ];
        
        return $routePermissions[$routeName] ?? null;
    }
    
    /**
     * Gérer l'accès non autorisé
     */
    protected function handleUnauthorized(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé',
                'redirect' => route('super-admin.login')
            ], 401);
        }
        
        return redirect()->route('super-admin.login');
    }
    
    /**
     * Gérer l'accès interdit
     */
    protected function handleForbidden(Request $request, string $permission)
    {
        $superAdmin = session('super_admin');
        
        // Log de tentative d'accès non autorisé
        Log::warning('Super Admin permission denied', [
            'email' => $superAdmin['email'] ?? 'unknown',
            'required_permission' => $permission,
            'user_permissions' => $superAdmin['permissions'] ?? [],
            'route' => $request->route()->getName(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'timestamp' => Carbon::now()
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Permission insuffisante',
                'required_permission' => $permission,
                'code' => 'INSUFFICIENT_PERMISSION'
            ], 403);
        }
        
        return redirect()->route('super-admin.dashboard')
            ->with('error', 'Vous n\'avez pas la permission d\'accéder à cette ressource.');
    }
    
    /**
     * Vérifier les permissions (méthode statique pour les controllers)
     */
    public static function check(string $permission): bool
    {
        $superAdmin = session('super_admin');
        
        if (!$superAdmin) {
            return false;
        }
        
        // Super Admin avec '*' a toutes les permissions
        if (in_array('*', $superAdmin['permissions'] ?? [])) {
            return true;
        }
        
        $instance = new self();
        return $instance->hasPermission($superAdmin, $permission);
    }
    
    /**
     * Obtenir toutes les permissions disponibles
     */
    public static function getAllPermissions(): array
    {
        return [
            'organizations' => [
                'organizations.view' => 'Voir les organisations',
                'organizations.create' => 'Créer des organisations',
                'organizations.edit' => 'Modifier les organisations',
                'organizations.delete' => 'Supprimer les organisations',
                'organizations.suspend' => 'Suspendre les organisations',
                'organizations.activate' => 'Activer les organisations',
                'organizations.bulk' => 'Actions en lot sur les organisations',
                'organizations.database' => 'Gérer les bases de données',
                'organizations.migrate' => 'Migrer les bases de données',
                'organizations.inspect' => 'Inspecter les bases de données',
                'organizations.*' => 'Toutes les permissions organisations',
            ],
            
            'users' => [
                'users.view' => 'Voir les utilisateurs',
                'users.edit' => 'Modifier les utilisateurs',
                'users.suspend' => 'Suspendre les utilisateurs',
                'users.impersonate' => 'Se connecter comme utilisateur',
                'users.*' => 'Toutes les permissions utilisateurs',
            ],
            
            'billing' => [
                'billing.view' => 'Voir la facturation',
                'billing.invoices' => 'Gérer les factures',
                'billing.plans' => 'Gérer les plans',
                'billing.generate' => 'Générer des factures',
                'billing.*' => 'Toutes les permissions facturation',
            ],
            
            'reports' => [
                'reports.view' => 'Voir les rapports',
                'reports.export' => 'Exporter les rapports',
                'reports.*' => 'Toutes les permissions rapports',
            ],
            
            'system' => [
                'system.health' => 'Vérifier la santé du système',
                'system.settings' => 'Modifier les paramètres système',
                'logs.view' => 'Voir les logs',
                'logs.clear' => 'Effacer les logs',
                'templates.view' => 'Voir les templates',
                'templates.create' => 'Créer des templates',
                'templates.edit' => 'Modifier les templates',
                'templates.delete' => 'Supprimer des templates',
                'backups.view' => 'Voir les sauvegardes',
                'backups.create' => 'Créer des sauvegardes',
                'backups.restore' => 'Restaurer des sauvegardes',
                'monitoring.view' => 'Voir le monitoring',
                'monitoring.performance' => 'Monitoring performance',
                'monitoring.errors' => 'Monitoring erreurs',
                'analytics.global' => 'Analytics globales',
                'analytics.revenue' => 'Analytics revenus',
                'analytics.users' => 'Analytics utilisateurs',
                'system.*' => 'Toutes les permissions système',
            ],
        ];
    }
    
    /**
     * Obtenir les permissions par rôle
     */
    public static function getPermissionsByRole(string $role): array
    {
        $rolePermissions = [
            'super_admin' => ['*'], // Toutes les permissions
            
            'admin' => [
                'organizations.*',
                'users.view',
                'users.edit',
                'users.suspend',
                'billing.*',
                'reports.*',
                'system.health',
                'logs.view',
                'monitoring.*',
                'analytics.*',
            ],
            
            'manager' => [
                'organizations.view',
                'organizations.edit',
                'users.view',
                'billing.view',
                'reports.view',
                'reports.export',
                'system.health',
                'logs.view',
            ],
            
            'viewer' => [
                'organizations.view',
                'users.view',
                'billing.view',
                'reports.view',
                'system.health',
            ],
            
            'support' => [
                'organizations.view',
                'organizations.suspend',
                'organizations.activate',
                'users.view',
                'users.suspend',
                'billing.view',
                'reports.view',
                'logs.view',
                'monitoring.*',
                'system.health',
            ],
        ];
        
        return $rolePermissions[$role] ?? [];
    }
    
    /**
     * Assigner des permissions à un Super Admin
     */
    public static function assignPermissions(string $email, array $permissions)
    {
        $superAdmin = session('super_admin');
        
        if ($superAdmin && $superAdmin['email'] === $email) {
            // Mettre à jour les permissions en session
            $superAdmin['permissions'] = $permissions;
            session(['super_admin' => $superAdmin]);
            
            Log::info('Permissions Super Admin mises à jour', [
                'email' => $email,
                'permissions' => $permissions,
                'updated_by' => $superAdmin['email'],
            ]);
        }
    }
}