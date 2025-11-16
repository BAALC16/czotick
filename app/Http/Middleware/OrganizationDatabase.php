<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class OrganizationDatabase
{
    /**
     * Handle an incoming request.
     * Configure la connexion à la base de données de l'organisation
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = session('organization_user');
        $orgSlug = $request->route('org_slug');

        if (!$user) {
            Log::debug('No user session found in OrganizationDatabase middleware');
            return $next($request);
        }

        if (!isset($user['database_name'])) {
            Log::error('Database name not found in user session', [
                'user_id' => $user['id'] ?? 'unknown',
                'org_slug' => $orgSlug
            ]);
            return $next($request);
        }

        try {
            $this->configureTenantDatabase($user['database_name']);
            
            Log::debug('Tenant database configured in middleware', [
                'database_name' => $user['database_name'],
                'user_id' => $user['id']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to configure tenant database in middleware', [
                'database_name' => $user['database_name'],
                'user_id' => $user['id'],
                'error' => $e->getMessage()
            ]);
            
            // Ne pas bloquer la requête, mais logger l'erreur
        }

        return $next($request);
    }

    /**
     * Configurer la connexion à la base de données tenant
     */
    private function configureTenantDatabase($databaseName)
    {
        // Vérifier si la configuration est déjà en place
        $currentConfig = config('database.connections.tenant.database');
        if ($currentConfig === $databaseName) {
            return; // Déjà configurée
        }

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
        
        // Purger la connexion existante pour forcer la reconnexion
        DB::purge('tenant');
        
        Log::debug('Tenant database connection configured', [
            'database' => $databaseName
        ]);
    }
}