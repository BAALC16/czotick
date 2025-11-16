<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
              'label' => "Conception de plans de maison",
              'prix' => 50000,
              'description' => "Pour tous vos plans de maisons pour permis de construire et d'exécution",
              'image' => "/images/service-plan.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
            [
              'label' => "Travaux de Rénovation",
              'prix' => 50000,
              'description' => "Travaux de rénovation pour appartements ou villas",
              'image' => "/images/service-renovation.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
            [
              'label' => "Suivi de Chantier",
              'prix' => 50000,
              'description' => "Pour un suivi efficace de vos travaux à distance",
              'image' => "/images/service-site.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
            [
              'label' => "Déménagement",
              'prix' => 50000,
              'description' => "Vous n'avez plus à vous inquiéter pour le déplacement de vos effets",
              'image' => "/images/service-moving-truck.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
            [
              'label' => "Conception de plans de maison",
              'prix' => 50000,
              'description' => "Pour tous vos plans de maisons pour permis de construire et d'exécution",
              'image' => "/images/service-plan.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
            [
              'label' => "Travaux de Rénovation",
              'prix' => 50000,
              'description' => "Travaux de rénovation pour appartements ou villas",
              'image' => "/images/service-renovation.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
            [
              'label' => "Suivi de Chantier",
              'prix' => 50000,
              'description' => "Pour un suivi efficace de vos travaux à distance",
              'image' => "/images/service-site.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
            [
              'label' => "Déménagement",
              'prix' => 50000,
              'description' => "Vous n'avez plus à vous inquiéter pour le déplacement de vos effets",
              'image' => "/images/service-moving-truck.png",
              'icon' => "",
              'actif' => true,
              'user_id' => 1,
              'views' => 0,
            ],
        ];

        foreach ($services as $s) {
          $s['slug'] = str_slug($s['label']);  
          if(!(Service::where('slug', $s['slug'])->exists())) {
            Service::create($s);
          }
        }
    }
}
