<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditPointsPromotionsRequest extends FormRequest
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
          'user_id' => "sometimes|required|integer|exists:users,id",
          'credit_points_id' => "sometimes|required|integer",
          'title' => "required|string|between:2,191",
          'description' => "required|string|max:10000",
          'point' => "required|integer",
          'amount' => "required|string",
          'expiry' => "date|date_format:Y-m-d|",
        ];
    }
}
