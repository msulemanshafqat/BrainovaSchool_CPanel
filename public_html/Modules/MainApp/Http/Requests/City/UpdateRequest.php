<?php

namespace Modules\MainApp\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'country_id' => 'required|exists:countries,id',
            'name'       => 'required|string|max:255',
            'status'     => 'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'country_id.required' => ___('mainapp_validation.The country field is required.'),
            'country_id.exists'   => ___('mainapp_validation.The selected country is invalid.'),

            'name.required'       => ___('mainapp_validation.The city name is required.'),
            'name.string'         => ___('mainapp_validation.The city name must be a string.'),
            'name.max'            => ___('mainapp_validation.The city name may not be greater than 255 characters.'),

            'status.required'     => ___('mainapp_validation.The status field is required.'),
            'status.in'           => ___('mainapp_validation.The selected status is invalid.'),
        ];
    }
}
