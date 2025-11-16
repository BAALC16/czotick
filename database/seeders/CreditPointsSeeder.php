<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditPoints;

class CreditPointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $creditPoints = [
            [
                'point'   => 1,
                'amount'  => '1',
                'type'  => 'D',
            ],
            [
                'point'   => 0,
                'amount'  => '0',
                'type'  => 'I',
            ],

        ];

        foreach ($creditPoints as $c) {
            if(!(CreditPoints::where('type', $c['type'])->exists())) {
                CreditPoints::create($c);
            }
        }
    }
}
