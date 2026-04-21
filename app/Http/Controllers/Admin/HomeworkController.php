<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentInfo\Student;
use App\Http\Controllers\Controller;
use App\Models\StudentInfo\SessionClassStudent;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\SectionRepository;
use App\Repositories\Academic\SubjectRepository;
use App\Repositories\Homework\HomeworkRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\Academic\SubjectAssignRepository;
use App\Repositories\OnlineExamination\QuestionGroupRepository;
use App\Http\Requests\Examination\Homework\HomeworkStoreRequest;
use App\Http\Requests\Examination\Homework\HomeworkUpdateRequest;
use App\Models\HomeworkStudent;
use App\Models\Homework;

class HomeworkController extends Controller
{
    private $repo;
    private $classRepo;
    private $classSetupRepo;
    private $sectionRepo;
    private $subjectRepo;
    private $assignSubjectRepo;
    private $quesgrouprepo;

    public function __construct(
        HomeworkRepository     $repo,
        ClassSetupRepository   $classSetupRepo,
        ClassesRepository      $classRepo,
        SectionRepository      $sectionRepo,
        SubjectRepository      $subjectRepo,
        SubjectAssignRepository $assignSubjectRepo,
        QuestionGroupRepository $quesgrouprepo
    ) {
        $this->repo              = $repo;
        $this->classRepo         = $classRepo;
        $this->classSetupRepo    = $classSetupRepo;
        $this->sectionRepo       = $sectionRepo;
        $this->subjectRepo       = $subjectRepo;
        $this->assignSubjectRepo = $assignSubjectRepo;
        $this->quesgrouprepo     = $quesgrouprepo;
    }

    // =========================================================================
    // QUESTION GROUP & QUESTION LIST
    // These views are read-only references for teachers.
    // Creating/editing groups is an admin-only action managed elsewhere.
    // =========================================================================

    public function questionGroup()
    {
        if (!hasPermission('homework_read')) {
            return redirect()->route('homework.index')
                ->with('danger', ___('alert.you_dont_have_permission'));
        }

        $data['title']           = ___('examination.question_group');
        $data['question_groups'] = $this->quesgrouprepo->getHomeworkGroup();

        return view('backend.homework.question_group', compact('data'));
    }

    public function questionList()
    {
        if (!hasPermission('homework_read')) {
            return redirect()->route('homework.index')
                ->with('danger', ___('alert.you_dont_have_permission'));
        }

        $data['title']         = ___('examination.question_list');
        $data['question_bank'] = $this->quesgrouprepo->getHomeworkGroup(); // groups only — question bank is exam-only

        return view('backend.homework.question_list', compact('data'));
    }

    public function questionCreate()
    {
        // Creating questions directly is disabled for teachers in this module.
        // Questions are created via CSV upload during quiz creation.
        return redirect()->route('homework.index')
            ->with('danger', 'Questions are created automatically when you upload a Quiz CSV. Direct question creation is not available here.');
    }

    public function index()
    {
        $data['title']    = ___('examination.homework');
        $data['classes']  = $this->classRepo->assignedAll();
        $data['homeworks'] = $this->repo->getPaginateAll();

        return view('backend.homework.index', compact('data'));
    }

    public function search(Request $request)
    {
        $data['title']    = ___('examination.homework');
        $data['classes']  = $this->classRepo->assignedAll();
        $data['homeworks'] = $this->repo->search($request);

        return view('backend.homework.index', compact('data'));
    }

    public function create()
    {
        $data['title']   = ___('examination.homework');
        $data['classes'] = $this->classSetupRepo->all();

        return view('backend.homework.create', compact('data'));
    }

    public function store(HomeworkStoreRequest $request)
    {
        $result = $this->repo->store($request);

        if ($result['status']) {
            return redirect()->route('homework.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    public function edit($id, Request $request)
    {
        $data['homework'] = $this->repo->show($id);
        $data['classes']  = $this->classSetupRepo->all();
        $data['sections'] = $this->classSetupRepo->getSections($data['homework']->classes_id);
        $data['title']    = ___('examination.homework');

        $request->merge([
            'classes_id' => $data['homework']->classes_id,
            'section_id' => $data['homework']->section_id,
        ]);

        $data['subjects'] = $this->assignSubjectRepo->getSubjects($request);

        return view('backend.homework.edit', compact('data'));
    }

    public function update(HomeworkUpdateRequest $request, $id)
    {
        $result = $this->repo->update($request, $id);

        if ($result['status']) {
            return redirect()->route('homework.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {
        $result = $this->repo->destroy($id);

        if ($result['status']) {
            return response()->json([
                $result['message'],
                'success',
                ___('alert.deleted'),
                ___('alert.OK'),
            ]);
        }

        return response()->json([
            $result['message'],
            'error',
            ___('alert.oops'),
        ]);
    }

    // =========================================================================
    // EVALUATION
    // =========================================================================

    /**
     * Opens the evaluation modal — loads the student list for a homework item.
     */
    public function students(Request $request)
    {
        $data['homework'] = $this->repo->show($request->homework_id);

        $data['students'] = SessionClassStudent::with(['homeworkStudent' => function ($query) use ($data) {
            $query->where('homework_id', $data['homework']->id);
        }])
            ->where('session_id', setting('session'))
            ->where('classes_id', $data['homework']->classes_id)
            ->where('section_id', $data['homework']->section_id)
            ->get();

        $data['view'] = view('backend.homework.evaluation', compact('data'))->render();

        return response()->json($data);
    }

    /**
     * Saves manually assigned marks for standard homework submissions.
     */
    public function evaluationSubmit(Request $request)
    {
        $result = $this->repo->evaluationSubmit($request);

        if ($result['status']) {
            return redirect()->route('homework.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    /**
     * Saves marks from the answer-sheet grading view.
     * All business logic is handled inside the Repository.
     */
    public function markSubmit(Request $request)
    {
        $result = $this->repo->markSubmit($request);

        if ($result['status']) {
            return redirect()->route('homework.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    // =========================================================================
    // SUPPLEMENTARY VIEWS
    // =========================================================================

    public function viewAnswerSheet($id, $student_id)
    {
        $data['homework'] = $this->repo->show($id);
        $data['student']  = Student::find($student_id);

        return view('backend.homework.answerSheet', compact('data'));
    }

    public function viewQuestions(Request $request)
    {
        $homework  = $this->repo->show($request->id);

        // Quiz questions live in homework_quiz_questions — entirely separate from
        // the online-examination question_banks table. Use the dedicated modal blade.
        if ($homework && $homework->task_type === 'quiz') {
            $questions = DB::table('homework_quiz_questions')
                ->where('homework_id', $homework->id)
                ->orderBy('id')
                ->get();

            return view('backend.homework.quiz_questions_modal', compact('homework', 'questions'));
        }

        // Standard homework: fall back to the existing online-exam view
        $data = $homework;
        return view('backend.online-examination.online-exam.view_questions', compact('data'));
    }

    /**
     * Serves the blank Quiz CSV template for teachers to download.
     * Place "Quiz_Template.csv" inside /public/ for this to work.
     */
    public function downloadSample()
    {
        $path = public_path('Quiz_Template.csv');

        return response()->download($path, 'Quiz_Template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    // =========================================================================
    // EXTERNAL API  (authenticated via X-Brainova-Token header)
    // =========================================================================

    /**
     * Allows an external Brainova service to push a quiz into the LMS via JSON.
     * Token is validated against config/brainova.php (set BRAINOVA_API_TOKEN in .env).
     * Quiz questions are written to homework_quiz_questions — NOT to question_banks.
     */
    public function storeExternalQuiz(Request $request)
    {
        if ($request->header('X-Brainova-Token') !== config('brainova.api_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'class_id'                    => 'required',
            'title'                       => 'required|string|max:500',
            'questions'                   => 'required|array',
            'questions.*.question_text'   => 'required',
            'questions.*.correct_answer'  => 'required',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $homework                  = new Homework();
                $homework->classes_id      = $request->class_id;
                $homework->section_id      = $request->section_id;
                $homework->subject_id      = $request->subject_id;
                $homework->title           = $request->title;
                $homework->topic           = $request->topic ?? $request->title;
                $homework->task_type       = 'quiz';
                $homework->marks           = $request->total_marks;
                $homework->date            = now()->format('Y-m-d');
                $homework->submission_date = $request->due_date;
                $homework->status          = 1;
                $homework->session_id      = setting('session');
                $homework->save();

                foreach ($request->questions as $q) {
                    DB::table('homework_quiz_questions')->insert([
                        'homework_id'    => $homework->id,
                        'question'       => $q['question_text'],
                        'option_a'       => $q['option_a'] ?? null,
                        'option_b'       => $q['option_b'] ?? null,
                        'option_c'       => $q['option_c'] ?? null,
                        'option_d'       => $q['option_d'] ?? null,
                        'correct_answer' => $q['correct_answer'],
                        'hint'           => $q['hint'] ?? null,
                        'explanation'    => $q['explanation'] ?? null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }

                return response()->json([
                    'status'      => 'success',
                    'message'     => 'Quiz imported successfully',
                    'homework_id' => $homework->id,
                ]);
            });

        } catch (\Exception $e) {
            \Log::error('External Quiz Import Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Database error occurred.'], 500);
        }
    }

    /**
     * Returns real DB IDs for classes, sections, and subjects.
     * Used by external Brainova tools to resolve names to IDs before pushing quizzes.
     */
    public function getInventoryIds(Request $request)
    {
        if ($request->header('X-Brainova-Token') !== config('brainova.api_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'classes'  => DB::table('classes')->select('id', 'name')->where('status', 1)->get(),
            'sections' => DB::table('sections')->select('id', 'name')->where('status', 1)->get(),
            'subjects' => DB::table('subjects')->select('id', 'name')->where('status', 1)->get(),
        ]);
    }
}
