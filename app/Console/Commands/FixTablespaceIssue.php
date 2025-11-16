<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixTablespaceIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-tablespace {--connection=saas_master : La connexion à la base de données à utiliser}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corriger les problèmes de tablespace MySQL en supprimant les tablespaces orphelins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connection = $this->option('connection');
        
        $this->info("Correction des problèmes de tablespace pour la connexion : {$connection}");
        
        try {
            // Get database name from config
            $dbName = config("database.connections.{$connection}.database");
            $this->info("Base de données : {$dbName}");
            
            // Check if table exists
            $tableExists = false;
            try {
                $tableExists = Schema::connection($connection)->hasTable('migrations');
            } catch (\Exception $e) {
                $this->warn("Impossible de vérifier si la table existe : " . $e->getMessage());
            }
            
            if ($tableExists) {
                $this->info("La table 'migrations' existe. Tentative de suppression du tablespace...");
                
                try {
                    // Try to discard the tablespace
                    DB::connection($connection)->statement("ALTER TABLE migrations DISCARD TABLESPACE");
                    $this->info("✅ Tablespace supprimé avec succès.");
                } catch (\Exception $e) {
                    $this->warn("Impossible de supprimer le tablespace : " . $e->getMessage());
                    $this->info("Tentative de suppression de la table...");
                    
                    // Drop the table if it exists
                    try {
                        Schema::connection($connection)->dropIfExists('migrations');
                        $this->info("✅ Table supprimée avec succès.");
                    } catch (\Exception $e2) {
                        $this->error("Impossible de supprimer la table : " . $e2->getMessage());
                        throw $e2;
                    }
                }
            } else {
                $this->info("La table 'migrations' n'existe pas dans le schéma, mais le fichier tablespace existe.");
                $this->info("Tentative de création de la structure de table pour récupérer le tablespace orphelin...");
                
                // The issue: tablespace file exists but table definition doesn't
                // Solution: Create the table (which will try to use existing tablespace),
                // then immediately drop it to clean up
                try {
                    // Step 1: Create the table with the exact structure Laravel uses
                    // This will attempt to use the existing tablespace
                    DB::connection($connection)->statement("
                        CREATE TABLE migrations (
                            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            migration VARCHAR(191) NOT NULL,
                            batch INT NOT NULL,
                            PRIMARY KEY (id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
                    ");
                    
                    $this->info("✅ Table créée (tablespace orphelin récupéré).");
                    
                    // Step 2: Now drop it properly, which will clean up the tablespace
                    DB::connection($connection)->statement("DROP TABLE migrations");
                    $this->info("✅ Table supprimée (tablespace nettoyé).");
                    
                } catch (\Exception $e) {
                    // If creating fails due to tablespace conflict, try importing first
                    $this->warn("Création directe échouée : " . $e->getMessage());
                    $this->info("Tentative d'une méthode alternative...");
                    
                    try {
                        // Create table without tablespace
                        DB::connection($connection)->statement("
                            CREATE TABLE migrations (
                                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                migration VARCHAR(191) NOT NULL,
                                batch INT NOT NULL,
                                PRIMARY KEY (id)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
                        ");
                        
                        // Try to import the orphaned tablespace
                        DB::connection($connection)->statement("ALTER TABLE migrations IMPORT TABLESPACE");
                        
                        // Now drop it
                        DB::connection($connection)->statement("DROP TABLE migrations");
                        $this->info("✅ Tablespace orphelin importé et nettoyé.");
                        
                    } catch (\Exception $e2) {
                        $this->error("❌ Correction automatique impossible : " . $e2->getMessage());
                        $this->error("\nCorrection manuelle requise. Veuillez exécuter dans MySQL :");
                        $this->error("USE {$dbName};");
                        $this->error("CREATE TABLE migrations (id INT UNSIGNED NOT NULL AUTO_INCREMENT, migration VARCHAR(191) NOT NULL, batch INT NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB;");
                        $this->error("ALTER TABLE migrations IMPORT TABLESPACE;");
                        $this->error("DROP TABLE migrations;");
                        throw $e2;
                    }
                }
            }
            
            $this->info("\n✅ Le problème de tablespace devrait être résolu ! Vous pouvez maintenant exécuter :");
            $this->info("   php artisan migrate --database={$connection}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur : " . $e->getMessage());
            $this->error("\nVous devrez peut-être corriger cela manuellement dans MySQL :");
            $this->error("1. Connectez-vous à MySQL (mysql -u root -p)");
            $this->error("2. USE {$dbName};");
            $this->error("3. DROP TABLE IF EXISTS migrations;");
            $this->error("4. Puis exécutez : php artisan migrate --database={$connection}");
            
            return 1;
        }
    }
}

