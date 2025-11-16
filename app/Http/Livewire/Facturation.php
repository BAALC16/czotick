<?php

namespace App\Http\Livewire;

use App\Models\BlogConfiguration;
use Livewire\Component;

class Facturation extends Component
{
    public   $nbrJours  ;
    public  $costPostDaily ;
    public   $total;
    public   $service_id;
  
    public function render()
    {
        $this->costPostDaily= BlogConfiguration::find(1)->creditPostDaily;
            if ($this->nbrJours ==""){
                $this->nbrJours=0;
            }
            $this->total =$this->nbrJours*$this->costPostDaily;
       
        return view('livewire.facturation');
    }
}
