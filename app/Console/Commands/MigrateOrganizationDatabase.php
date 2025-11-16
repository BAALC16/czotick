<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class MigrateOrganizationDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'organization:migrate {database_name} {--seed : Run seeders after migration}';

    /**
     * The console command description.
     */
    protected $description = 'Run migrations on a specific organization database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databaseName = $this->argument('database_name');
        $runSeeders = $this->option('seed');

        // Vérifier que la base de données existe
        $databases = DB::select("SHOW DATABASES LIKE '{$databaseName}'");
        
        if (empty($databases)) {
            $this->error("La base de données '{$databaseName}' n'existe pas.");
            return 1;
        }

        $this->info("Migration de la base de données '{$databaseName}'...");

        // Configurer la connexion temporaire avec la même config que saas_master
        $saasConfig = config('database.connections.saas_master');
        config(['database.connections.tenant' => array_merge($saasConfig, ['database' => $databaseName])]);
        DB::purge('tenant');

        try {
            // Exécuter les migrations spécifiques aux organisations
            $this->info("Exécution des migrations...");
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true
            ]);

            $this->info("Migrations terminées avec succès.");

            // Exécuter les seeders si demandé
            if ($runSeeders) {
                $this->info("Exécution des seeders...");
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--class' => 'TenantEventSystemSeeder',
                    '--force' => true
                ]);
                $this->info("Seeders terminés avec succès.");
            }

            $this->info("✅ Base de données '{$databaseName}' migrée avec succès !");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la migration : " . $e->getMessage());
            return 1;
        }
    }
}
