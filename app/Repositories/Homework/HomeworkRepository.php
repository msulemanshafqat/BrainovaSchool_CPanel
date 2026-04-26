<?php

namespace App\Repositories\Homework;

use App\Models\Homework;
use App\Models\HomeworkQuestion;
use App\Models\HomeworkStudent;
use App\Models\OnlineExamination\Answer;
use App\Models\OnlineExamination\AnswerChildren;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Repositories\Homework\HomeworkInterface;

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
        $rows = $this->model::query()
            ->where('session_id', setting('session'));

        if (!empty($request->class)) {
            $rows = $rows->where('classes_id', $request->class);
        }
        if (!empty($request->section)) {
            $rows = $rows->where('section_id', $request->section);
        }
        if (!empty($request->subject)) {
            $rows = $rows->where('subject_id', $request->subject);
        }

        return $rows->paginate(10);
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
            $homework->topic           = $request->topic ?? null;
            $homework->title           = $request->title;
            $homework->task_type       = $request->task_type ?? 'homework';
            $homework->date            = $request->date;
            $homework->submission_date = $request->submission_date;
            $homework->marks           = $request->marks;
            $homework->status          = $request->status ?? 1;
            $homework->description     = $request->description;

            // Standard document upload
            if ($request->hasFile('document')) {
                $homework->document_id = $this->UploadImageCreate(
                    $request->document,
                    'backend/uploads/homeworks'
                );
            }

            // Save first to get an ID before CSV processing
            $homework->save();

            // Manual question selection (standard LMS flow)
            if ($request->questions_ids != null) {
                foreach ($request->questions_ids as $question_id) {
                    $assign                   = new HomeworkQuestion();
                    $assign->homework_id      = $homework->id;
                    $assign->question_bank_id = $question_id;
                    $assign->save();
                }
            }

            // Quiz CSV: parse BEFORE UploadImageCreate moves the temp file.
            // Questions go to homework_quiz_questions — NOT question_banks (exam table).
            if ($request->hasFile('bulk_questions_file')) {
                // Step 1: parse while temp file still at original path
                $this->processQuizCsv(
                    $request->file('bulk_questions_file'),
                    $homework->id
                );
                // Step 2: store the CSV file for teacher re-download
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
            $row                   = $this->model->find($id);
            $row->session_id       = setting('session');
            $row->classes_id       = $request->class;
            $row->section_id       = $request->section;
            $row->subject_id       = $request->subject;
            $row->topic            = $request->topic ?? $row->topic;
            $row->title            = $request->title ?? $row->title;
            $row->task_type        = $request->task_type ?? $row->task_type;
            $row->date             = $request->date;
            $row->submission_date  = $request->submission_date;
            $row->marks            = $request->marks;
            $row->status           = $request->status ?? $row->status;
            $row->description      = $request->description;

            if ($request->hasFile('document')) {
                $row->document_id = $this->UploadImageUpdate(
                    $request->document,
                    'backend/uploads/homeworks',
                    $row->document_id
                );
            }

            // Manual question selection
            if ($request->questions_ids != null) {
                HomeworkQuestion::where('homework_id', $row->id)->delete();
                foreach ($request->questions_ids as $question_id) {
                    $assign                   = new HomeworkQuestion();
                    $assign->homework_id      = $row->id;
                    $assign->question_bank_id = $question_id;
                    $assign->save();
                }
            }

            $row->save();

            // Replace quiz CSV — parse BEFORE UploadImageUpdate moves temp file
            if ($request->hasFile('bulk_questions_file')) {
                // Delete old quiz questions for this homework
                DB::table('homework_quiz_questions')
                    ->where('homework_id', $row->id)
                    ->delete();
                // Parse new CSV while temp file is still available
                $this->processQuizCsv(
                    $request->file('bulk_questions_file'),
                    $row->id
                );
                // Now move/store the file
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
            return $this->responseWithError(
                ___('alert.something_went_wrong_please_try_again'), []
            );
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->find($id);

            // Null-check before deleting upload — quiz submissions may have no file
            if ($row->document_id) {
                $this->UploadImageDelete($row->document_id);
            }

            // Clean up homework_quiz_questions (our custom table)
            DB::table('homework_quiz_questions')
                ->where('homework_id', $id)
                ->delete();

            // Clean up homework_questions pivot (standard LMS table)
            DB::table('homework_questions')
                ->where('homework_id', $id)
                ->delete();

            $row->delete();
            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(
                ___('alert.something_went_wrong_please_try_again'), []
            );
        }
    }

    // =========================================================================
    // EVALUATION
    // =========================================================================

    /**
     * Teacher manually assigns marks + optional feedback per student.
     * Awards Brainova E6 Points: 1 mark = 10 points (BRAINOVA HOOK — DO NOT DELETE).
     */
    /**
     * Teacher manually assigns marks + optional feedback per student.
     * Awards Brainova E6 Points: 1 mark = 10 points (BRAINOVA HOOK — DO NOT DELETE).
     *
     * Uses first() not firstOrFail() intentionally:
     * If a student hasn't submitted, firstOrFail() throws ModelNotFoundException
     * which aborts the entire DB transaction — all other marks entered are lost.
     * With first() + continue, unsubmitted students are skipped gracefully.
     */
    public function evaluationSubmit($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->students as $key => $student) {
                $record = HomeworkStudent::where('homework_id', $request->homework_id)
                    ->where('student_id', $student)
                    ->first();

                // Skip students who haven't submitted — don't abort the entire save
                if (!$record) continue;

                $currentMarks  = $request->marks[$key];
                $record->marks = $currentMarks;

                // Save feedback if provided (Tier 2 Feature F)
                if (isset($request->feedback[$key]) && trim($request->feedback[$key]) !== '') {
                    $record->feedback = trim($request->feedback[$key]);
                }

                $record->save();

                // ==========================================
                // BRAINOVA E6 POINTS HOOK — DO NOT DELETE
                // 1 Mark = 10 E6 Points multiplier
                // ==========================================
                $e6PointsEarned = (int) $currentMarks * 10;
                if ($e6PointsEarned > 0) {
                    DB::table('students')
                        ->where('id', $student)
                        ->increment('total_score', $e6PointsEarned);
                }
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.submitted_successfully'), []);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(
                ___('alert.something_went_wrong_please_try_again'), []
            );
        }
    }

    /**
     * Answer-sheet marking: teacher grades uploaded file submissions.
     */
    public function markSubmit($request)
    {
        DB::beginTransaction();
        try {
            $totalMark = 0;

            foreach ($request->answer_ids as $key => $value) {
                $row = AnswerChildren::find($value);
                if ($row) {
                    $row->evaluation_mark = array_key_exists($key, $request->marks)
                        ? (int) $request->marks[$key][0]
                        : 0;
                    $row->save();
                    $totalMark += $row->evaluation_mark;
                }
            }

            $answer = Answer::where('homework_id', $request->homework_id)
                ->where('student_id', $request->student_id)
                ->first();

            if ($answer) {
                $answer->result = $totalMark;
                $answer->save();
            }

            $homeworkStudent = HomeworkStudent::where('homework_id', $request->homework_id)
                ->where('student_id', $request->student_id)
                ->first();

            if ($homeworkStudent) {
                $homeworkStudent->marks = $totalMark;
                $homeworkStudent->save();

                // BRAINOVA E6 POINTS HOOK — DO NOT DELETE
                $e6PointsEarned = (int) $totalMark * 10;
                if ($e6PointsEarned > 0) {
                    DB::table('students')
                        ->where('id', $request->student_id)
                        ->increment('total_score', $e6PointsEarned);
                }
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.submitted_successfully'), []);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Homework Mark Submit Error: ' . $th->getMessage());
            return $this->responseWithError(
                ___('alert.something_went_wrong_please_try_again'), []
            );
        }
    }

    // =========================================================================
    // STATS (Teacher dashboard charts)
    // =========================================================================

    public function getStats(): array
    {
        $subjectIds = teacherSubjects();
        $sessionId  = setting('session');

        $hwIds = $this->model::where('session_id', $sessionId)
            ->whereIn('subject_id', $subjectIds)
            ->pluck('id');

        $byType = $this->model::where('session_id', $sessionId)
            ->whereIn('subject_id', $subjectIds)
            ->selectRaw('task_type, COUNT(*) as cnt')
            ->groupBy('task_type')
            ->pluck('cnt', 'task_type')
            ->toArray();

        $submitted   = HomeworkStudent::whereIn('homework_id', $hwIds)->count();
        $pendingEval = HomeworkStudent::whereIn('homework_id', $hwIds)
            ->whereNull('marks')
            ->count();

        return [
            'total'        => $hwIds->count(),
            'quizzes'      => $byType['quiz'] ?? 0,
            'submitted'    => $submitted,
            'pending_eval' => $pendingEval,
            'by_type'      => $byType,
        ];
    }

    // =========================================================================
    // ADMIN OVERVIEW
    // =========================================================================

    /**
     * Admin-only: returns ALL homework across ALL teachers/subjects.
     * Accepts optional GET filters: class, section, subject, task_type.
     */
    public function getAllForAdmin($request = null)
    {
        $query = $this->model::with(['class', 'section', 'subject', 'upload'])
            ->orderByDesc('id');

        if ($request) {
            if (!empty($request->class)) {
                $query->where('classes_id', $request->class);
            }
            if (!empty($request->section)) {
                $query->where('section_id', $request->section);
            }
            if (!empty($request->subject)) {
                $query->where('subject_id', $request->subject);
            }
            if (!empty($request->task_type)) {
                $query->where('task_type', $request->task_type);
            }
        }

        return $query->paginate(20)->withQueryString();
    }

    // =========================================================================
    // TIER 2 — QUIZ ANALYTICS
    // =========================================================================

    /**
     * Per-question analytics for a quiz homework.
     * Reads from homework_quiz_answers — NOT online-exam tables.
     */
    public function getQuizAnalytics(int $homeworkId): array
    {
        $questions = DB::table('homework_quiz_questions')
            ->where('homework_id', $homeworkId)
            ->orderBy('id')
            ->get();

        $allAnswers    = DB::table('homework_quiz_answers')
            ->where('homework_id', $homeworkId)
            ->get()
            ->groupBy('question_id');

        $totalStudents = HomeworkStudent::where('homework_id', $homeworkId)->count();

        $result = [];
        foreach ($questions as $q) {
            $qAnswers     = $allAnswers->get($q->id, collect());
            $attempts     = $qAnswers->count();
            $correctCount = $qAnswers->where('is_correct', 1)->count();
            $accuracyPct  = $attempts > 0
                ? round(($correctCount / $attempts) * 100) : 0;

            $result[] = [
                'id'             => $q->id,
                'question'       => $q->question,
                'option_a'       => $q->option_a,
                'option_b'       => $q->option_b,
                'option_c'       => $q->option_c,
                'option_d'       => $q->option_d,
                'correct_answer' => $q->correct_answer,
                'hint'           => $q->hint,
                'total_attempts' => $attempts,
                'correct_count'  => $correctCount,
                'accuracy_pct'   => $accuracyPct,
                'option_counts'  => [
                    'a'       => $qAnswers->where('selected_answer', $q->option_a)->count(),
                    'b'       => $qAnswers->where('selected_answer', $q->option_b)->count(),
                    'c'       => $qAnswers->where('selected_answer', $q->option_c)->count(),
                    'd'       => $qAnswers->where('selected_answer', $q->option_d)->count(),
                    'skipped' => $totalStudents - $attempts,
                ],
            ];
        }

        return [
            'questions'      => $result,
            'total_students' => $totalStudents,
            'homework'       => DB::table('homework')->where('id', $homeworkId)->first(),
        ];
    }

    // =========================================================================
    // TIER 2 — CSV EXPORT
    // =========================================================================

    public function exportResultsCsv(int $homeworkId)
    {
        $homework = $this->model->findOrFail($homeworkId);

        $students = DB::table('session_class_students as scs')
            ->join('students as s', 's.id', '=', 'scs.student_id')
            ->leftJoin('homework_students as hs', function ($join) use ($homeworkId) {
                $join->on('hs.student_id', '=', 'scs.student_id')
                     ->where('hs.homework_id', $homeworkId);
            })
            ->where('scs.session_id', setting('session'))
            ->where('scs.classes_id', $homework->classes_id)
            ->where('scs.section_id', $homework->section_id)
            ->select([
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                's.admission_no',
                'hs.date        as submission_date',
                'hs.marks',
                'hs.feedback',
            ])
            ->orderBy('s.first_name')
            ->get();

        $filename = 'results_'
            . str_replace([' ', '/'], '_', $homework->title ?? $homeworkId)
            . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($students, $homework) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Student Name', 'Admission No', 'Submission Date',
                'Marks Earned', 'Total Marks', 'Feedback',
            ]);
            foreach ($students as $row) {
                fputcsv($handle, [
                    $row->student_name,
                    $row->admission_no   ?? '—',
                    $row->submission_date ?? 'Not submitted',
                    $row->marks          ?? '—',
                    $homework->marks     ?? '—',
                    $row->feedback       ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Parses a quiz CSV and inserts rows into homework_quiz_questions.
     *
     * CSV column order:
     *   question | option_a | option_b | option_c | option_d | correct_answer | hint | explanation
     *
     * IMPORTANT: Writes ONLY to homework_quiz_questions.
     * Must NEVER write to question_banks (online-exam table).
     *
     * Must be called BEFORE UploadImageCreate/Update moves the temp file.
     */
    private function processQuizCsv($file, int $homeworkId): void
    {
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // skip header

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 6 || empty(trim($row[0]))) {
                continue; // skip malformed/empty rows
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
