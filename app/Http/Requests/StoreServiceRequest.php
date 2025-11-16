<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
        return [
          'label' => "required|string|between:2,191",
          'prix' => "required|numeric",
          'icon' => "required",
          'description' => "nullable|string|max:10000",
        //   'image_file' => "image",
          'actif' => "sometimes|boolean",
          /*
          'attributs' => "sometimes|array|min:1",
          'attributs.*' => "sometimes|array|max:4",
          'attributs.*.label' => "sometimes|required|string|max:191",
          'attributs.*.description' => "sometimes|nullable|string|max:191",
          'attributs.*.icone' => "sometimes|nullable|string|max:191",
          'attributs.*.type_champ' => "sometimes|required|string|in:text,textarea,file,files",
          */
          'continue' => "sometimes|url",
        ];
    }
}
