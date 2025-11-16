<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class OrganizationAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $orgSlug = $request->route('org_slug');
        $user = session('organization_user');
        
        Log::info('=== OrganizationAuth Middleware ===', [
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'route_name' => $request->route()?->getName(),
            'org_slug' => $orgSlug,
            'has_user' => !empty($user)
        ]);

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            Log::info('User not authenticated, redirecting to login', [
                'org_slug' => $orgSlug,
                'route' => $request->route()?->getName(),
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('org.login', ['org_slug' => $orgSlug])
                ->with('error', 'Vous devez vous connecter pour accéder à cette page.');
        }

        // Vérifier que l'organisation de la session correspond à celle de l'URL
        if ($user['org_subdomain'] !== $orgSlug) {
            Log::warning('Organization mismatch in session', [
                'session_org' => $user['org_subdomain'],
                'url_org' => $orgSlug,
                'user_id' => $user['id']
            ]);
            
            session()->forget('organization_user');
            return redirect()->route('org.login', ['org_slug' => $orgSlug])
                ->with('error', 'Session invalide. Veuillez vous reconnecter.');
        }

        // Vérifier si la session n'a pas expiré (8 heures)
        $loginTime = \Carbon\Carbon::parse($user['logged_in_at']);
        if ($loginTime->addHours(8)->isPast()) {
            Log::info('Session expired', [
                'user_id' => $user['id'],
                'login_time' => $user['logged_in_at'],
                'org_slug' => $orgSlug
            ]);
            
            session()->forget('organization_user');
            return redirect()->route('org.login', ['org_slug' => $orgSlug])
                ->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        // Configurer la connexion à la base de données de l'organisation
        if (!$this->configureTenantDatabase($user['database_name'])) {
            Log::error('Failed to configure tenant database in middleware', [
                'database_name' => $user['database_name'],
                'user_id' => $user['id']
            ]);
            
            return redirect()->route('org.login', ['org_slug' => $orgSlug])
                ->with('error', 'Erreur de configuration. Veuillez vous reconnecter.');
        }

        // Vérifier que l'utilisateur existe toujours et est actif
        try {
            $currentUser = DB::connection('tenant')
                ->table('users')
                ->where('id', $user['id'])
                ->where('is_active', true)
                ->first();

            if (!$currentUser) {
                Log::warning('User no longer exists or is inactive', [
                    'user_id' => $user['id'],
                    'org_slug' => $orgSlug
                ]);
                
                session()->forget('organization_user');
                return redirect()->route('org.login', ['org_slug' => $orgSlug])
                    ->with('error', 'Votre compte n\'est plus actif. Contactez l\'administrateur.');
            }
        } catch (\Exception $e) {
            Log::error('Error checking user status in middleware', [
                'user_id' => $user['id'],
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('org.login', ['org_slug' => $orgSlug])
                ->with('error', 'Erreur lors de la vérification de votre compte.');
        }

        // Partager les données utilisateur avec les vues
        view()->share('currentUser', $user);
        view()->share('orgSlug', $orgSlug);

        return $next($request);
    }

    /**
     * Configurer la connexion à la base de données tenant
     */
    private function configureTenantDatabase($databaseName)
    {
        try {
            // En local, utiliser les identifiants de la connexion principale
            $mysqlConfig = config('database.connections.mysql');
            
            $tenantConfig = [
                'driver' => 'mysql',
                'host' => $mysqlConfig['host'],
                'port' => $mysqlConfig['port'],
                'database' => $databaseName,
                'username' => $mysqlConfig['username'],
                'password' => $mysqlConfig['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ];

            Config::set('database.connections.tenant', $tenantConfig);
            DB::purge('tenant');
            
            // Tester la connexion
            DB::connection('tenant')->getPdo();
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Tenant database configuration failed in middleware', [
                'database_name' => $databaseName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
