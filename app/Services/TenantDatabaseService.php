<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Organization;

class TenantDatabaseService
{
    const TENANT_PASSWORD = 'Une@Vie@2route';
    
    /**
     * Créer une nouvelle base de données tenant avec utilisateur dédié
     */
    public function createTenantDatabase($organizationName)
    {
        $databaseName = $this->generateDatabaseName($organizationName);
        
        // Vérifier si la BD existe déjà
        if ($this->tenantDatabaseExists($databaseName)) {
            throw new \Exception("La base de données {$databaseName} existe déjà");
        }
        
        try {
            DB::beginTransaction();
            
            // 1. Créer la base de données
            $this->createDatabase($databaseName);
            
            // 2. Créer l'utilisateur dédié
            $this->createDatabaseUser($databaseName);
            
            // 3. Appliquer les privilèges
            $this->grantUserPrivileges($databaseName);
            
            DB::commit();
            
            \Log::info('Base de données tenant créée avec succès', [
                'database_name' => $databaseName,
                'username' => $databaseName
            ]);
            
            return $databaseName;
            
        } catch (\Exception $e) {
            DB::rollback();
            
            // Nettoyer en cas d'erreur
            $this->cleanupFailedCreation($databaseName);
            
            throw new \Exception("Erreur lors de la création de la base tenant: " . $e->getMessage());
        }
    }
    
    /**
     * Créer la base de données
     */
    private function createDatabase($databaseName)
    {
        $query = "CREATE DATABASE `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        DB::statement($query);
    }
    
    /**
     * Créer l'utilisateur MySQL pour cette base
     */
    private function createDatabaseUser($databaseName)
    {
        // Supprimer l'utilisateur s'il existe déjà
        DB::statement("DROP USER IF EXISTS '{$databaseName}'@'localhost'");
        DB::statement("DROP USER IF EXISTS '{$databaseName}'@'127.0.0.1'");
        DB::statement("DROP USER IF EXISTS '{$databaseName}'@'%'");
        
        // Créer le nouvel utilisateur
        DB::statement("CREATE USER '{$databaseName}'@'localhost' IDENTIFIED BY '" . self::TENANT_PASSWORD . "'");
        DB::statement("CREATE USER '{$databaseName}'@'127.0.0.1' IDENTIFIED BY '" . self::TENANT_PASSWORD . "'");
        DB::statement("CREATE USER '{$databaseName}'@'%' IDENTIFIED BY '" . self::TENANT_PASSWORD . "'");
    }
    
    /**
     * Donner les privilèges à l'utilisateur sur sa base
     */
    private function grantUserPrivileges($databaseName)
    {
        // Privilèges complets sur sa propre base
        DB::statement("GRANT ALL PRIVILEGES ON `{$databaseName}`.* TO '{$databaseName}'@'localhost'");
        DB::statement("GRANT ALL PRIVILEGES ON `{$databaseName}`.* TO '{$databaseName}'@'127.0.0.1'");
        DB::statement("GRANT ALL PRIVILEGES ON `{$databaseName}`.* TO '{$databaseName}'@'%'");
        
        // Recharger les privilèges
        DB::statement("FLUSH PRIVILEGES");
    }
    
    /**
     * Migrer les tables pour un tenant avec l'utilisateur dédié
     */
    public function migrateTenantDatabase($databaseName)
    {
        // Configurer temporairement la connexion avec l'utilisateur tenant
        config(['database.connections.temp_tenant' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $databaseName,
            'username' => $databaseName,  // Utilisateur = nom de la base
            'password' => self::TENANT_PASSWORD,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);
        
        try {
            // Tester la connexion
            DB::connection('temp_tenant')->getPdo();
            
            // Exécuter les migrations tenant
            Artisan::call('migrate', [
                '--database' => 'temp_tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true
            ]);
            
        } finally {
            // Nettoyer la connexion temporaire
            DB::purge('temp_tenant');
        }
    }
    
    /**
     * Supprimer une base de données tenant et son utilisateur
     */
    public function dropTenantDatabase($databaseName)
    {
        if (!$this->tenantDatabaseExists($databaseName)) {
            throw new \Exception("La base de données {$databaseName} n'existe pas");
        }
        
        try {
            DB::beginTransaction();
            
            // Supprimer l'utilisateur
            DB::statement("DROP USER IF EXISTS '{$databaseName}'@'localhost'");
            DB::statement("DROP USER IF EXISTS '{$databaseName}'@'127.0.0.1'");
            DB::statement("DROP USER IF EXISTS '{$databaseName}'@'%'");
            
            // Supprimer la base de données
            DB::statement("DROP DATABASE `{$databaseName}`");
            
            // Recharger les privilèges
            DB::statement("FLUSH PRIVILEGES");
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception("Erreur lors de la suppression: " . $e->getMessage());
        }
    }
    
    /**
     * Vérifier si une base de données tenant existe
     */
    public function tenantDatabaseExists($databaseName)
    {
        $result = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
        return !empty($result);
    }
    
    /**
     * Nettoyer après une création échouée
     */
    private function cleanupFailedCreation($databaseName)
    {
        try {
            // Supprimer la base si elle a été créée
            DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
            
            // Supprimer l'utilisateur si il a été créé
            DB::statement("DROP USER IF EXISTS '{$databaseName}'@'localhost'");
            DB::statement("DROP USER IF EXISTS '{$databaseName}'@'127.0.0.1'");
            DB::statement("DROP USER IF EXISTS '{$databaseName}'@'%'");
            
            DB::statement("FLUSH PRIVILEGES");
        } catch (\Exception $e) {
            // Ignorer les erreurs de nettoyage
        }
    }
    
    /**
     * Générer un nom de base de données unique
     */
    private function generateDatabaseName($organizationName)
    {
        $slug = \Str::slug($organizationName, '_');
        $slug = preg_replace('/[^a-zA-Z0-9_]/', '', $slug);
        $prefix = env('TENANT_DB_PREFIX', 'org_');
        
        // Limiter la longueur pour MySQL (64 caractères max)
        $maxLength = 64 - strlen($prefix) - 10;
        $slug = substr($slug, 0, $maxLength);
        
        $timestamp = time();
        $databaseName = $prefix . $slug . '_' . $timestamp;
        
        // Vérifier l'unicité
        if ($this->tenantDatabaseExists($databaseName)) {
            $databaseName = $prefix . $slug . '_' . $timestamp . '_' . rand(100, 999);
        }
        
        return $databaseName;
    }
    
    /**
     * Lister tous les utilisateurs tenant
     */
    public function listTenantUsers()
    {
        $users = DB::select("
            SELECT User, Host 
            FROM mysql.user 
            WHERE User LIKE 'org_%'
            ORDER BY User
        ");
        
        return collect($users)->map(function ($user) {
            return [
                'username' => $user->User,
                'host' => $user->Host,
                'database' => $user->User
            ];
        });
    }
    
    /**
     * Vérifier la connexion d'un tenant spécifique
     */
    public function testTenantConnection($databaseName)
    {
        try {
            config(['database.connections.test_tenant' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => $databaseName,
                'username' => $databaseName,
                'password' => self::TENANT_PASSWORD,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]]);
            
            $pdo = DB::connection('test_tenant')->getPdo();
            $tables = DB::connection('test_tenant')->select('SHOW TABLES');
            
            return [
                'status' => 'success',
                'connection' => 'OK',
                'username' => $databaseName,
                'database' => $databaseName,
                'tables_count' => count($tables)
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'connection' => 'FAILED',
                'username' => $databaseName,
                'database' => $databaseName,
                'error' => $e->getMessage()
            ];
        } finally {
            DB::purge('test_tenant');
        }
    }
}