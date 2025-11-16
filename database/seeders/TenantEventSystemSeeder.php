<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantEventSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insérer les types d'événements pour les organisations
        $eventTypes = [
            [
                'type_key' => 'concert_spectacle',
                'type_name' => 'Concert & Spectacle',
                'type_description' => 'Concerts, spectacles, représentations artistiques',
                'icon' => 'music',
                'color' => '#e91e63',
                'display_order' => 1
            ],
            [
                'type_key' => 'formation',
                'type_name' => 'Formation',
                'type_description' => 'Formations professionnelles, ateliers, séminaires',
                'icon' => 'graduation-cap',
                'color' => '#2196f3',
                'display_order' => 2
            ],
            [
                'type_key' => 'conference',
                'type_name' => 'Conférence',
                'type_description' => 'Conférences, colloques, symposiums',
                'icon' => 'microphone',
                'color' => '#4caf50',
                'display_order' => 3
            ],
            [
                'type_key' => 'festival',
                'type_name' => 'Festival',
                'type_description' => 'Festivals culturels, musicaux, artistiques',
                'icon' => 'calendar-alt',
                'color' => '#ff9800',
                'display_order' => 4
            ],
            [
                'type_key' => 'soiree',
                'type_name' => 'Soirée',
                'type_description' => 'Soirées, galas, événements sociaux',
                'icon' => 'glass-cheers',
                'color' => '#9c27b0',
                'display_order' => 5
            ],
            [
                'type_key' => 'gastronomie',
                'type_name' => 'Gastronomie',
                'type_description' => 'Événements culinaires, dégustations, cours de cuisine',
                'icon' => 'utensils',
                'color' => '#f44336',
                'display_order' => 6
            ],
            [
                'type_key' => 'tourisme',
                'type_name' => 'Tourisme',
                'type_description' => 'Événements touristiques, visites, excursions',
                'icon' => 'map-marked-alt',
                'color' => '#00bcd4',
                'display_order' => 7
            ],
            [
                'type_key' => 'sport',
                'type_name' => 'Sport',
                'type_description' => 'Événements sportifs, compétitions, tournois',
                'icon' => 'running',
                'color' => '#4caf50',
                'display_order' => 8
            ],
            [
                'type_key' => 'religion',
                'type_name' => 'Religion',
                'type_description' => 'Événements religieux, cérémonies, célébrations',
                'icon' => 'pray',
                'color' => '#795548',
                'display_order' => 9
            ],
            [
                'type_key' => 'mariage',
                'type_name' => 'Mariage',
                'type_description' => 'Mariages, cérémonies de mariage, réceptions',
                'icon' => 'heart',
                'color' => '#e91e63',
                'display_order' => 10
            ],
            [
                'type_key' => 'autres',
                'type_name' => 'Autres',
                'type_description' => 'Autres types d\'événements',
                'icon' => 'ellipsis-h',
                'color' => '#607d8b',
                'display_order' => 11
            ]
        ];

        foreach ($eventTypes as $type) {
            DB::table('event_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Insérer les pays supportés pour les organisations (uniquement si la table existe)
        if (Schema::hasTable('supported_countries')) {
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
                DB::table('supported_countries')->insert(array_merge($country, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }
    }
}
