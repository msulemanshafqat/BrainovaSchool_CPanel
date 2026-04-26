<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialDiscountStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:25',
            'type' => 'required',
            'id'   => 'nullable|integer|exists:special_discounts,id',
            'discount_amount' => 'required|integer',
            'min_amount' => 'nullable|numeric',
            'max_amount' => 'nullable|numeric',
            'min_eligible_amount' => 'nullable|numeric',
            'max_eligible_amount' => 'nullable|numeric',
            'status' => 'required',
            'short_desc' => 'nullable|string|max:255',
        ];
    }
}
