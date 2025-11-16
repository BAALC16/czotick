<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
          'text' => "nullable|string|max:5000",
          'fichiers' => "sometimes|array|min:1|max:30",
          'fichiers.*' => "sometimes|file|max:50000|mimes:jpg,jpeg,gif,png,doc,docx,ppt,pptx,pdf,csv,xls,xlsx,txt,bmp"
        ];
    }
}
