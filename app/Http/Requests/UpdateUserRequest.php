<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
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
          'email' => "required|string|email|max:64",
          'old_password' => "sometimes|nullable|string|current_password",
          'new_password' => ['nullable', 'confirmed', Rules\Password::defaults()],
          'nom' => "required|string|between:2,64",
          'prenoms' => "required|string|between:2,64",
          'photo_file' => "sometimes|image|max:20000",
          'titre' => "nullable|string|max:160",
          'introduction' => "nullable|string|max:500",
          'ville' => "nullable|string|max:100",
          'code_pays' => "nullable|string|max:3|exists:pays,code",
          'email_pro' => "nullable|string|email|max:64",
          'mobile' => "nullable|string|max:30",
          'telephone' => "nullable|string|max:30",
          'site_web' => "nullable|url|max:64",
          'twitter' => "nullable|url|max:64",
          'facebook' => "nullable|url|max:64",
          'instagram' => "nullable|url|max:64",
          'linkedin' => "nullable|url|max:64",
          'points_disponibles' => "numeric",
          'continue' => "sometimes|required|url",
        ];
    }
}
