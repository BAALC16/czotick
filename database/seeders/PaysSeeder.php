<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class PaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(Country::count())) {
            $countries = collect(json_decode(file_get_contents(database_path('seeders/countries.json')), true));
            Country::insert($countries->map(function($row) {
                return [
                    'code' => $row['code'],
                    'nom' => $row['name'],
                    'prefix_telephone' => $row['dial_code'],
                    'drapeau' => NULL,
                ];
            })->toArray());
        }
    }
}
