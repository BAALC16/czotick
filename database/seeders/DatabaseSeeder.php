<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\Article;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
  * Seed the application's database.
  *
  * @return void
  */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            PaysSeeder::class,
            RoleSeeder::class,
            StatutReservationSeeder::class,
            AddRolesMembreAndPrestataireSeeder::class,
            PropertyTypeSeeder::class,
            VillaTypeSeeder::class,
            LayoutTypeSeeder::class,
            CitySeeder::class,
            ServiceSeeder::class,
            BlongConfigurationSeeder::class,
            CreditPointsSeeder::class,
            FeaturePropertySeeder::class
        ]);
        // Ajout automatique du pack standard si non existant
        \App\Models\SubscriptionPack::firstOrCreate([
            'pack_key' => 'standard'
        ], [
            'pack_name' => 'Standard',
            'pack_description' => 'Pack standard inclus par défaut',
            'pack_type' => 'standard',
            'commission_percentage' => 0,
            'setup_fee' => 0,
            'monthly_fee' => 0,
            'currency' => 'XOF',
            'email_tickets' => true,
            'whatsapp_tickets' => false,
            'custom_tickets' => false,
            'multi_ticket_purchase' => false,
            'multi_country_support' => false,
            'custom_domain' => false,
            'advanced_analytics' => false,
            'api_access' => false,
            'priority_support' => false,
            'max_events' => 1,
            'max_participants_per_event' => 100,
            'max_storage_mb' => 100,
            'max_ticket_types_per_event' => 3,
            'supported_countries' => null,
            'payment_methods' => null,
            'ticket_templates' => null,
            'is_active' => true,
            'display_order' => 0
        ]);
        //Article::factory(3)->create();
    }
}
