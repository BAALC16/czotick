<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TenantDatabaseService;
use App\Models\Organization;

class TenantCreateCommand extends Command
{
    protected $signature = 'tenant:create 
                           {name : Nom de l\'organisation} 
                           {slug : Slug de l\'organisation}
                           {--migrate : Migrer la base de données}
                           {--seed : Seeder les données de base}';

    protected $description = 'Créer une nouvelle organisation tenant avec utilisateur dédié';

    protected $tenantService;

    public function __construct(TenantDatabaseService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $slug = $this->argument('slug');
        
        $this->info("🏗️  Création de l'organisation: {$name}");
        
        try {
            // 1. Créer la base de données avec utilisateur dédié
            $this->info("📊 Création de la base de données...");
            $databaseName = $this->tenantService->createTenantDatabase($name);
            $this->info("✅ Base de données créée: {$databaseName}");
            $this->info("👤 Utilisateur créé: {$databaseName}");
            $this->info("🔑 Mot de passe: Une@Vie@2route");
            
            // 2. Créer l'organisation dans la BD principale
            $this->info("🏢 Création de l'enregistrement organisation...");
            $organization = Organization::create([
                'org_key' => $slug,
                'org_name' => $name,
                'org_type' => 'jci', // Par défaut
                'contact_name' => 'Admin',
                'contact_email' => 'admin@' . $slug . '.local',
                'database_name' => $databaseName,
                'subdomain' => $slug,
                'subscription_status' => 'trial',
                'subscription_ends_at' => now()->addDays(30),
            ]);
            
            $this->info("✅ Organisation créée avec l'ID: {$organization->id}");
            
            // 3. Migrer si demandé
            if ($this->option('migrate')) {
                $this->info("🚀 Application des migrations...");
                $this->tenantService->migrateTenantDatabase($databaseName);
                $this->info("✅ Migrations appliquées");
            }
            
            // 4. Seeder si demandé
            if ($this->option('seed')) {
                $this->info("🌱 Insertion des données de base...");
                $this->seedTenantDatabase($databaseName);
                $this->info("✅ Données de base créées");
            }
            
            // 5. Test de connexion
            $this->info("🔍 Test de la connexion...");
            $result = $this->tenantService->testTenantConnection($databaseName);
            
            if ($result['status'] === 'success') {
                $this->info("✅ Connexion testée avec succès");
                $this->info("📋 Tables trouvées: " . $result['tables_count']);
            } else {
                $this->error("❌ Erreur de test: " . $result['error']);
            }
            
            $this->newLine();
            $this->info("🎉 Organisation '{$name}' créée avec succès!");
            $this->info("🌐 URL d'accès: /{$slug}");
            $this->info("🔗 Base de données: {$databaseName}");
            $this->info("👤 Utilisateur BD: {$databaseName}");
            $this->info("🔑 Mot de passe BD: Une@Vie@2route");
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la création: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function seedTenantDatabase($databaseName)
    {
        // Utiliser l'utilisateur tenant pour seeder
        config(['database.connections.temp_seed' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $databaseName,
            'username' => $databaseName,  // Utilisateur = nom de la base
            'password' => 'Une@Vie@2route',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);
        
        // Exécuter les seeders tenant
        \Artisan::call('db:seed', [
            '--database' => 'temp_seed',
            '--class' => 'TenantDatabaseSeeder'
        ]);
        
        DB::purge('temp_seed');
    }
}

