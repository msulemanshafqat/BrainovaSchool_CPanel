<?php

namespace App\Http\Requests\Examination\Homework;

use Illuminate\Foundation\Http\FormRequest;

class HomeworkStoreRequest extends FormRequest
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
        'class'               => 'required',
        'section'             => 'required',
        'subject'             => 'required',
        'date'                => 'required',
        'marks'               => 'required|numeric', 
        'task_type'           => 'required', 
        'title'               => 'required', 
        
        // THIS MUST BE NULLABLE. If it says 'required' or 'required_if', the form will crash!
        'document'            => 'nullable|file',
        
        // This is what makes the CSV mandatory ONLY for Quizzes
        'bulk_questions_file' => 'required_if:task_type,quiz|file|mimes:csv,txt',
    ];
}

}
