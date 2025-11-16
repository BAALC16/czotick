<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
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

        return $rules;
    }
}
