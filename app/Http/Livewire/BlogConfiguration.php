<?php

namespace App\Http\Livewire;

use App\Models\BlogConfiguration as ModelsBlogConfiguration;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Livewire\Component;
use App\Models\Trace;


class BlogConfiguration extends Component
{
  public $bonusRegister;
  public $creditPostDaily;
  public $rules = [
    'bonusRegister' => "numeric|min:0",
    'creditPostDaily' => "numeric|min:0",
  ];
  public function render()
  {
    $configuration = ModelsBlogConfiguration::find(1);

    $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Modifier la configuration des articles", 'description' => "Modification de la configuration des articles", 'resource' => $configuration->id]);
    $trace->save();

    return view('livewire.blog-configuration', [
      'configuration' => $configuration, 'edit' => true
    ]);
  }

  public function edit()
  {
    // $input = $this->validate();
    $configuration = ModelsBlogConfiguration::find(1);

    if($this->bonusRegister== null || $this->bonusRegister=="" ){
      $this->bonusRegister=$configuration->bonusRegister;
    }
    if($this->creditPostDaily== null || $this->creditPostDaily=="" ){
      $this->creditPostDaily=$configuration->creditPostDaily;
    }


      $configuration->bonusRegister = $this->bonusRegister;
      $configuration->creditPostDaily = $this->creditPostDaily;


    $configuration->update();
    $this->bonusRegister=null;
    $this->creditPostDaily=null;
  }
}
