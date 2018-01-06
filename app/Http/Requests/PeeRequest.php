<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeeRequest extends FormRequest
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
            "age" => "required|numeric|min:1|max:150",
            "weight" => "required|numeric|min:10|max:220",
            "height" => "required|numeric|min:20|max:270",
            "consumption" => "required|numeric|min:0|max:100"
        ];
    }
}
