<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SuperAdminHelper
{
    /**
     * Vérifier si un super admin est connecté
     */
    public static function isLoggedIn(): bool
    {
        return session()->has('super_admin_logged_in');
    }

    /**
     * Obtenir l'ID de l'admin connecté
     */
    public static function id(): ?int
    {
        return session('super_admin_id');
    }

    /**
     * Obtenir le nom d'utilisateur de l'admin connecté
     */
    public static function username(): ?string
    {
        return session('super_admin_username');
    }

    /**
     * Obtenir le nom complet de l'admin connecté
     */
    public static function name(): ?string
    {
        return session('super_admin_name');
    }

    /**
     * Obtenir le niveau de l'admin connecté
     */
    public static function level(): ?string
    {
        return session('super_admin_level');
    }

    /**
     * Vérifier si l'admin a un niveau minimum
     */
    public static function hasLevel(string $requiredLevel): bool
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        $levels = ['readonly', 'support', 'admin', 'super_admin'];
        $currentLevel = self::level();
        
        $currentIndex = array_search($currentLevel, $levels);
        $requiredIndex = array_search($requiredLevel, $levels);
        
        return $currentIndex !== false && $currentIndex >= $requiredIndex;
    }

    /**
     * Vérifier si l'admin est super admin
     */
    public static function isSuperAdmin(): bool
    {
        return self::level() === 'super_admin';
    }

    /**
     * Obtenir toutes les informations de l'admin
     */
    public static function user(): array
    {
        if (!self::isLoggedIn()) {
            return [];
        }

        return [
            'id' => self::id(),
            'username' => self::username(),
            'name' => self::name(),
            'level' => self::level(),
            'login_time' => session('login_time'),
            'last_activity' => session('last_activity'),
        ];
    }

    /**
     * Obtenir les statistiques système rapidement
     */
    public static function getQuickStats(): array
    {
        try {
            return [
                'organizations_total' => DB::table('organizations')->count(),
                'organizations_active' => DB::table('organizations')->where('subscription_status', 'active')->count(),
                'users_total' => DB::table('saas_users')->count(),
                'users_active' => DB::table('saas_users')->where('is_active', 1)->count(),
            ];
        } catch (\Exception $e) {
            return [
                'organizations_total' => 0,
                'organizations_active' => 0,
                'users_total' => 0,
                'users_active' => 0,
            ];
        }
    }

    /**
     * Logger une action d'administration
     */
    public static function logAction(string $action, string $description, ?string $resourceType = null, ?int $resourceId = null): void
    {
        if (!self::isLoggedIn()) {
            return;
        }

        try {
            DB::table('admin_activity_logs')->insert([
                'admin_id' => self::id(),
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log silencieusement les erreurs de logging
            \Log::warning('Erreur lors du logging d\'activité admin', [
                'admin_id' => self::id(),
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vérifier les permissions pour une action
     */
    public static function can(string $permission): bool
    {
        // Super admin peut tout faire
        if (self::isSuperAdmin()) {
            return true;
        }

        if (!self::isLoggedIn()) {
            return false;
        }

        // Récupérer les permissions de l'admin depuis la base
        try {
            $admin = DB::table('system_admins')
                ->where('id', self::id())
                ->first(['permissions', 'admin_level']);

            if (!$admin) {
                return false;
            }

            $permissions = json_decode($admin->permissions ?? '[]', true);
            
            // Vérifier la permission exacte ou wildcard
            return in_array($permission, $permissions) || in_array('*', $permissions);
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Formater un nombre pour l'affichage
     */
    public static function formatNumber($number): string
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1) . 'K';
        }
        
        return number_format($number);
    }

    /**
     * Obtenir une couleur de badge selon le statut
     */
    public static function getStatusBadgeClass(string $status): string
    {
        $classes = [
            'active' => 'bg-success',
            'inactive' => 'bg-secondary',
            'suspended' => 'bg-warning',
            'cancelled' => 'bg-danger',
            'expired' => 'bg-secondary',
            'trial' => 'bg-info',
            'pending' => 'bg-warning',
            'confirmed' => 'bg-success',
            'paid' => 'bg-success',
            'unpaid' => 'bg-danger',
        ];

        return $classes[$status] ?? 'bg-secondary';
    }

    /**
     * Générer une clé d'organisation unique
     */
    public static function generateOrgKey(string $orgName): string
    {
        $key = strtolower($orgName);
        $key = preg_replace('/[^a-z0-9\s-]/', '', $key);
        $key = preg_replace('/\s+/', '-', $key);
        $key = preg_replace('/-+/', '-', $key);
        $key = trim($key, '-');
        
        // Vérifier l'unicité
        $originalKey = $key;
        $counter = 1;
        
        while (DB::table('organizations')->where('org_key', $key)->exists()) {
            $key = $originalKey . '-' . $counter;
            $counter++;
        }
        
        return $key;
    }

    /**
     * Vérifier l'état de santé du système
     */
    public static function getSystemHealth(): array
    {
        $health = [];
        
        // Base de données
        try {
            DB::connection()->getPdo();
            $health['database'] = ['status' => 'OK', 'message' => 'Connexion active'];
        } catch (\Exception $e) {
            $health['database'] = ['status' => 'ERROR', 'message' => 'Erreur de connexion'];
        }
        
        // Stockage
        $storagePath = storage_path();
        $health['storage'] = [
            'status' => is_writable($storagePath) ? 'OK' : 'WARNING',
            'message' => is_writable($storagePath) ? 'Accessible en écriture' : 'Problème de permissions',
            'free_space' => disk_free_space($storagePath)
        ];
        
        // Sessions
        try {
            $sessionId = session()->getId();
            $health['sessions'] = [
                'status' => $sessionId ? 'OK' : 'ERROR',
                'message' => $sessionId ? 'Fonctionnelles' : 'Problème sessions'
            ];
        } catch (\Exception $e) {
            $health['sessions'] = ['status' => 'ERROR', 'message' => 'Erreur sessions'];
        }
        
        return $health;
    }
}