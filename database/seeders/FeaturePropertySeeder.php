<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;
use App\Models\PropertyType;

class FeaturePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $feature = new Feature([
            'name' => "Piscine",
            'slug' => "piscine",
            'icon' => "las la-swimmer"
        ]);

        if (Feature::where('slug', $feature->slug)->count() <= 0) {
            $feature->saveQuietly();
            $propertyTypes = PropertyType::whereIn('slug', ['appartement', 'villa'])->get();
            $propertyTypes->each(function($propertyType) use($feature) {
              $feature->propertyTypes()->attach($propertyType);
            });   
        }
        
        $feature = new Feature([
            'name' => "Sécurité",
            'slug' => "securite",
            'icon' => "bx bx-cctv"
        ]);

        if (Feature::where('slug', $feature->slug)->count() <= 0) {
            $feature->saveQuietly();
            $propertyTypes = PropertyType::whereIn('slug', ['appartement', 'villa', 'bureau', 'magasin'])->get();
            $propertyTypes->each(function($propertyType) use($feature) {
              $feature->propertyTypes()->attach($propertyType);
            });
        }
        
        $feature = new Feature([
            'name' => "Chauffe-eau",
            'slug' => "chauffe-eau",
            'icon' => "las la-hot-tub"
        ]);

        if (Feature::where('slug', $feature->slug)->count() <= 0) {
            $feature->saveQuietly();
            $propertyTypes = PropertyType::whereIn('slug', ['appartement', 'villa'])->get();
            $propertyTypes->each(function($propertyType) use($feature) {
              $feature->propertyTypes()->attach($propertyType);
            });
        }
        $feature = new Feature([
            'name' => "Interphone",
            'slug' => "interphone",
            'icon' => "las la-concierge-bell"
        ]);

        if (Feature::where('slug', $feature->slug)->count() <= 0) {
            $feature->saveQuietly();
            $propertyTypes = PropertyType::whereIn('slug', ['appartement', 'villa', 'bureau', 'magasin'])->get();
            $propertyTypes->each(function($propertyType) use($feature) {
              $feature->propertyTypes()->attach($propertyType);
            });
        }
        
        $feature = new Feature([
            'name' => "Ascenseur",
            'slug' => "ascenseur",
            'icon' => "las la-upload"
        ]);

        if (Feature::where('slug', $feature->slug)->count() <= 0) {
            $feature->saveQuietly();
            $propertyTypes = PropertyType::whereIn('slug', ['appartement', 'villa', 'bureau', 'magasin'])->get();
            $propertyTypes->each(function($propertyType) use($feature) {
              $feature->propertyTypes()->attach($propertyType);
            });
        }
        
        $feature = new Feature([
            'name' => "Parking",
            'slug' => "parking",
            'icon' => "las la-car"
        ]);

        if (Feature::where('slug', $feature->slug)->count() <= 0) {
            $feature->saveQuietly();
            $propertyTypes = PropertyType::whereIn('slug', ['appartement', 'villa', 'bureau', 'magasin'])->get();
            $propertyTypes->each(function($propertyType) use($feature) {
              $feature->propertyTypes()->attach($propertyType);
            });
        }

        
        $feature = new Feature([
            'name' => "Fibre Optique",
            'slug' => "fiber",
            'icon' => "las la-wifi"
        ]);

        if (Feature::where('slug', $feature->slug)->count() <= 0) {
            $feature->saveQuietly();
            $propertyTypes = PropertyType::whereIn('slug', ['appartement', 'villa', 'bureau', 'magasin'])->get();
            $propertyTypes->each(function($propertyType) use($feature) {
              $feature->propertyTypes()->attach($propertyType);
            });
        }
        
    }
}
