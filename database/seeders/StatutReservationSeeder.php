<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Statut;

class StatutReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
              'code' => "DEM_INITIEE",
              'label' => "Création en cours",
              'color' => "secondary",
              'final_state' => false,
              'statusable' => "Reservation",
            ],
            [
              'code' => "DEM_SOUMISE",
              'label' => "En attente",
              'color' => "primary",
              'final_state' => false,
              'statusable' => "Reservation",
            ],
            [
              'code' => "DEM_ACCEPTEE",
              'label' => "Acceptée",
              'color' => "success",
              'final_state' => false,
              'statusable' => "Reservation",
            ],
            [
              'code' => "DEM_REJETEE",
              'label' => "Rejetée",
              'color' => "danger",
              'final_state' => false,
              'statusable' => "Reservation",
            ],
            [
              'code' => "DEM_TRAITEE",
              'label' => "Traitée",
              'color' => "info",
              'final_state' => true,
              'statusable' => "Reservation",
            ],
            [
              'code' => "DEM_ATT_PAIEMENT",
              'label' => "Paiement en attente",
              'color' => "info",
              'final_state' => false,
              'statusable' => "Reservation",
            ],
            [
              'code' => "DEM_EN_COURS",
              'label' => "Traitement en cours",
              'color' => "info",
              'final_state' => false,
              'statusable' => "Reservation",
            ],
        ];
        $reservation_statuses = Statut::where('statusable', 'Reservation')->get();
        $new_statuses = [];
        foreach ($statuses as $status) {
          if(!($reservation_statuses->contains('code', $status['code']))) {
            $new_statuses[] = $status;
          }
        }
        if(!empty($new_statuses)) Statut::insert($new_statuses);
    }
}
