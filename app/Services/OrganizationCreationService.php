<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrganizationRegistration;
use App\Models\SubscriptionPack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Exception;

class OrganizationCreationService
{
    /**
     * Créer une nouvelle organisation avec sa base de données
     */
    public function createOrganization(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // 1. Créer l'enregistrement d'inscription (optionnel si la table n'existe pas)
            $registration = null;
            $registrationToken = Str::random(32);
            if (Schema::connection('saas_master')->hasTable('organization_registrations')) {
                $registration = $this->createRegistration($data);
                $registrationToken = $registration->registration_token;
            }
            
            // 2. Générer le nom de la base de données
            $databaseName = $this->generateDatabaseName($data['org_key']);
            
            // 3. Créer la base de données
            $this->createDatabase($databaseName);
            
            // 4. Créer l'organisation
            $organization = $this->createOrganizationRecord($data, $databaseName, $registration);
            
            // 5. Exécuter les migrations sur la nouvelle base
            $this->runMigrationsOnDatabase($databaseName);
            
            // 6. Insérer les données initiales
            $this->seedInitialData($databaseName);
            
            // 7. Mettre à jour l'enregistrement si présent
            if ($registration instanceof OrganizationRegistration) {
                $registration->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'created_organization_id' => $organization->id,
                    'created_database_name' => $databaseName
                ]);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'organization' => $organization,
                'database_name' => $databaseName,
                'registration' => $registration,
                'registration_token' => $registrationToken
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            
            if ($registration instanceof OrganizationRegistration) {
                $registration->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'processed_at' => now()
                ]);
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer l'enregistrement d'inscription
     */
    private function createRegistration(array $data): OrganizationRegistration
    {
        $pack = null;
        if (!empty($data['subscription_pack'])) {
            $pack = SubscriptionPack::where('pack_key', $data['subscription_pack'])->first();
        }

        return OrganizationRegistration::create([
            'registration_token' => Str::random(32),
            'org_name' => $data['org_name'],
            'org_key' => $data['org_key'],
            'org_type' => $data['org_type'],
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'] ?? null,
            'subdomain' => $data['subdomain'] ?? null,
            'custom_domain' => $data['custom_domain'] ?? null,
            'subscription_pack_id' => $pack?->id,
            'pack_settings' => $data['pack_settings'] ?? null,
            'enabled_countries' => $data['enabled_countries'] ?? null,
            'enabled_event_types' => $data['enabled_event_types'] ?? null,
            'status' => 'processing'
        ]);
    }
    
    /**
     * Générer un nom de base de données unique
     */
    private function generateDatabaseName(string $orgKey): string
    {
        $baseName = 'org_' . Str::slug($orgKey, '_');
        $databaseName = $baseName;
        $counter = 1;
        
        // Vérifier l'unicité
        while ($this->databaseExists($databaseName)) {
            $databaseName = $baseName . '_' . $counter;
            $counter++;
        }
        
        return $databaseName;
    }
    
    /**
     * Vérifier si une base de données existe
     */
    private function databaseExists(string $databaseName): bool
    {
        $result = DB::connection('saas_master')->select("SHOW DATABASES LIKE '{$databaseName}'");
        return count($result) > 0;
    }
    
    /**
     * Créer la base de données
     */
    private function createDatabase(string $databaseName): void
    {
        DB::connection('saas_master')->statement("CREATE DATABASE `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
    
    /**
     * Créer l'enregistrement d'organisation
     */
    private function createOrganizationRecord(array $data, string $databaseName, ?OrganizationRegistration $registration): Organization
    {
        $pack = ($registration && $registration->subscription_pack_id)
            ? SubscriptionPack::find($registration->subscription_pack_id)
            : null;

        $organizationData = [
            'org_key' => $data['org_key'],
            'org_name' => $data['org_name'],
            'organization_logo' => $data['organization_logo'] ?? 'default-logo.png',
            'org_type' => $data['org_type'],
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'] ?? null,
            'database_name' => $databaseName,
            'subdomain' => $data['subdomain'] ?? null,
            'custom_domain' => $data['custom_domain'] ?? null,
        ];

        // Ajouter les champs optionnels si la migration add_pack_features existe
        if (Schema::connection('saas_master')->hasColumn('organizations', 'subscription_pack_id')) {
            $organizationData['subscription_pack_id'] = $pack?->id;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'enabled_event_types')) {
            $organizationData['enabled_event_types'] = $data['enabled_event_types'] ?? null;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'enabled_countries')) {
            $organizationData['enabled_countries'] = $data['enabled_countries'] ?? null;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'payment_methods')) {
            $organizationData['payment_methods'] = $pack->payment_methods ?? null;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'multi_ticket_purchase')) {
            $organizationData['multi_ticket_purchase'] = $pack->multi_ticket_purchase ?? false;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'max_tickets_per_purchase')) {
            $organizationData['max_tickets_per_purchase'] = ($pack->multi_ticket_purchase ?? false) ? 10 : 1;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'whatsapp_integration')) {
            $organizationData['whatsapp_integration'] = $pack->whatsapp_tickets ?? false;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'custom_ticket_design')) {
            $organizationData['custom_ticket_design'] = $pack->custom_tickets ?? false;
        }
        if (Schema::connection('saas_master')->hasColumn('organizations', 'ticket_templates')) {
            $organizationData['ticket_templates'] = $pack->ticket_templates ?? null;
        }

        return Organization::create($organizationData);
    }
    
    /**
     * Exécuter les migrations sur la nouvelle base de données
     */
    private function runMigrationsOnDatabase(string $databaseName): void
    {
        // Configurer la connexion temporaire avec les mêmes identifiants que saas_master
        $saasConfig = config('database.connections.saas_master');
        config(['database.connections.tenant' => array_merge($saasConfig, ['database' => $databaseName])]);
        
        // Exécuter les migrations spécifiques aux organisations
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true
        ]);
    }
    
    /**
     * Insérer les données initiales
     */
    private function seedInitialData(string $databaseName): void
    {
        // Configurer la connexion temporaire avec les mêmes identifiants que saas_master
        $saasConfig = config('database.connections.saas_master');
        config(['database.connections.tenant' => array_merge($saasConfig, ['database' => $databaseName])]);
        
        // Exécuter les seeders spécifiques aux organisations
        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => 'TenantEventSystemSeeder',
            '--force' => true
        ]);
    }
    
    /**
     * Supprimer une organisation et sa base de données
     */
    public function deleteOrganization(Organization $organization): bool
    {
        DB::beginTransaction();
        
        try {
            // Supprimer la base de données
            DB::connection('saas_master')->statement("DROP DATABASE IF EXISTS `{$organization->database_name}`");
            
            // Supprimer l'organisation
            $organization->delete();
            
            DB::commit();
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    
    /**
     * Obtenir le statut d'une inscription
     */
    public function getRegistrationStatus(string $token): ?OrganizationRegistration
    {
        return OrganizationRegistration::where('registration_token', $token)->first();
    }
}
