<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrateTicketTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:migrate-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrer les templates de tickets existants vers la nouvelle structure public/{database_name}/events/tickets/';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Début de la migration des templates de tickets...');

        // Récupérer toutes les organisations
        $organizations = DB::connection('saas_master')
            ->table('organizations')
            ->select('id', 'database_name', 'org_name')
            ->get();

        $totalMigrated = 0;
        $totalErrors = 0;

        foreach ($organizations as $org) {
            $this->info("Traitement de l'organisation: {$org->org_name} (DB: {$org->database_name})");

            try {
                // Configurer la connexion tenant
                $mysqlConfig = config('database.connections.mysql');
                $tenantConfig = [
                    'driver' => 'mysql',
                    'host' => $mysqlConfig['host'],
                    'port' => $mysqlConfig['port'],
                    'database' => $org->database_name,
                    'username' => $mysqlConfig['username'],
                    'password' => $mysqlConfig['password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                ];
                config(['database.connections.tenant' => $tenantConfig]);
                DB::purge('tenant');

                // Récupérer tous les événements avec ticket_customization
                $events = DB::connection('tenant')
                    ->table('events')
                    ->whereNotNull('ticket_customization')
                    ->select('id', 'event_title', 'ticket_customization')
                    ->get();

                foreach ($events as $event) {
                    $ticketCustomization = json_decode($event->ticket_customization, true);

                    if (!isset($ticketCustomization['template_path'])) {
                        continue;
                    }

                    $oldPath = $ticketCustomization['template_path'];
                    $this->line("  - Événement: {$event->event_title}");
                    $this->line("    Ancien chemin: {$oldPath}");

                    // Déterminer l'ancien emplacement du fichier
                    $fileName = basename($oldPath);
                    $oldFilePaths = [
                        storage_path('app/public/' . $oldPath),
                        public_path($oldPath),
                        public_path('events/tickets/' . $org->database_name . '/' . $fileName),
                        public_path('storage/' . $oldPath),
                        public_path($org->database_name . '/events/tickets/' . $fileName),
                    ];
                    
                    // Chercher aussi dans tous les sous-répertoires de public
                    $publicDirs = ['public', 'public/storage'];
                    foreach ($publicDirs as $dir) {
                        if (is_dir($dir)) {
                            $foundFiles = glob($dir . '/**/' . $fileName);
                            foreach ($foundFiles as $foundFile) {
                                $oldFilePaths[] = $foundFile;
                            }
                        }
                    }

                    $sourceFile = null;
                    foreach ($oldFilePaths as $path) {
                        if (file_exists($path)) {
                            $sourceFile = $path;
                            break;
                        }
                    }

                    if (!$sourceFile) {
                        $this->warn("    ⚠️  Fichier non trouvé: {$oldPath}");
                        $totalErrors++;
                        continue;
                    }

                    // Nouveau chemin
                    $fileName = basename($oldPath);
                    $newDirectory = public_path($org->database_name . '/events/tickets');
                    $newFilePath = $newDirectory . '/' . $fileName;
                    $newRelativePath = $org->database_name . '/events/tickets/' . $fileName;

                    // Créer le répertoire de destination
                    if (!File::exists($newDirectory)) {
                        File::makeDirectory($newDirectory, 0755, true);
                    }

                    // Copier le fichier
                    if (File::copy($sourceFile, $newFilePath)) {
                        $this->info("    ✅ Fichier copié vers: {$newRelativePath}");

                        // Mettre à jour le chemin dans la base de données
                        $ticketCustomization['template_path'] = $newRelativePath;
                        $updatedCustomization = json_encode($ticketCustomization);

                        DB::connection('tenant')
                            ->table('events')
                            ->where('id', $event->id)
                            ->update(['ticket_customization' => $updatedCustomization]);

                        $this->info("    ✅ Chemin mis à jour dans la base de données");
                        $totalMigrated++;

                        // Optionnel: Supprimer l'ancien fichier
                        // File::delete($sourceFile);
                    } else {
                        $this->error("    ❌ Erreur lors de la copie du fichier");
                        $totalErrors++;
                    }
                }
            } catch (\Exception $e) {
                $this->error("Erreur pour l'organisation {$org->org_name}: " . $e->getMessage());
                $totalErrors++;
            }
        }

        $this->info("\n=== Résumé de la migration ===");
        $this->info("Templates migrés: {$totalMigrated}");
        $this->info("Erreurs: {$totalErrors}");

        return 0;
    }
}
