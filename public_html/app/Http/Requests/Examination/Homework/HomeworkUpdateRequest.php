<?php

namespace App\Http\Requests\Examination\Homework;

use Illuminate\Foundation\Http\FormRequest;

class HomeworkUpdateRequest extends FormRequest
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
            'class'              => 'required',
            'section'            => 'required',
            'subject'            => 'required',
            'date'               => 'required',
            'marks'              => 'required|numeric', // Ensures it's mandatory AND a number
            'question_group'     => 'required',         // Makes Homework Group mandatory
            'task_type'          => 'required',         // Makes Task Category mandatory
            'title'              => 'required',         // Makes Homework Title mandatory
        ];
    }
}
