<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevisRequest extends FormRequest
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
          'cout' => "required|string|between:1,15",
          'description' => "nullable|string|max:5000",
          'debut_execution' => "required|date|date_format:Y-m-d|after_or_equal:today",
          'fin_execution' => "sometimes|nullable|date|date_format:Y-m-d|after_or_equal:debut_execution",
        ];
    }
}
