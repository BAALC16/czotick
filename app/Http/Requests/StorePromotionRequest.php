<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
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
          'title' => "required|string|between:2,191",
          'project' => "required|string|between:2,191",
          'city' => "required|string",
          'address' => "required|string|between:2,191",
          'developer' => "required|string|between:2,191",
          'description' => "required|string|max:10000",
          'video' => "nullable|string|between:2,191",
        ];
    }
}
