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

    /**
     * Admin-only: returns ALL homework with no teacher/subject scope.
     * Accepts optional GET filters: class, section, subject, task_type.
     * Loads class, section, subject and upload relations for the overview table.
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
     * Teacher assigns marks and optional written feedback per student.
     * Automatically awards Brainova E6 Points on save.
     *
     * Expected request fields:
     *   homework_id     — int
     *   students[]      — array of student IDs (only submitted students)
     *   marks[]         — marks per student (parallel array to students[])
     *   feedback[]      — optional feedback text per student (parallel array)
     */
    public function evaluationSubmit($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->students as $key => $studentId) {
                $record = HomeworkStudent::where('homework_id', $request->homework_id)
                    ->where('student_id', $studentId)
                    ->firstOrFail();

                $record->marks = (int) $request->marks[$key];

                // Save feedback if the teacher provided it for this student
                if (isset($request->feedback[$key]) && trim($request->feedback[$key]) !== '') {
                    $record->feedback = trim($request->feedback[$key]);
                }

                $record->save();

                $this->awardE6Points((int) $studentId, $record->marks);
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
    // DASHBOARD STATS (for teacher portal charts)
    // =========================================================================

    /**
     * Returns aggregated statistics scoped to the current teacher's subjects and session.
     * Used by the teacher index view to power stats cards and Chart.js charts.
     */
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

        $submitted   = \App\Models\HomeworkStudent::whereIn('homework_id', $hwIds)->count();
        $pendingEval = \App\Models\HomeworkStudent::whereIn('homework_id', $hwIds)
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
    // TIER 2 — QUIZ ANALYTICS
    // =========================================================================

    /**
     * Returns per-question analytics for a quiz homework.
     *
     * For each question we calculate:
     *   - total_attempts  : number of students who answered
     *   - correct_count   : number of correct answers
     *   - accuracy_pct    : percentage correct (0–100)
     *   - option_counts   : how many students chose each option (a–d + null/skipped)
     *
     * Data source: homework_quiz_answers (populated when students submit the quiz).
     * Completely separate from online-exam question_banks / answers tables.
     */
    public function getQuizAnalytics(int $homeworkId): array
    {
        // Load questions for this quiz
        $questions = DB::table('homework_quiz_questions')
            ->where('homework_id', $homeworkId)
            ->orderBy('id')
            ->get();

        // Load all individual student answers for this quiz in one query
        $allAnswers = DB::table('homework_quiz_answers')
            ->where('homework_id', $homeworkId)
            ->get()
            ->groupBy('question_id');

        // Total unique students who submitted this quiz
        $totalStudents = DB::table('homework_students')
            ->where('homework_id', $homeworkId)
            ->count();

        $result = [];

        foreach ($questions as $q) {
            $qAnswers = $allAnswers->get($q->id, collect());

            $attempts      = $qAnswers->count();
            $correctCount  = $qAnswers->where('is_correct', 1)->count();
            $accuracyPct   = $attempts > 0 ? round(($correctCount / $attempts) * 100) : 0;

            // Count how many students chose each option
            $optionCounts = [
                'a'       => $qAnswers->where('selected_answer', $q->option_a)->count(),
                'b'       => $qAnswers->where('selected_answer', $q->option_b)->count(),
                'c'       => $qAnswers->where('selected_answer', $q->option_c)->count(),
                'd'       => $qAnswers->where('selected_answer', $q->option_d)->count(),
                'skipped' => $totalStudents - $attempts,
            ];

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
                'option_counts'  => $optionCounts,
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

    /**
     * Streams a CSV file of all student results for a given homework.
     *
     * Columns: Student Name | Admission No | Submission Date | Marks | Total Marks | Feedback
     *
     * Works for both quiz (auto-graded) and standard (teacher-graded) homework.
     * Returns a Laravel streaming download Response.
     */
    public function exportResultsCsv(int $homeworkId)
    {
        $homework = $this->model->findOrFail($homeworkId);

        // Fetch all students in the class/section for this homework
        $students = DB::table('session_class_students as scs')
            ->join('students as s',       's.id',  '=', 'scs.student_id')
            ->leftJoin('homework_students as hs', function ($join) use ($homeworkId) {
                $join->on('hs.student_id',  '=', 'scs.student_id')
                     ->where('hs.homework_id', $homeworkId);
            })
            ->where('scs.session_id',  setting('session'))
            ->where('scs.classes_id',  $homework->classes_id)
            ->where('scs.section_id',  $homework->section_id)
            ->select([
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                's.admission_no',
                'hs.date        as submission_date',
                'hs.marks',
                'hs.feedback',
            ])
            ->orderBy('s.first_name')
            ->get();

        // Build CSV content
        $filename = 'results_' . str_replace([' ', '/'], '_', $homework->title ?? $homeworkId) . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($students, $homework) {
            $handle = fopen('php://output', 'w');

            // CSV header row
            fputcsv($handle, [
                'Student Name',
                'Admission No',
                'Submission Date',
                'Marks Earned',
                'Total Marks',
                'Feedback',
            ]);

            foreach ($students as $row) {
                fputcsv($handle, [
                    $row->student_name,
                    $row->admission_no  ?? '—',
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
    // DASHBOARD REPORTING (for admin/teacher filtered dashboard)
    // =========================================================================

    /**
     * Returns global stats for all classes (admin-level view).
     * Fetches: Total Tasks, Submitted, Pending Eval, Cumulative Score (E6 Points conversion).
     */
    public function getGlobalStats(): array
    {
        $sessionId = setting('session');

        // Total homework tasks assigned
        $totalTasks = $this->model::where('session_id', $sessionId)->count();

        // Total submissions (students who submitted something)
        $totalSubmitted = DB::table('homework_students')
            ->whereIn('homework_id', $this->model::where('session_id', $sessionId)->pluck('id'))
            ->count();

        // Pending evaluations (submitted but no marks yet)
        $pendingEval = DB::table('homework_students')
            ->whereIn('homework_id', $this->model::where('session_id', $sessionId)->pluck('id'))
            ->whereNull('marks')
            ->count();

        // Cumulative score in E6 Points (sum of all student scores converted to points)
        $totalMarks = DB::table('homework_students')
            ->whereIn('homework_id', $this->model::where('session_id', $sessionId)->pluck('id'))
            ->sum('marks');
        $e6Points = $totalMarks * config('brainova.e6_points_per_mark', 10);

        return [
            'total_tasks_assigned' => $totalTasks,
            'total_submitted'      => $totalSubmitted,
            'pending_evaluations'  => $pendingEval,
            'cumulative_score_e6'  => $e6Points,
        ];
    }

    /**
     * Returns filtered homework report data for charts and table.
     * Respects teacher permissions (if not admin, only returns assigned classes/sections).
     * Returns: table_html, chart_data (donut + line), statistics.
     */
    public function getFilteredHomeworkReport($filters): array
    {
        $sessionId = setting('session');
        
        $query = $this->model::with(['class', 'section', 'subject', 'upload'])
            ->where('session_id', $sessionId);

        // Apply filters
        if (!empty($filters['class'])) {
            $query->where('classes_id', $filters['class']);
        }
        if (!empty($filters['section'])) {
            $query->where('section_id', $filters['section']);
        }
        if (!empty($filters['subject'])) {
            $query->where('subject_id', $filters['subject']);
        }
        if (!empty($filters['task_type']) && $filters['task_type'] !== 'all') {
            $query->where('task_type', $filters['task_type']);
        }

        // Apply teacher permission scope if not admin
        if (!auth()->user() || auth()->user()->role_id != 1) { // role_id 1 = Admin
            $query->whereIn('subject_id', teacherSubjects());
        }

        $homeworks = $query->orderByDesc('id')->get();

        // Build task status donut data
        $donutData = $this->getTaskStatisticsForFilters($homeworks);

        // Build score trend line data
        $trendData = $this->getScoreTrendForFilters($homeworks);

        // Build table rows HTML
        $tableHtml = view('backend.homework.partials.filtered-table', [
            'homeworks' => $homeworks,
        ])->render();

        return [
            'success'        => true,
            'table_html'     => $tableHtml,
            'donut_data'     => $donutData,
            'trend_data'     => $trendData,
            'total_records'  => $homeworks->count(),
        ];
    }

    /**
     * Helper: Calculates task status breakdown (Submitted vs Pending vs Overdue).
     * Used by the filtered report donut chart.
     */
    private function getTaskStatisticsForFilters($homeworks): array
    {
        $submitted = 0;
        $pending   = 0;
        $overdue   = 0;

        foreach ($homeworks as $hw) {
            // Count submissions for this homework
            $submissionCount = DB::table('homework_students')
                ->where('homework_id', $hw->id)
                ->count();

            // Count submissions without marks (pending eval)
            $withoutMarks = DB::table('homework_students')
                ->where('homework_id', $hw->id)
                ->whereNull('marks')
                ->count();

            if ($submissionCount > 0) {
                $submitted += $submissionCount;
            }

            if ($withoutMarks > 0) {
                $pending += $withoutMarks;
            }

            // Check if overdue
            if ($hw->submission_date && \Carbon\Carbon::parse($hw->submission_date)->isPast()) {
                $overdue += 1;
            }
        }

        return [
            'labels' => ['Submitted', 'Pending', 'Overdue'],
            'data'   => [$submitted, $pending, $overdue],
            'colors' => ['#10b981', '#f59e0b', '#dc2626'],
        ];
    }

    /**
     * Helper: Calculates score trend over time (average score per task).
     * Used by the filtered report line chart.
     */
    private function getScoreTrendForFilters($homeworks): array
    {
        $labels = [];
        $datasets = [];

        // Group by class for multiple lines (each class = one line)
        $groupedByClass = $homeworks->groupBy(fn($h) => $h->class->name ?? 'Unknown');

        $colors = ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4', '#ec4899', '#f97316'];
        $colorIndex = 0;

        foreach ($groupedByClass as $className => $classHomeworks) {
            $data = [];
            foreach ($classHomeworks as $hw) {
                $avgScore = DB::table('homework_students')
                    ->where('homework_id', $hw->id)
                    ->avg('marks') ?? 0;
                $data[] = round($avgScore, 2);
            }

            // Pad shorter arrays with null for chart alignment
            $maxLen = $groupedByClass->max(fn($g) => $g->count()) ?: 1;
            while (count($data) < $maxLen) {
                $data[] = null;
            }

            $datasets[] = [
                'label'      => $className,
                'data'       => $data,
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => 'rgba(' . implode(',', array_slice(sscanf($colors[$colorIndex % count($colors)], '#%02x%02x%02x'), 0, 3)) . ',0.1)',
                'borderWidth' => 2,
                'tension'    => 0.4,
            ];

            $colorIndex++;
        }

        // Build labels as task numbers (#1, #2, #3…)
        $maxLen = $groupedByClass->max(fn($g) => $g->count()) ?: 1;
        for ($i = 1; $i <= $maxLen; $i++) {
            $labels[] = '#' . $i;
        }

        return [
            'labels'   => $labels,
            'datasets' => $datasets,
        ];
    }

    /**
     * Returns sections for a given class (used by dependent dropdown).
     */
    /**
    *public function getSectionsByClass(int $classId): array
    *{
    *    $sections = DB::table('class_sections')
     *       ->where('classes_id', $classId)
     *       ->get(['id', 'name']);

     *   return $sections->toArray();
    *}
        */
 public function getSectionsByClass(int $classId): array
{
    $sections = DB::table('subject_assigns as sa')
        ->join('sections', 'sections.id', '=', 'sa.section_id')
        ->where('sa.classes_id', $classId)
        ->where('sa.session_id', setting('session'))
        ->where('sections.status', 1)
        ->distinct()
        ->get(['sections.id', 'sections.name']);

    return $sections->toArray();
}

    /**
     * Returns subjects for a given class/section combo (used by dependent dropdown).
     */
    /**
    *public function getSubjectsByClassSection(int $classId, int $sectionId): array
   * {
      *  $subjects = DB::table('subject_assigns')
       *     ->join('subjects', 'subjects.id', '=', 'subject_assigns.subject_id')
       *     ->where('subject_assigns.classes_id', $classId)
       *     ->where('subject_assigns.section_id', $sectionId)
      *      ->distinct()
       *     ->get(['subjects.id', 'subjects.name']);

      *  return $subjects->toArray();
   * }
 */
public function getSubjectsByClassSection(int $classId, int $sectionId): array
{
    $subjects = DB::table('subject_assigns as sa')
        ->join('subject_assign_childrens as sac', 'sac.subject_assign_id', '=', 'sa.id')
        ->join('subjects', 'subjects.id', '=', 'sac.subject_id')
        ->where('sa.classes_id', $classId)
        ->where('sa.section_id', $sectionId)
        ->where('sa.session_id', setting('session'))
        ->distinct()
        ->get(['subjects.id', 'subjects.name']);

    return $subjects->toArray();
}





    /**
     * Returns all unique task types used in this session.
     */
    public function getTaskTypes(): array
    {
        $types = DB::table('homework')
            ->where('session_id', setting('session'))
            ->distinct()
            ->pluck('task_type')
            ->filter()
            ->toArray();

        return $types;
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
