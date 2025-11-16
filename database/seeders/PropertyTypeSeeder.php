<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;
use DB;
use Str;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        /*

            $table->increments('id');
            $table->string('name');
            $table->string('icon');
            */
        $propertyTypes = [
            [
                'name'          => 'Appartement',
                'slug'          => 'appartement',
                'icon'           => 'la-building'
            ],
            [
                'name'          => 'Villa',
                'slug'          => 'villa',
                'icon'           => 'la-home'
            ],
            [
                'name'          => 'Terrain',
                'slug'          => 'terrain',
                'icon'           => 'la-map-marked'
            ],
            [
                'name'          => 'Bureau',
                'slug'          => 'bureau',
                'icon'           => 'la-city'
            ],
            [
                'name'          => 'Magasin',
                'slug'          => 'magasin',
                'icon'           => 'la-store'
            ],
        ];

        foreach ($propertyTypes as $p) {
          if(!(PropertyType::where('name', $p['name'])->exists())) {
            PropertyType::create($p);
          }
        }
    }
}
