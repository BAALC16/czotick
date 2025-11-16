<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrganizationCreationService;
use App\Models\SubscriptionPack;
use Illuminate\Support\Facades\DB;

class CreateOrganization extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'organization:create 
                            {org_name : Nom de l\'organisation}
                            {org_key : Clé de l\'organisation}
                            {org_type : Type d\'organisation (jci,rotary,lions,association,company,other)}
                            {contact_name : Nom du contact}
                            {contact_email : Email du contact}
                            {--pack=standard : Pack d\'abonnement (standard,premium,custom)}
                            {--subdomain= : Sous-domaine personnalisé}
                            {--phone= : Numéro de téléphone du contact}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new organization with its database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orgName = $this->argument('org_name');
        $orgKey = $this->argument('org_key');
        $orgType = $this->argument('org_type');
        $contactName = $this->argument('contact_name');
        $contactEmail = $this->argument('contact_email');
        $packKey = $this->option('pack');
        $subdomain = $this->option('subdomain');
        $phone = $this->option('phone');

        // Vérifier que le pack existe
        $pack = SubscriptionPack::where('pack_key', $packKey)->first();
        if (!$pack) {
            $this->error("Le pack '{$packKey}' n'existe pas.");
            return 1;
        }

        // Vérifier que la clé d'organisation est unique
        if (DB::table('organizations')->where('org_key', $orgKey)->exists()) {
            $this->error("La clé d'organisation '{$orgKey}' est déjà utilisée.");
            return 1;
        }

        // Préparer les données
        $data = [
            'org_name' => $orgName,
            'org_key' => $orgKey,
            'org_type' => $orgType,
            'contact_name' => $contactName,
            'contact_email' => $contactEmail,
            'contact_phone' => $phone,
            'subdomain' => $subdomain,
            'subscription_pack' => $packKey,
            'enabled_countries' => ['CI'], // Par défaut Côte d'Ivoire
            'enabled_event_types' => null, // Tous les types par défaut
            'pack_settings' => null
        ];

        $this->info("Création de l'organisation '{$orgName}'...");
        $this->info("Pack sélectionné: {$pack->pack_name} ({$pack->formatted_commission})");

        // Créer l'organisation
        $service = new OrganizationCreationService();
        $result = $service->createOrganization($data);

        if ($result['success']) {
            $organization = $result['organization'];
            $this->info("✅ Organisation créée avec succès !");
            $this->info("   - ID: {$organization->id}");
            $this->info("   - Clé: {$organization->org_key}");
            $this->info("   - Base de données: {$result['database_name']}");
            $this->info("   - URL: " . ($organization->subdomain ? "https://{$organization->subdomain}.votre-domaine.com" : "https://votre-domaine.com/{$organization->org_key}"));
            
            return 0;
        } else {
            $this->error("❌ Erreur lors de la création : " . $result['error']);
            return 1;
        }
    }
}
