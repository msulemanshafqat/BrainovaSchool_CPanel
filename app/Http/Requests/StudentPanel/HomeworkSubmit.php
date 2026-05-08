<?php

namespace App\Http\Requests\StudentPanel;

use Illuminate\Foundation\Http\FormRequest;

class HomeworkSubmit extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if ($this->route()->getName() === 'student-panel.homework.submit') {
            return [
                'homework_id'     => 'required|integer|exists:homework,id',
                'student_comment' => 'nullable|string|max:2000',
                'homework'        => 'required|array|min:1|max:5',
                'homework.*'      => 'file|max:10240|mimes:jpeg,jpg,png,webp,gif,pdf,doc,docx',
            ];
        }

        return [
            'homework'     => 'required',
            'homework_id'  => 'required',
        ];
    }
}
