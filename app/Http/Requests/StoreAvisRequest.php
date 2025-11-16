<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvisRequest extends FormRequest
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
          'nom' => "required|string|max:100",
          'email' => "required|string|email|max:64",
          'service_id' => "required|integer|exists:services,id",
          'note' => "required|integer|between:1,5",
          'comment' => "required|string|max:1000",
        ];
    }
}
