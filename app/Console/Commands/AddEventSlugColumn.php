<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEventSlugColumn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:add-event-slug {database_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add event_slug column to events table in tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databaseName = $this->argument('database_name');

        // Configurer la connexion tenant
        $mysqlConfig = config('database.connections.mysql');
        
        $tenantConfig = [
            'driver' => 'mysql',
            'host' => $mysqlConfig['host'],
            'port' => $mysqlConfig['port'],
            'database' => $databaseName,
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

        try {
            // Vérifier si la colonne existe déjà
            if (Schema::connection('tenant')->hasColumn('events', 'event_slug')) {
                $this->info("La colonne 'event_slug' existe déjà dans la table 'events'.");
                return 0;
            }

            // Ajouter la colonne
            Schema::connection('tenant')->table('events', function ($table) {
                $table->string('event_slug', 255)->nullable()->after('event_title');
                $table->index('event_slug');
            });

            $this->info("✅ Colonne 'event_slug' ajoutée avec succès à la table 'events' dans la base de données '{$databaseName}'.");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'ajout de la colonne : " . $e->getMessage());
            return 1;
        }
    }
}
