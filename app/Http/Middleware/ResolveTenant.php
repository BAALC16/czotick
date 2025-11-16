<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Organization;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $orgSlug = $request->route('org_slug');
        
        if (!$orgSlug) {
            return $next($request);
        }
        
        // Chercher l'organisation sur la base de données principale
        $organization = $this->findOrganization($orgSlug);
        
        if (!$organization) {
            abort(404, 'Organisation non trouvée');
        }
        
        // La vérification de l'abonnement se fait maintenant au niveau des événements
        // Plus besoin de vérifier ici car la souscription est gérée au niveau des événements
        
        // Stocker l'organisation dans le contexte
        app()->instance('current.organization', $organization);
        
        // Configurer la connexion tenant avec utilisateur dynamique
        $this->configureTenantDatabase($organization);
        
        return $next($request);
    }
    
    /**
     * Chercher l'organisation sur la base de données principale
     */
    private function findOrganization($orgSlug)
    {
        return Organization::on('mysql')
                          ->where('org_key', $orgSlug)
                          ->orWhere('subdomain', $orgSlug)
                          ->first();
    }
    
    /**
     * Configurer la connexion tenant avec utilisateur et mot de passe dynamiques
     */
    private function configureTenantDatabase($organization)
    {
        if (empty($organization->database_name)) {
            abort(500, 'Configuration de base de données manquante pour cette organisation');
        }
        
        // En local, utiliser les identifiants de la connexion principale
        $mysqlConfig = config('database.connections.mysql');
        
        // Configuration dynamique de la connexion tenant
        $tenantConfig = [
            'driver' => 'mysql',
            'host' => $mysqlConfig['host'],
            'port' => $mysqlConfig['port'],
            'database' => $organization->database_name,
            'username' => $mysqlConfig['username'],
            'password' => $mysqlConfig['password'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ];
        
        // Définir la configuration de connexion
        Config::set('database.connections.tenant', $tenantConfig);
        
        // Purger les connexions existantes
        DB::purge('tenant');
        
        try {
            // Tester la connexion
            DB::connection('tenant')->getPdo();
            
            \Log::info('Connexion tenant établie avec succès', [
                'organization_id' => $organization->id,
                'database_name' => $organization->database_name,
                'username' => $organization->database_name
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur de connexion à la base de données tenant', [
                'organization_id' => $organization->id,
                'database_name' => $organization->database_name,
                'username' => $organization->database_name,
                'error' => $e->getMessage()
            ]);
            
            // En développement, montrer l'erreur détaillée
            if (app()->environment('local')) {
                abort(500, 'Erreur de connexion tenant: ' . $e->getMessage());
            }
            
            abort(500, 'Impossible de se connecter à la base de données de l\'organisation');
        }
    }
    
    // Méthode supprimée - la souscription est gérée au niveau des événements
}
