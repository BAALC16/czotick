<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use DB;
use Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = [
            [
                'name'          => 'Abobo',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Adjamé',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Anyama',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Attécoubé',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Bingerville',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Cocody',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Koumassi',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Marcory',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Plateau',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Port-Bouët',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Songon',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Treichville',
                'is_abidjan'     => true
            ],
            [
                'name'          => 'Yopougon',
                'is_abidjan'     => true
            ],
        ];

        foreach ($cities as $c) {
          if(!(City::where('name', $c['name'])->exists())) {
            $c["slug"] = str_slug($c['name']);
            City::create($c);
          }
        }
    }
}
