<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlongConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params=[
            ['bonusRegister'=>0,'creditPostDaily'=>0],
            
           
        ];
        DB::table('blog_configurations')->insert($params);
    }
}
