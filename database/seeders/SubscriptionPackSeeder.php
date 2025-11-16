<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPack;

class SubscriptionPackSeeder extends Seeder
{
    public function run(): void
    {
        // Pack Standard
        SubscriptionPack::firstOrCreate(
            ['pack_key' => 'standard'],
            [
                'pack_name' => 'Standard',
                'pack_description' => 'Réception de ticket par email, ticket basique',
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
                'display_order' => 0,
            ]
        );

        // Pack Premium
        SubscriptionPack::firstOrCreate(
            ['pack_key' => 'premium'],
            [
                'pack_name' => 'Premium',
                'pack_description' => 'Email + WhatsApp, ticket personnalisé',
                'pack_type' => 'premium',
                'commission_percentage' => 0,
                'setup_fee' => 0,
                'monthly_fee' => 0,
                'currency' => 'XOF',
                'email_tickets' => true,
                'whatsapp_tickets' => true,
                'custom_tickets' => true,
                'multi_ticket_purchase' => true,
                'multi_country_support' => true,
                'custom_domain' => false,
                'advanced_analytics' => false,
                'api_access' => false,
                'priority_support' => false,
                'max_events' => 3,
                'max_participants_per_event' => 500,
                'max_storage_mb' => 500,
                'max_ticket_types_per_event' => 6,
                'supported_countries' => null,
                'payment_methods' => null,
                'ticket_templates' => null,
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        // Pack Personnalisé
        SubscriptionPack::firstOrCreate(
            ['pack_key' => 'custom'],
            [
                'pack_name' => 'Personnalisé',
                'pack_description' => 'Fonctionnalités sur-mesure',
                'pack_type' => 'custom',
                'commission_percentage' => 0,
                'setup_fee' => 0,
                'monthly_fee' => 0,
                'currency' => 'XOF',
                'email_tickets' => true,
                'whatsapp_tickets' => true,
                'custom_tickets' => true,
                'multi_ticket_purchase' => true,
                'multi_country_support' => true,
                'custom_domain' => true,
                'advanced_analytics' => true,
                'api_access' => true,
                'priority_support' => true,
                'max_events' => -1,
                'max_participants_per_event' => -1,
                'max_storage_mb' => -1,
                'max_ticket_types_per_event' => -1,
                'supported_countries' => null,
                'payment_methods' => null,
                'ticket_templates' => null,
                'is_active' => true,
                'display_order' => 2,
            ]
        );
    }
}

