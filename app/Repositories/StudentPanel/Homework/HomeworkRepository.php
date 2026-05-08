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

    /**
     * Returns all homework for the student's current class/section.
     * Uses get() (not paginate) so the portal can group by subject and
     * compute per-subject chart data across the full dataset.
     */
    public function index()
    {
        return $this->model::with(['subject', 'upload'])
            ->where('classes_id', Auth::user()->student?->session_class_student?->classes_id)
            ->where('section_id', Auth::user()->student?->session_class_student?->section_id)
            ->where('session_id', setting('session'))
            ->orderByDesc('id')
            ->get();
    }

    public function show($id)
    {
        // Note: examQuestions loads from homework_questions → question_banks (online-exam).
        // Homework quiz questions live in homework_quiz_questions — handled separately.
        return $this->model::find($id);
    }

    /**
     * Standard homework file submission (non-quiz task types).
     * Stores the uploaded file and records the homework as submitted.
     *
     * IMPORTANT: UploadImageUpdate MUST be called — it was previously commented
     * out, which meant files were never stored and the submission status never
     * persisted across page reloads.
     */
    public function submit($request)
    {
        DB::beginTransaction();
        try {
            $student = Auth::user()->student;

            $homework_student = HomeworkStudent::where('student_id', $student->id)
                ->where('homework_id', $request->homework_id)
                ->first();

            if (!$homework_student) {
                $homework_student           = new HomeworkStudent();
                $homework_student->homework = null;
            }

            $homework_student->student_id  = $student->id;
            $homework_student->homework_id = $request->homework_id;
            $homework_student->date        = date('Y-m-d');

            $files = $request->file('homework');
            if (!is_array($files)) {
                $files = array_values(array_filter([$files]));
            }
            $files = array_slice($files, 0, 5);

            foreach ($homework_student->extra_upload_ids ?? [] as $oldId) {
                if ($oldId) {
                    $this->UploadImageDelete((int) $oldId);
                }
            }

            $firstFile = $files[0];
            $homework_student->homework = $this->UploadImageUpdate(
                $firstFile,
                'backend/uploads/homeworks',
                $homework_student->homework
            );

            $extraIds = [];
            for ($i = 1; $i < count($files); $i++) {
                $extraIds[] = $this->UploadImageUpdate($files[$i], 'backend/uploads/homeworks', null);
            }
            $homework_student->extra_upload_ids = $extraIds !== [] ? $extraIds : null;

            $comment = $request->input('student_comment');
            $homework_student->student_comment = ($comment !== null && trim((string) $comment) !== '')
                ? trim((string) $comment)
                : null;

            $homework_student->save();
            DB::commit();
            return $homework_student;

        } catch (\Throwable $th) {
            DB::rollBack();
            // Never dd() in production — it dumps sensitive data to the browser.
            \Log::error('Student Homework Submit Error: ' . $th->getMessage());
            throw $th;
        }
    }
}
