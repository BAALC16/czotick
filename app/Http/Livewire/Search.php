<?php

namespace App\Http\Livewire;

use App\Models\Article;
use App\Models\Service;
use App\Models\Property;
use Livewire\Component;

class Search extends Component
{
    public  $query;
    public $sugArticles = [];
    public $sugServices =[];
    public $sugProperties =[];


    public function updatedQuery()
    {
        if (strlen($this->query > 2)) {
            //search for posts
            $words = '%' .$this->query.'%';
            $this->sugArticles = Article::where('title', 'like', $words)
                ->orWhere('content', 'like', $words)
                ->first();
            //search for services
            $this->sugServices = Service::where('label', 'like', $words)
                ->orWhere('description', 'like', $words)
                ->orWhere('slug', 'like', $words)
                ->first();

            //search for properties
            $this->sugProperties = Property::where('title', 'like', $words)
                ->orWhere('description', 'like', $words)
                ->orWhere('slug', 'like', $words)
                ->first();

        }

    }
    public function render()
    {
        if (strlen($this->query > 2)) {
            //search for posts
            $words = '%' .$this->query.'%';
            $this->sugArticles = Article::where('title', 'like', $words)
                ->where('actif', 1)
                ->orWhere('content', 'like', $words)
                ->first();
            //search for services
            $this->sugServices = Service::where('label', 'like', $words)
                ->orWhere('description', 'like', $words)
                ->orWhere('slug', 'like', $words)
                ->first();

            $this->sugProperties = Property::join('property_types', 'property_types.id', '=', 'properties.property_type_id')
                ->join('cities', 'cities.id', '=', 'properties.city')
                ->where('properties.title', 'like', $words)
                ->where('published', 1)
                ->orWhere('properties.description', 'like', $words)
                ->orWhere('properties.slug', 'like', $words)
                ->orWhere('properties.purpose', 'like', $words)
                ->orWhere('cities.name', 'like', $words)
                ->orWhere('cities.slug', 'like', $words)
                ->first();

        }

        return view('livewire.search', ['newVersion' => true,'sugArticles' => $this->sugArticles, 'sugServices' => $this->sugServices, 'sugProperties' => $this->sugProperties]);
    }
}
