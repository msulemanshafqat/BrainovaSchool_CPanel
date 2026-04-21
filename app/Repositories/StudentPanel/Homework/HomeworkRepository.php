<?php

namespace App\Repositories\StudentPanel\Homework;

use App\Models\Homework;
use App\Models\HomeworkStudent;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeworkRepository implements HomeworkInterface
{
    use ReturnFormatTrait, CommonHelperTrait;

    private $model;

    public function __construct(Homework $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->model::where('classes_id', Auth::user()->student?->session_class_student?->classes_id)
            ->where('section_id', Auth::user()->student?->session_class_student?->section_id)
            ->where('session_id', setting('session'))
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function show($id)
    {
        // examQuestions loads from homework_questions → question_banks (online-exam).
        // Homework quiz questions are in homework_quiz_questions and are handled separately.
        return $this->model::find($id);
    }

    /**
     * Standard homework file submission (non-quiz task types).
     * Stores the uploaded file and marks the homework as submitted.
     */
    public function submit($request)
    {
        DB::beginTransaction();
        try {
            $student = Auth::user()->student;

            $homeworkStudent = HomeworkStudent::where('student_id', $student->id)
                ->where('homework_id', $request->homework_id)
                ->first();

            if (!$homeworkStudent) {
                $homeworkStudent = new HomeworkStudent();
            }

            $homeworkStudent->student_id  = $student->id;
            $homeworkStudent->homework_id = $request->homework_id;
            $homeworkStudent->date        = now()->format('Y-m-d');

            if ($request->hasFile('homework')) {
                $homeworkStudent->homework = $this->UploadImageUpdate(
                    $request->homework,
                    'backend/uploads/homeworks',
                    $homeworkStudent->homework
                );
            }

            $homeworkStudent->save();

            DB::commit();
            return $homeworkStudent;

        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Homework Submit Error: ' . $th->getMessage());
            throw $th;
        }
    }
}
