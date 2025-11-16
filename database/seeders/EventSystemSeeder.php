<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insérer les types d'événements
        $eventTypes = [
            [
                'type_key' => 'concert_spectacle',
                'type_name' => 'Concert & Spectacle',
                'type_description' => 'Concerts, spectacles, représentations artistiques',
                'icon' => 'music',
                'color' => '#e91e63',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'age' => ['required' => false, 'label' => 'Âge'],
                    'preferences' => ['required' => false, 'label' => 'Préférences musicales']
                ]),
                'display_order' => 1
            ],
            [
                'type_key' => 'formation',
                'type_name' => 'Formation',
                'type_description' => 'Formations professionnelles, ateliers, séminaires',
                'icon' => 'graduation-cap',
                'color' => '#2196f3',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'organization' => ['required' => true, 'label' => 'Organisation'],
                    'position' => ['required' => true, 'label' => 'Poste'],
                    'experience_level' => ['required' => false, 'label' => 'Niveau d\'expérience']
                ]),
                'display_order' => 2
            ],
            [
                'type_key' => 'conference',
                'type_name' => 'Conférence',
                'type_description' => 'Conférences, colloques, symposiums',
                'icon' => 'microphone',
                'color' => '#4caf50',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'organization' => ['required' => true, 'label' => 'Organisation'],
                    'position' => ['required' => true, 'label' => 'Poste'],
                    'interests' => ['required' => false, 'label' => 'Sujets d\'intérêt']
                ]),
                'display_order' => 3
            ],
            [
                'type_key' => 'festival',
                'type_name' => 'Festival',
                'type_description' => 'Festivals culturels, musicaux, artistiques',
                'icon' => 'calendar-alt',
                'color' => '#ff9800',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'age' => ['required' => true, 'label' => 'Âge'],
                    'emergency_contact' => ['required' => true, 'label' => 'Contact d\'urgence'],
                    'dietary_restrictions' => ['required' => false, 'label' => 'Restrictions alimentaires']
                ]),
                'display_order' => 4
            ],
            [
                'type_key' => 'soiree',
                'type_name' => 'Soirée',
                'type_description' => 'Soirées, galas, événements sociaux',
                'icon' => 'glass-cheers',
                'color' => '#9c27b0',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'plus_one' => ['required' => false, 'label' => 'Accompagnateur'],
                    'dress_code' => ['required' => false, 'label' => 'Code vestimentaire']
                ]),
                'display_order' => 5
            ],
            [
                'type_key' => 'gastronomie',
                'type_name' => 'Gastronomie',
                'type_description' => 'Événements culinaires, dégustations, cours de cuisine',
                'icon' => 'utensils',
                'color' => '#f44336',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'dietary_restrictions' => ['required' => true, 'label' => 'Restrictions alimentaires'],
                    'allergies' => ['required' => false, 'label' => 'Allergies'],
                    'cooking_level' => ['required' => false, 'label' => 'Niveau en cuisine']
                ]),
                'display_order' => 6
            ],
            [
                'type_key' => 'tourisme',
                'type_name' => 'Tourisme',
                'type_description' => 'Événements touristiques, visites, excursions',
                'icon' => 'map-marked-alt',
                'color' => '#00bcd4',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'emergency_contact' => ['required' => true, 'label' => 'Contact d\'urgence'],
                    'medical_conditions' => ['required' => false, 'label' => 'Conditions médicales'],
                    'travel_insurance' => ['required' => false, 'label' => 'Assurance voyage']
                ]),
                'display_order' => 7
            ],
            [
                'type_key' => 'sport',
                'type_name' => 'Sport',
                'type_description' => 'Événements sportifs, compétitions, tournois',
                'icon' => 'running',
                'color' => '#4caf50',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'age' => ['required' => true, 'label' => 'Âge'],
                    'medical_certificate' => ['required' => true, 'label' => 'Certificat médical'],
                    'emergency_contact' => ['required' => true, 'label' => 'Contact d\'urgence']
                ]),
                'display_order' => 8
            ],
            [
                'type_key' => 'religion',
                'type_name' => 'Religion',
                'type_description' => 'Événements religieux, cérémonies, célébrations',
                'icon' => 'pray',
                'color' => '#795548',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'religious_affiliation' => ['required' => false, 'label' => 'Affiliation religieuse'],
                    'special_requirements' => ['required' => false, 'label' => 'Exigences spéciales']
                ]),
                'display_order' => 9
            ],
            [
                'type_key' => 'mariage',
                'type_name' => 'Mariage',
                'type_description' => 'Mariages, cérémonies de mariage, réceptions',
                'icon' => 'heart',
                'color' => '#e91e63',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'relationship_to_couple' => ['required' => true, 'label' => 'Relation avec les mariés'],
                    'plus_one' => ['required' => false, 'label' => 'Accompagnateur'],
                    'dietary_restrictions' => ['required' => false, 'label' => 'Restrictions alimentaires']
                ]),
                'display_order' => 10
            ],
            [
                'type_key' => 'autres',
                'type_name' => 'Autres',
                'type_description' => 'Autres types d\'événements',
                'icon' => 'ellipsis-h',
                'color' => '#607d8b',
                'default_form_fields' => json_encode([
                    'full_name' => ['required' => true, 'label' => 'Nom complet'],
                    'email' => ['required' => true, 'label' => 'Email'],
                    'phone' => ['required' => true, 'label' => 'Téléphone'],
                    'organization' => ['required' => false, 'label' => 'Organisation'],
                    'position' => ['required' => false, 'label' => 'Poste']
                ]),
                'display_order' => 11
            ]
        ];

        foreach ($eventTypes as $type) {
            DB::table('event_types')->updateOrInsert(
                ['type_key' => $type['type_key']],
                array_merge($type, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        // Insérer les packs d'abonnement
        $subscriptionPacks = [
            [
                'pack_key' => 'standard',
                'pack_name' => 'Pack Standard',
                'pack_description' => 'Pack de base avec tickets par email et design basique',
                'pack_type' => 'standard',
                'commission_percentage' => 7.00,
                'setup_fee' => 0,
                'monthly_fee' => 0,
                'currency' => 'XOF',
                'email_tickets' => true,
                'whatsapp_tickets' => false,
                'custom_tickets' => false,
                'multi_ticket_purchase' => false,
                'multi_country_support' => false,
                'custom_domain' => true,
                'advanced_analytics' => true,
                'api_access' => false,
                'priority_support' => false,
                'max_events' => 3,
                'max_participants_per_event' => 100,
                'max_storage_mb' => 500,
                'max_ticket_types_per_event' => 3,
                'supported_countries' => json_encode(['CI']),
                'payment_methods' => json_encode(['mobile_money', 'bank_transfer']),
                'display_order' => 1
            ],
            [
                'pack_key' => 'premium',
                'pack_name' => 'Pack Premium',
                'pack_description' => 'Pack avancé avec tickets WhatsApp et design personnalisé',
                'pack_type' => 'premium',
                'commission_percentage' => 8.00,
                'setup_fee' => 0,
                'monthly_fee' => 0,
                'currency' => 'XOF',
                'email_tickets' => true,
                'whatsapp_tickets' => true,
                'custom_tickets' => true,
                'multi_ticket_purchase' => true,
                'multi_country_support' => false,
                'custom_domain' => true,
                'advanced_analytics' => true,
                'api_access' => true,
                'priority_support' => true,
                'max_events' => 10,
                'max_participants_per_event' => 500,
                'max_storage_mb' => 2000,
                'max_ticket_types_per_event' => 10,
                'supported_countries' => json_encode(['CI']),
                'payment_methods' => json_encode(['mobile_money', 'bank_transfer', 'credit_card']),
                'display_order' => 2
            ],
            [
                'pack_key' => 'custom',
                'pack_name' => 'Pack Personnalisé',
                'pack_description' => 'Pack sur mesure avec toutes les fonctionnalités',
                'pack_type' => 'custom',
                'commission_percentage' => 0.00, // Négociable
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
                'max_events' => 999,
                'max_participants_per_event' => 9999,
                'max_storage_mb' => 10000,
                'max_ticket_types_per_event' => 50,
                'supported_countries' => json_encode(['CI', 'BJ', 'TG', 'SN', 'CM', 'ML', 'BF', 'FR', 'US']),
                'payment_methods' => json_encode(['mobile_money', 'bank_transfer', 'credit_card', 'paypal', 'crypto']),
                'display_order' => 3
            ]
        ];

        foreach ($subscriptionPacks as $pack) {
            DB::table('subscription_packs')->updateOrInsert(
                ['pack_key' => $pack['pack_key']],
                array_merge($pack, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        // Insérer les pays supportés
        $countries = [
            [
                'country_code' => 'CI',
                'country_name' => 'Côte d\'Ivoire',
                'country_name_fr' => 'Côte d\'Ivoire',
                'phone_code' => '+225',
                'currency_code' => 'XOF',
                'currency_symbol' => 'FCFA',
                'flag_emoji' => 'CI',
                'payment_providers' => json_encode(['mtn_money', 'orange_money', 'moov_money', 'bank_transfer']),
                'phone_format' => json_encode(['XX XX XX XX XX']),
                'display_order' => 1
            ],
            [
                'country_code' => 'BJ',
                'country_name' => 'Benin',
                'country_name_fr' => 'Bénin',
                'phone_code' => '+229',
                'currency_code' => 'XOF',
                'currency_symbol' => 'FCFA',
                'flag_emoji' => 'BJ',
                'payment_providers' => json_encode(['mtn_money', 'moov_money', 'bank_transfer']),
                'phone_format' => json_encode(['XX XX XX XX']),
                'display_order' => 2
            ],
            [
                'country_code' => 'TG',
                'country_name' => 'Togo',
                'country_name_fr' => 'Togo',
                'phone_code' => '+228',
                'currency_code' => 'XOF',
                'currency_symbol' => 'FCFA',
                'flag_emoji' => 'TG',
                'payment_providers' => json_encode(['moov_money', 'togocel_money', 'bank_transfer']),
                'phone_format' => json_encode(['XX XX XX XX']),
                'display_order' => 3
            ],
            [
                'country_code' => 'SN',
                'country_name' => 'Senegal',
                'country_name_fr' => 'Sénégal',
                'phone_code' => '+221',
                'currency_code' => 'XOF',
                'currency_symbol' => 'FCFA',
                'flag_emoji' => 'SN',
                'payment_providers' => json_encode(['orange_money', 'free_money', 'bank_transfer']),
                'phone_format' => json_encode(['XX XXX XX XX']),
                'display_order' => 4
            ],
            [
                'country_code' => 'CM',
                'country_name' => 'Cameroon',
                'country_name_fr' => 'Cameroun',
                'phone_code' => '+237',
                'currency_code' => 'XAF',
                'currency_symbol' => 'FCFA',
                'flag_emoji' => 'CM',
                'payment_providers' => json_encode(['mtn_money', 'orange_money', 'bank_transfer']),
                'phone_format' => json_encode(['XX XX XX XX']),
                'display_order' => 5
            ],
            [
                'country_code' => 'ML',
                'country_name' => 'Mali',
                'country_name_fr' => 'Mali',
                'phone_code' => '+223',
                'currency_code' => 'XOF',
                'currency_symbol' => 'FCFA',
                'flag_emoji' => 'ML',
                'payment_providers' => json_encode(['orange_money', 'malitel_money', 'bank_transfer']),
                'phone_format' => json_encode(['XX XX XX XX']),
                'display_order' => 6
            ],
            [
                'country_code' => 'BF',
                'country_name' => 'Burkina Faso',
                'country_name_fr' => 'Burkina Faso',
                'phone_code' => '+226',
                'currency_code' => 'XOF',
                'currency_symbol' => 'FCFA',
                'flag_emoji' => 'BF',
                'payment_providers' => json_encode(['orange_money', 'moov_money', 'bank_transfer']),
                'phone_format' => json_encode(['XX XX XX XX']),
                'display_order' => 7
            ]
        ];

        foreach ($countries as $country) {
            DB::table('supported_countries')->updateOrInsert(
                ['country_code' => $country['country_code']],
                array_merge($country, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }
}
