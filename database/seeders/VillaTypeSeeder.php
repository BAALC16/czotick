<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VillaType;
use DB;
use Str;

class VillaTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $villaTypes = [
            [
                'name'          => 'Basse',
            ],
            [
                'name'          => 'Duplex',
            ],
            [
                'name'          => 'Triplex',
            ],
            [
                'name'          => 'Triplex+',
            ],
        ];

        foreach ($villaTypes as $v) {
          if(!(VillaType::where('name', $v['name'])->exists())) {
            VillaType::create($v);
          }
        }
    }
}
