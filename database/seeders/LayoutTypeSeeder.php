<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LayoutType;
use DB;
use Str;

class LayoutTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $layouts = [
            [
                'name'          => 'Studio',
            ],
            [
                'name'          => '2 pièces',
            ],
            [
                'name'          => '3 pièces',
            ],
            [
                'name'          => '4 pièces',
            ],
            [
                'name'          => '5 pièces',
            ],
            [
                'name'          => '6 pièces',
            ],
            [
                'name'          => '+ de 6 pièces',
            ]
        ];

        foreach ($layouts as $l) {
            $l["slug"] = str_slug($l['name']);
            if (!(LayoutType::where('slug', $l['slug'])->exists())) {
                LayoutType::create($l);
            }
        }
    }
}
