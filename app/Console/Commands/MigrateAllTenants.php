<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Organization;

class MigrateAllTenants extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:migrate-all {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     */
    protected $description = 'Run migrations on all organization tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Début de la migration de toutes les bases de données tenant...');

        // Récupérer toutes les organisations
        $organizations = Organization::on('mysql')->get();

        if ($organizations->isEmpty()) {
            $this->warn('Aucune organisation trouvée.');
            return 0;
        }

        $this->info("Nombre d'organisations trouvées : " . $organizations->count());

        $successCount = 0;
        $errorCount = 0;

        foreach ($organizations as $organization) {
            $databaseName = $organization->database_name;

            if (empty($databaseName)) {
                $this->warn("⚠️  Organisation '{$organization->org_name}' n'a pas de base de données configurée. Ignorée.");
                continue;
            }

            $this->info("\n📊 Migration de la base de données '{$databaseName}' (Organisation: {$organization->org_name})...");

            // Vérifier que la base de données existe
            try {
                $databases = DB::select("SHOW DATABASES LIKE '{$databaseName}'");

                if (empty($databases)) {
                    $this->error("❌ La base de données '{$databaseName}' n'existe pas.");
                    $errorCount++;
                    continue;
                }
            } catch (\Exception $e) {
                $this->error("❌ Erreur lors de la vérification de la base de données '{$databaseName}': " . $e->getMessage());
                $errorCount++;
                continue;
            }

            // Configurer la connexion temporaire
            $saasConfig = config('database.connections.saas_master');
            config(['database.connections.tenant' => array_merge($saasConfig, ['database' => $databaseName])]);
            DB::purge('tenant');

            try {
                // Exécuter les migrations spécifiques aux organisations
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => $this->option('force') || true
                ]);

                $this->info("✅ Base de données '{$databaseName}' migrée avec succès !");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("❌ Erreur lors de la migration de '{$databaseName}': " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("📊 Résumé de la migration :");
        $this->info("✅ Succès : {$successCount}");
        $this->info("❌ Erreurs : {$errorCount}");
        $this->info("📦 Total : " . $organizations->count());
        $this->info(str_repeat('=', 50));

        return $errorCount > 0 ? 1 : 0;
    }
}

