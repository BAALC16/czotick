<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
          'user_id' => "sometimes|required|integer|exists:users,id",
          'note' => "nullable|string|max:5000",
          'last_name' => "required|string|max:100",
          'first_name' => "required|string|max:100",
          'email' => "required|string|email|max:64",
          'mobile' => "required|digits:10",
        ];

/*
        $service = $this->route('service');
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
*/
        return $rules;
    }
}
