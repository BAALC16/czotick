<?php

// ============================================================================
// app/Helpers/AuthHelper.php
// ============================================================================

namespace App\Helpers;

class AuthHelper 
{
    /**
     * Récupérer l'utilisateur de l'organisation connecté
     */
    public static function organizationUser()
    {
        return session('organization_user');
    }
    
    /**
     * Vérifier si un utilisateur organisation est connecté
     */
    public static function isOrganizationLoggedIn()
    {
        return session()->has('organization_user');
    }
    
    /**
     * Récupérer l'ID de l'organisation courante
     */
    public static function organizationId()
    {
        $user = self::organizationUser();
        return $user ? $user['organization_id'] : null;
    }
    
    /**
     * Récupérer le nom de la base de données de l'organisation
     */
    public static function organizationDatabase()
    {
        $user = self::organizationUser();
        return $user ? $user['database_name'] : null;
    }
    
    /**
     * Configurer dynamiquement la connexion à la base de données de l'organisation
     */
    public static function setOrganizationConnection()
    {
        $database = self::organizationDatabase();
        if ($database) {
            config(['database.connections.org.database' => $database]);
        }
    }
    
    /**
     * Récupérer le nom de l'organisation
     */
    public static function organizationName()
    {
        $user = self::organizationUser();
        return $user ? $user['org_name'] : null;
    }
    
    /**
     * Récupérer le plan d'abonnement
     */
    public static function subscriptionPlan()
    {
        $user = self::organizationUser();
        return $user ? $user['subscription_plan'] : null;
    }
    
    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public static function hasRole($role)
    {
        $user = self::organizationUser();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Vérifier si l'utilisateur est propriétaire
     */
    public static function isOwner()
    {
        return self::hasRole('owner');
    }
    
    /**
     * Vérifier si l'utilisateur est admin
     */
    public static function isAdmin()
    {
        return self::hasRole('admin') || self::isOwner();
    }
}

// ============================================================================
// app/Http/Middleware/OrganizationAuth.php
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!session()->has('organization_user')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
            
            return redirect()->route('organization.login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = session('organization_user');

        // Vérifier que l'organisation est toujours active
        $organization = DB::connection('saas_master')
            ->table('organizations')
            ->where('id', $user['organization_id'])
            ->where('subscription_status', 'active')
            ->first();

        if (!$organization) {
            session()->forget('organization_user');
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Organisation inactive'], 403);
            }
            
            return redirect()->route('organization.login')
                ->with('error', 'Votre organisation n\'est plus active. Contactez le support.');
        }

        // Vérifier que l'utilisateur est toujours actif
        $activeUser = DB::connection('saas_master')
            ->table('saas_users')
            ->where('id', $user['id'])
            ->where('is_active', true)
            ->first();

        if (!$activeUser) {
            session()->forget('organization_user');
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Utilisateur inactif'], 403);
            }
            
            return redirect()->route('organization.login')
                ->with('error', 'Votre compte n\'est plus actif. Contactez l\'administrateur.');
        }

        // Ajouter les informations utilisateur à la requête
        $request->attributes->set('organization_user', $user);
        $request->attributes->set('organization', $organization);

        return $next($request);
    }
}

// ============================================================================
// app/Http/Middleware/OrganizationGuest.php
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrganizationGuest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('organization_user')) {
            return redirect()->route('organization.dashboard');
        }

        return $next($request);
    }
}

// ============================================================================
// app/Http/Middleware/SetOrganizationDatabase.php
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;

class SetOrganizationDatabase
{
    public function handle(Request $request, Closure $next)
    {
        if (AuthHelper::isOrganizationLoggedIn()) {
            AuthHelper::setOrganizationConnection();
        }
        
        return $next($request);
    }
}

// ============================================================================
// app/Http/Middleware/OrganizationRole.php
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;

class OrganizationRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!AuthHelper::isOrganizationLoggedIn()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
            return redirect()->route('organization.login');
        }

        $user = AuthHelper::organizationUser();
        $userRole = $user['role'];

        // Hiérarchie des rôles
        $roleHierarchy = [
            'user' => 1,
            'manager' => 2,
            'admin' => 3,
            'owner' => 4
        ];

        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = max(array_map(fn($role) => $roleHierarchy[$role] ?? 0, $roles));

        if ($userLevel < $requiredLevel) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Permissions insuffisantes'], 403);
            }
            
            return redirect()->route('organization.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}

// ============================================================================
// Configuration database.php - Section complète
// ============================================================================

/*
Dans config/database.php, ajouter ces connexions dans le tableau 'connections' :

'org' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => '', // Sera défini dynamiquement
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],

'saas_master' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('SAAS_MASTER_DB', 'saas_master'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],

*/

// ============================================================================
// app/Http/Kernel.php - Section middlewares
// ============================================================================

/*
Dans app/Http/Kernel.php, ajouter dans le tableau $routeMiddleware :

protected $routeMiddleware = [
    // ... autres middlewares existants ...
    'organization.auth' => \App\Http\Middleware\OrganizationAuth::class,
    'organization.guest' => \App\Http\Middleware\OrganizationGuest::class,
    'organization.db' => \App\Http\Middleware\SetOrganizationDatabase::class,
    'organization.role' => \App\Http\Middleware\OrganizationRole::class,
];

*/

// ============================================================================
// app/Providers/AppServiceProvider.php
// ============================================================================

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\AuthHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Partager les données utilisateur avec toutes les vues
        View::composer('*', function ($view) {
            if (AuthHelper::isOrganizationLoggedIn()) {
                $view->with([
                    'currentUser' => AuthHelper::organizationUser(),
                    'organizationName' => AuthHelper::organizationName(),
                    'subscriptionPlan' => AuthHelper::subscriptionPlan(),
                ]);
            }
        });

        // Partager les helpers avec toutes les vues
        View::share('authHelper', AuthHelper::class);
    }
}

// ============================================================================
// Variables d'environnement .env
// ============================================================================

/*
Ajouter dans votre fichier .env :

SAAS_MASTER_DB=saas_master
ORGANIZATION_SESSION_LIFETIME=120
ORGANIZATION_SESSION_SECURE=true
ORGANIZATION_REMEMBER_LIFETIME=43200

# Optionnel : Configuration de cache pour les sessions organisation
ORGANIZATION_CACHE_DRIVER=redis
ORGANIZATION_CACHE_PREFIX=org_
*/