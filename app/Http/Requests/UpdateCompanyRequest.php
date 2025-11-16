<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateCompanyRequest extends FormRequest
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
          'email' => "nullable|string|email|max:64",
          'title' => "required|string|between:2,64",
          'address' => "nullable|string|between:2,64",
          'photo_file' => "sometimes|image|max:20000",
          'description' => "required|string|max:500",
          'physical_address' => "nullable|string|max:500",
          'timing' => "required|string|max:500",
          'phone' => "nullable|string|max:30",
          'rc' => "nullable|string|max:50",
          'cc' => "nullable|string|max:50",
          'cnps' => "nullable|string|max:50",
          'facebook' => "nullable|string|max:50",
          'linkedin' => "nullable|string|max:50",
          'twitter' => "nullable|string|max:50",
          'instagram' => "nullable|string|max:50",
          'web' => "nullable|string|max:50",
      ];
    }
}
