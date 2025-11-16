<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reservation;
use App\Models\Service;

class SubmitReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
          'note' => "nullable|string|max:5000",
        ];

        $reservation = $this->route('reservation');
        $service = $reservation->service;
        foreach ($service->attributs as $attr) {
          if($attr->type_champ == 'text') {
            $rules['attributs.'.$attr->id] = "nullable|string|max:191";
          } elseif ($attr->type_champ == "textarea") {
            $rules['attributs.'.$attr->id] = "nullable|string|max:5000";
          } elseif ($attr->type_champ == "file") {
            $rules['attributs.'.$attr->id] = "sometimes|file|mimes:jpg,jpeg,gif,png,doc,docx,ppt,pptx,pdf,csv,xls,xlsx,txt,bmp|max:50000";
          } elseif ($attr->type_champ == "files") {
            $rules['attributs.'.$attr->id.'.*.file'] = "sometimes|file|mimes:jpg,jpeg,gif,png,doc,docx,ppt,pptx,pdf,csv,xls,xlsx,txt,bmp|max:50000";
          }
        }

        return $rules;
    }
}
