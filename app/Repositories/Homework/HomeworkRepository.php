<?php

namespace App\Repositories\Homework;

use App\Models\Homework;
use App\Models\HomeworkStudent;
use App\Models\OnlineExamination\Answer;
use App\Models\OnlineExamination\AnswerChildren;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class HomeworkRepository implements HomeworkInterface
{
    use ReturnFormatTrait, CommonHelperTrait;

    private $model;

    public function __construct(Homework $model)
    {
        $this->model = $model;
    }

    // =========================================================================
    // READ
    // =========================================================================

    public function all()
    {
        return $this->model->active()
            ->where('session_id', setting('session'))
            ->orderByDesc('id')
            ->get();
    }

    public function getPaginateAll()
    {
        return $this->model::latest()
            ->where('session_id', setting('session'))
            ->whereIn('subject_id', teacherSubjects())
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function search($request)
    {
        $query = $this->model::query()->where('session_id', setting('session'));

        if (!empty($request->class)) {
            $query->where('classes_id', $request->class);
        }
        if (!empty($request->section)) {
            $query->where('section_id', $request->section);
        }
        if (!empty($request->subject)) {
            $query->where('subject_id', $request->subject);
        }

        return $query->orderByDesc('id')->paginate(10);
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    // =========================================================================
    // WRITE
    // =========================================================================

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $homework                  = new Homework();
            $homework->session_id      = setting('session');
            $homework->classes_id      = $request->class;
            $homework->section_id      = $request->section;
            $homework->subject_id      = $request->subject;
            $homework->topic           = $request->topic;
            $homework->title           = $request->title;
            $homework->task_type       = $request->task_type;
            $homework->date            = $request->date;
            $homework->submission_date = $request->submission_date;
            $homework->marks           = $request->marks;
            $homework->status          = $request->status ?? 1;
            $homework->description     = $request->description;

            // Save first so we have a homework->id for the CSV pivot inserts below.
            // document_id is updated in a second save after file processing.
            $homework->save();

            // Standard task: optional document (image / PDF / Word)
            if ($request->hasFile('document')) {
                $homework->document_id = $this->UploadImageCreate(
                    $request->document,
                    'backend/uploads/homeworks'
                );
                $homework->save();
            }

            // Quiz CSV: MUST process (parse rows) BEFORE calling UploadImageCreate.
            // UploadImageCreate moves the temp file from /tmp — after that move the
            // original path returned by getRealPath() no longer exists, causing the
            // "Failed to open stream: No such file or directory" error.
            if ($request->hasFile('bulk_questions_file')) {
                // Step 1 — parse CSV while the temp file is still at its original path
                $this->processQuizCsv($request->file('bulk_questions_file'), $homework->id);

                // Step 2 — now safely move/store the file for teacher re-download
                $homework->document_id = $this->UploadImageCreate(
                    $request->bulk_questions_file,
                    'backend/uploads/homeworks'
                );
                $homework->save();
            }

            DB::commit();
            return ['status' => true, 'message' => ___('alert.created_successfully')];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Homework Store Error: ' . $e->getMessage());
            return ['status' => false, 'message' => ___('alert.something_went_wrong')];
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $row                  = $this->model->findOrFail($id);
            $row->session_id      = setting('session');
            $row->classes_id      = $request->class;
            $row->section_id      = $request->section;
            $row->subject_id      = $request->subject;
            $row->topic           = $request->topic;
            $row->title           = $request->title;
            $row->task_type       = $request->task_type;
            $row->date            = $request->date;
            $row->submission_date = $request->submission_date;
            $row->marks           = $request->marks;
            $row->status          = $request->status ?? $row->status;
            $row->description     = $request->description;

            // Save core fields first
            $row->save();

            // Replace standard document if a new one is provided
            if ($request->hasFile('document')) {
                $row->document_id = $this->UploadImageUpdate(
                    $request->document,
                    'backend/uploads/homeworks',
                    $row->document_id
                );
                $row->save();
            }

            // Replace quiz CSV — process CSV BEFORE UploadImageUpdate moves the temp file
            if ($request->hasFile('bulk_questions_file')) {
                // Step 1 — delete old questions
                DB::table('homework_quiz_questions')->where('homework_id', $row->id)->delete();
                // Step 2 — parse new CSV while temp file still exists
                $this->processQuizCsv($request->file('bulk_questions_file'), $row->id);
                // Step 3 — now move/store the file (temp file consumed here)
                $row->document_id = $this->UploadImageUpdate(
                    $request->bulk_questions_file,
                    'backend/uploads/homeworks',
                    $row->document_id
                );
                $row->save();
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Homework Update Error: ' . $th->getMessage());
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->findOrFail($id);

            // Remove quiz questions linked to this homework
            DB::table('homework_quiz_questions')->where('homework_id', $id)->delete();

            // Remove uploaded file only if one exists (quizzes may have a CSV upload; others may not)
            if ($row->document_id) {
                $this->UploadImageDelete($row->document_id);
            }

            $row->delete();

            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    // =========================================================================
    // EVALUATION
    // =========================================================================

    /**
     * Standard homework manual evaluation.
     * Teacher reviews submitted work and assigns a mark per student.
     * Automatically awards Brainova E6 Points on save.
     */
    public function evaluationSubmit($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->students as $key => $studentId) {
                $record = HomeworkStudent::where('homework_id', $request->homework_id)
                    ->where('student_id', $studentId)
                    ->firstOrFail();

                $marks         = (int) $request->marks[$key];
                $record->marks = $marks;
                $record->save();

                $this->awardE6Points((int) $studentId, $marks);
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.submitted_successfully'), []);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    /**
     * Answer-sheet based marking for file-upload homework submissions.
     * Teacher grades each answer section; total is written back to homework_students.
     * Automatically awards Brainova E6 Points on save.
     */
    public function markSubmit($request)
    {
        DB::beginTransaction();
        try {
            $totalMark = 0;

            foreach ($request->answer_ids as $key => $answerId) {
                $child = AnswerChildren::find($answerId);
                if ($child) {
                    $child->evaluation_mark = array_key_exists($key, $request->marks)
                        ? (int) $request->marks[$key][0]
                        : 0;
                    $child->save();
                    $totalMark += $child->evaluation_mark;
                }
            }

            // Persist total on the Answer record
            $answer = Answer::where('homework_id', $request->homework_id)
                ->where('student_id', $request->student_id)
                ->firstOrFail();
            $answer->result = $totalMark;
            $answer->save();

            // Mirror onto homework_students so the index/evaluation view reflects the score
            $homeworkStudent = HomeworkStudent::where('homework_id', $request->homework_id)
                ->where('student_id', $request->student_id)
                ->first();

            if ($homeworkStudent) {
                $homeworkStudent->marks = $totalMark;
                $homeworkStudent->save();
                $this->awardE6Points((int) $request->student_id, $totalMark);
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.submitted_successfully'), []);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Homework Mark Submit Error: ' . $th->getMessage());
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * BRAINOVA E6 POINTS HOOK — DO NOT DELETE
     * ─────────────────────────────────────────
     * Awards E6 Points to a student when marks are saved.
     * Multiplier is set in config/brainova.php (default: 1 mark = 10 points).
     * To change the ratio, update BRAINOVA_E6_MULTIPLIER in .env — no code changes needed.
     */
    private function awardE6Points(int $studentId, int $marks): void
    {
        $points = $marks * config('brainova.e6_points_per_mark', 10);

        if ($points > 0) {
            DB::table('students')
                ->where('id', $studentId)
                ->increment('total_score', $points);
        }
    }

    /**
     * Parses a CSV file and inserts rows into homework_quiz_questions.
     *
     * Expected CSV column order:
     *   question | option_a | option_b | option_c | option_d | correct_answer | hint | explanation
     *
     * IMPORTANT: This table is completely separate from the online-examination
     * question_banks table. Homework quizzes must NEVER write to question_banks.
     */
    private function processQuizCsv($file, int $homeworkId): void
    {
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Skip the header row

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Skip rows that are too short or have a blank question
            if (count($row) < 6 || empty(trim($row[0]))) {
                continue;
            }

            DB::table('homework_quiz_questions')->insert([
                'homework_id'    => $homeworkId,
                'question'       => trim($row[0]),
                'option_a'       => trim($row[1] ?? ''),
                'option_b'       => trim($row[2] ?? ''),
                'option_c'       => trim($row[3] ?? ''),
                'option_d'       => trim($row[4] ?? ''),
                'correct_answer' => trim($row[5] ?? ''),
                'hint'           => isset($row[6]) && $row[6] !== '' ? trim($row[6]) : null,
                'explanation'    => isset($row[7]) && $row[7] !== '' ? trim($row[7]) : null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        fclose($handle);
    }
}
