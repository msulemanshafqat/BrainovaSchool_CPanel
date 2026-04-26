<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BehaviourRecordStoreRequest extends FormRequest
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
            'incident_id'=> 'required|exists:incidents,id',
            'student_id'=> 'required|exists:students,id',
            'short_note' => 'nullable|string|max:150',
        ];
    }
}
