<?php

namespace App\Helpers;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TenantHelper
{
    /**
     * Cache duration en minutes
     */
    const CACHE_DURATION = 60;

    /**
     * Clés de cache
     */
    const CACHE_ORG_PREFIX = 'tenant_org_';
    const CACHE_EVENT_PREFIX = 'tenant_event_';

    /**
     * Organisation et événement actuels (cache en mémoire)
     */
    private static $currentOrganization = null;
    private static $currentEvent = null;
    private static $isResolved = false;
    private static $tenantConnectionName = null;

    /**
     * Obtenir l'organisation actuelle
     *
     * @return Organization|null
     */
    public static function getCurrentOrganization()
    {
        // ✅ PRIORITÉ AU CONTEXTE MANUEL (pour les callbacks)
        if (self::$currentOrganization && self::$isResolved) {
            return self::$currentOrganization;
        }

        // Vérifier d'abord si défini dans le service container
        if (app()->bound('current.organization')) {
            self::$currentOrganization = app('current.organization');
            self::$isResolved = true;
            return self::$currentOrganization;
        }

        if (!self::$isResolved) {
            self::resolveTenantContext();
        }

        return self::$currentOrganization;
    }

    /**
     * Obtenir l'événement actuel
     *
     * @return Event|null
     */
    public static function getCurrentEvent()
    {
        // ✅ PRIORITÉ AU CONTEXTE MANUEL (pour les callbacks)
        if (self::$currentEvent && self::$isResolved) {
            return self::$currentEvent;
        }

        // Vérifier d'abord si défini dans le service container
        if (app()->bound('current.event')) {
            self::$currentEvent = app('current.event');
            self::$isResolved = true;
            return self::$currentEvent;
        }

        if (!self::$isResolved) {
            self::resolveTenantContext();
        }

        return self::$currentEvent;
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Définir l'organisation dans le service container
     */
    public static function setCurrentOrganization($organization)
    {
        self::$currentOrganization = $organization;
        app()->instance('current.organization', $organization);

        if ($organization) {
            self::configureTenantConnection($organization);
            Log::info('Organisation définie dans TenantHelper', [
                'organization_id' => $organization->id,
                'org_key' => $organization->org_key,
                'database_name' => $organization->database_name
            ]);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Définir l'événement dans le service container
     */
    public static function setCurrentEvent($event)
    {
        self::$currentEvent = $event;
        app()->instance('current.event', $event);

        if ($event) {
            Log::info('Événement défini dans TenantHelper', [
                'event_id' => $event->id,
                'event_slug' => $event->event_slug,
                'event_title' => $event->event_title ?? $event->title
            ]);
        }
    }

    /**
     * Résoudre le contexte tenant depuis l'URL ou la requête
     *
     * @return void
     */
    private static function resolveTenantContext()
    {
        // ✅ AMÉLIORATION : Vérifier d'abord le service container
        if (app()->bound('current.organization') && app()->bound('current.event')) {
            self::$currentOrganization = app('current.organization');
            self::$currentEvent = app('current.event');
            self::$isResolved = true;
            return;
        }

        // Récupérer les paramètres de route
        $orgSlug = Route::current()?->parameter('org_slug');
        $eventSlug = Route::current()?->parameter('event_slug');

        // Si pas de paramètres de route, essayer depuis la requête
        if (!$orgSlug) {
            $orgSlug = Request::input('org_slug') ?? Request::segment(1);
        }

        if (!$eventSlug) {
            $eventSlug = Request::input('event_slug') ?? Request::segment(2);
        }

        // Résoudre l'organisation
        if ($orgSlug) {
            self::$currentOrganization = self::resolveOrganization($orgSlug);
            if (self::$currentOrganization) {
                app()->instance('current.organization', self::$currentOrganization);
            }
        }

        // Résoudre l'événement
        if ($eventSlug && self::$currentOrganization) {
            self::$currentEvent = self::resolveEvent($eventSlug, self::$currentOrganization);
            if (self::$currentEvent) {
                app()->instance('current.event', self::$currentEvent);
            }
        }

        self::$isResolved = true;
    }

    /**
     * Résoudre l'organisation par son slug
     *
     * @param string $orgSlug
     * @return Organization|null
     */
    private static function resolveOrganization($orgSlug)
    {
        $cacheKey = self::CACHE_ORG_PREFIX . $orgSlug;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($orgSlug) {
            // Utiliser la connexion mysql (base principale) pour récupérer l'organisation
            $organization = Organization::on('mysql')
                ->where('org_key', $orgSlug)
                ->first();

            if ($organization) {
                Log::info('Organisation résolue depuis la base', [
                    'org_key' => $orgSlug,
                    'organization_id' => $organization->id,
                    'database_name' => $organization->database_name
                ]);
            }

            return $organization;
        });
    }

    /**
     * Résoudre l'événement par son slug et l'organisation
     *
     * @param string $eventSlug
     * @param Organization $organization
     * @return Event|null
     */
    private static function resolveEvent($eventSlug, $organization)
    {
        $cacheKey = self::CACHE_EVENT_PREFIX . $organization->id . '_' . $eventSlug;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($eventSlug, $organization) {
            // Configurer la connexion tenant
            self::configureTenantConnection($organization);

            // Utiliser la connexion tenant pour récupérer l'événement
            $event = Event::on('tenant')
                ->where('event_slug', $eventSlug)
                ->where('is_published', true)
                ->with(['ticketTypes' => function($query) {
                    $query->where('is_active', true)->orderBy('display_order');
                }, 'accessControls'])
                ->first();

            if ($event) {
                Log::info('Événement résolu depuis la base tenant', [
                    'event_slug' => $eventSlug,
                    'event_id' => $event->id,
                    'organization_id' => $organization->id
                ]);
            }

            return $event;
        });
    }

    /**
     * ✅ AMÉLIORATION : Ajout de logs pour le debug
     */
    private static function configureTenantConnection($organization)
    {
        if (self::$tenantConnectionName === $organization->database_name) {
            return; // Déjà configurée
        }

        // Utiliser toujours les identifiants MySQL par défaut (root sans mot de passe)
        // pour toutes les connexions tenant, peu importe l'environnement
        $tenantConfig = [
            'driver' => 'mysql',
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => $organization->database_name,
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
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

        self::$tenantConnectionName = $organization->database_name;

        Log::info('Connexion tenant configurée', [
            'organization_id' => $organization->id,
            'database_name' => $organization->database_name,
            'username' => $tenantConfig['username'],
            'environment' => app()->environment()
        ]);
    }

    /**
     * ✅ AMÉLIORATION : Définir manuellement le contexte tenant avec logs
     *
     * @param Organization|null $organization
     * @param Event|null $event
     * @return void
     */
    public static function setTenantContext($organization = null, $event = null)
    {
        if ($organization) {
            self::setCurrentOrganization($organization);
        }

        if ($event) {
            self::setCurrentEvent($event);
        }

        self::$isResolved = true;

        Log::info('Contexte tenant défini manuellement', [
            'organization' => $organization ? $organization->org_key : null,
            'event' => $event ? $event->event_slug : null
        ]);
    }

    /**
     * Réinitialiser le contexte tenant
     *
     * @return void
     */
    public static function resetTenantContext()
    {
        self::$currentOrganization = null;
        self::$currentEvent = null;
        self::$isResolved = false;
        self::$tenantConnectionName = null;

        // Nettoyer le service container
        if (app()->bound('current.organization')) {
            app()->forgetInstance('current.organization');
        }

        if (app()->bound('current.event')) {
            app()->forgetInstance('current.event');
        }

        // Purger la connexion tenant
        DB::purge('tenant');

        Log::info('Contexte tenant réinitialisé');
    }

    /**
     * Vérifier si le contexte tenant est valide
     *
     * @param bool $requireEvent
     * @return bool
     */
    public static function hasValidTenantContext($requireEvent = false)
    {
        $hasOrg = self::getCurrentOrganization() !== null;

        if (!$requireEvent) {
            return $hasOrg;
        }

        return $hasOrg && self::getCurrentEvent() !== null;
    }

    /**
     * Obtenir l'URL de base pour l'organisation actuelle
     *
     * @return string|null
     */
    public static function getOrganizationBaseUrl()
    {
        $org = self::getCurrentOrganization();

        if (!$org) {
            return null;
        }

        return url('/' . $org->org_key);
    }

    /**
     * Obtenir l'URL de base pour l'événement actuel
     *
     * @return string|null
     */
    public static function getEventBaseUrl()
    {
        $org = self::getCurrentOrganization();
        $event = self::getCurrentEvent();

        if (!$org || !$event) {
            return null;
        }

        return url('/' . $org->org_key . '/' . $event->event_slug);
    }

    /**
     * Générer une URL pour une route dans le contexte tenant actuel
     *
     * @param string $routeName
     * @param array $parameters
     * @return string|null
     */
    public static function tenantRoute($routeName, $parameters = [])
    {
        $org = self::getCurrentOrganization();
        $event = self::getCurrentEvent();

        if (!$org) {
            return null;
        }

        $routeParams = array_merge([
            'org_slug' => $org->org_key
        ], $parameters);

        if ($event) {
            $routeParams['event_slug'] = $event->event_slug;
        }

        return route($routeName, $routeParams);
    }

    /**
     * Vérifier si l'utilisateur a accès à l'organisation
     *
     * @param int|null $userId
     * @return bool
     */
    public static function hasOrganizationAccess($userId = null)
    {
        $org = self::getCurrentOrganization();

        if (!$org) {
            return false;
        }

        // Si pas d'utilisateur spécifié, utiliser l'utilisateur connecté
        if ($userId === null) {
            $userId = auth()->id();
        }

        // Si pas d'utilisateur connecté, autoriser l'accès public pour les événements
        if (!$userId) {
            return true;
        }

        // Vérifier si l'utilisateur est membre de l'organisation dans la base principale
        return $org->users()->where('user_id', $userId)->exists();
    }

    /**
     * Vérifier si l'événement est accessible
     *
     * @return bool
     */
    public static function isEventAccessible()
    {
        $event = self::getCurrentEvent();

        if (!$event) {
            return false;
        }

        // Vérifier si l'événement est publié
        if (!$event->is_published) {
            return false;
        }

        // Vérifier les dates d'inscription si définies
        $now = now();

        if ($event->registration_start_date && $now->lt($event->registration_start_date)) {
            return false;
        }

        if ($event->registration_end_date && $now->gt($event->registration_end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir la configuration de l'organisation
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getOrganizationConfig($key, $default = null)
    {
        $org = self::getCurrentOrganization();

        if (!$org) {
            return $default;
        }

        // Utiliser les propriétés directes de l'organisation ou chercher dans une table de settings
        $property = "org_{$key}";
        if (isset($org->$property)) {
            return $org->$property;
        }

        return $default;
    }

    /**
     * Obtenir la configuration de l'événement
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getEventConfig($key, $default = null)
    {
        $event = self::getCurrentEvent();

        if (!$event) {
            return $default;
        }

        // Si vous avez une table event_settings, décommentez cette partie :
        /*
        if (isset($event->settings)) {
            $setting = $event->settings->where('setting_key', $key)->first();
            return $setting ? $setting->value : $default;
        }
        */

        // Pour l'instant, retourner la valeur par défaut
        return $default;
    }

    /**
     * Invalider le cache pour l'organisation
     *
     * @param string $orgSlug
     * @return void
     */
    public static function invalidateOrganizationCache($orgSlug)
    {
        Cache::forget(self::CACHE_ORG_PREFIX . $orgSlug);
    }

    /**
     * Invalider le cache pour l'événement
     *
     * @param int $organizationId
     * @param string $eventSlug
     * @return void
     */
    public static function invalidateEventCache($organizationId, $eventSlug)
    {
        Cache::forget(self::CACHE_EVENT_PREFIX . $organizationId . '_' . $eventSlug);
    }

    /**
     * Obtenir la connexion tenant actuelle
     *
     * @return string|null
     */
    public static function getTenantConnection()
    {
        $org = self::getCurrentOrganization();

        if (!$org) {
            return null;
        }

        self::configureTenantConnection($org);

        return 'tenant';
    }

    /**
     * Exécuter une closure avec la connexion tenant
     *
     * @param callable $callback
     * @return mixed
     */
    public static function withTenantConnection($callback)
    {
        $org = self::getCurrentOrganization();

        if (!$org) {
            throw new \Exception('Aucune organisation courante définie');
        }

        self::configureTenantConnection($org);

        return $callback();
    }

    /**
     * Obtenir les statistiques de l'organisation
     *
     * @return array
     */
    public static function getOrganizationStats()
    {
        $org = self::getCurrentOrganization();

        if (!$org) {
            return [];
        }

        return self::withTenantConnection(function() {
            return [
                'total_events' => Event::count(),
                'published_events' => Event::published()->count(),
                'upcoming_events' => Event::upcoming()->published()->count(),
                'total_registrations' => \DB::table('registrations')->count(),
                'confirmed_registrations' => \DB::table('registrations')->where('status', 'confirmed')->count(),
                'total_revenue' => \DB::table('registrations')->where('payment_status', 'paid')->sum('amount_paid'),
            ];
        });
    }

    /**
     * Invalider tout le cache tenant
     *
     * @return void
     */
    public static function invalidateAllTenantCache()
    {
        // Vous pouvez implémenter une logique plus sophistiquée
        // pour invalider tous les caches tenant si nécessaire
        Cache::flush();
    }

    /**
     * ✅ AMÉLIORATION : Obtenir les informations de debug du contexte tenant avec plus de détails
     *
     * @return array
     */
    public static function getDebugInfo()
    {
        return [
            'is_resolved' => self::$isResolved,
            'tenant_connection' => self::$tenantConnectionName,
            'service_container' => [
                'has_organization' => app()->bound('current.organization'),
                'has_event' => app()->bound('current.event'),
            ],
            'current_organization' => self::$currentOrganization ? [
                'id' => self::$currentOrganization->id,
                'name' => self::$currentOrganization->org_name,
                'key' => self::$currentOrganization->org_key,
                'database' => self::$currentOrganization->database_name,
                'status' => 'active', // Statut par défaut
            ] : null,
            'current_event' => self::$currentEvent ? [
                'id' => self::$currentEvent->id,
                'title' => self::$currentEvent->event_title,
                'slug' => self::$currentEvent->event_slug,
                'date' => self::$currentEvent->event_date?->format('Y-m-d'),
            ] : null,
            'route_parameters' => [
                'org_slug' => Route::current()?->parameter('org_slug'),
                'event_slug' => Route::current()?->parameter('event_slug'),
            ],
            'request_segments' => [
                'segment_1' => Request::segment(1),
                'segment_2' => Request::segment(2),
            ]
        ];
    }
}
