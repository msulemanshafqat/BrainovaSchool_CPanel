<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\StudentInfo\Student;
use App\Http\Controllers\Controller;
use App\Models\OnlineExamination\Answer;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\OnlineExamination\AnswerChildren;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\SectionRepository;
use App\Repositories\Academic\SubjectRepository;
use App\Repositories\Homework\HomeworkRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\Academic\SubjectAssignRepository;
use App\Interfaces\OnlineExamination\QuestionGroupInterface;
use App\Repositories\OnlineExamination\OnlineExamRepository;
use App\Repositories\OnlineExamination\QuestionBankRepository;
use App\Repositories\OnlineExamination\QuestionGroupRepository;
use App\Http\Requests\Examination\Homework\HomeworkStoreRequest;
use App\Http\Requests\Examination\Homework\HomeworkUpdateRequest;
use App\Models\HomeworkStudent;



use App\Models\Homework;
// use App\Models\QuestionBank; // Recommended if model exists

class HomeworkController extends Controller
{
    private $repo;
    private $classRepo;
    private $classSetupRepo;
    private $sectionRepo;
    private $subjectRepo;
    private $assignSubjectRepo;
    private $quesgrouprepo;
    private $questionRepo;
    private $onlineExamRepo;


    function __construct(
        HomeworkRepository $repo,

        ClassSetupRepository $classSetupRepo,
        ClassesRepository $classRepo,
        SectionRepository $sectionRepo,
        SubjectRepository $subjectRepo,
        SubjectAssignRepository $assignSubjectRepo,
        QuestionGroupRepository $quesgrouprepo,
        QuestionBankRepository $questionRepo,
        OnlineExamRepository $onlineExamRepo,

    ) {
        $this->repo               = $repo;
        $this->classRepo          = $classRepo;
        $this->classSetupRepo     = $classSetupRepo;
        $this->sectionRepo        = $sectionRepo;
        $this->subjectRepo        = $subjectRepo;
        $this->assignSubjectRepo        = $assignSubjectRepo;
        $this->quesgrouprepo        = $quesgrouprepo;
        $this->questionRepo        = $questionRepo;
        $this->onlineExamRepo        = $onlineExamRepo;
    }

   // Example Controller Fix
public function index()
    {
        $data['title']              = ___('examination.homework');
        $data['classes']            = $this->classRepo->assignedAll();
        $data['homeworks']    = $this->repo->getPaginateAll();
        return view('backend.homework.index', compact('data'));
    }



    public function questionGroup()
    {
        $data['title']              = ___('examination.question_group');
        $data['question_groups']    =  $this->quesgrouprepo->getHomeworkGroup();
        return view('backend.homework.question_group', compact('data'));
    }

    public function questionList()
    {
        $data['title']              = ___('examination.question_list');
        $data['question_bank'] = $this->questionRepo->getHomeworkQues();
        return view('backend.homework.question_list', compact('data'));
    }

    public function questionCreate()
    {
        $data['title']              = ___('examination.question_create');
        $data['question_groups']    =  $this->quesgrouprepo->getHomeworkGroup();
        return view('backend.homework.question_create', compact('data'));
    }


    
    public function viewAnswerSheet($id, $student_id)
    {
        $data['homework']        = $this->repo->show($id);
        $data['answer']          = Answer::where('homework_id', $id)->where('student_id', $student_id)->first();
        $data['student']         = Student::find($student_id);
        return view('backend.homework.answerSheet', compact('data'));
    }

    public function answer($id, $student_id)
    {
        $data['exam']    = $this->repo->show($id);
        $data['answer']  = $this->repo->answer($id, $student_id);
        $data['title']   = ___('online-examination.Exam Answer');
        return view('backend.online-examination.online-exam.answer', compact('data', 'student_id'));
    }

    public function search(Request $request)
    {
        // 1. Fix the title
        $data['title']     = ___('examination.homework');
        
        // 2. Fix the array key so the dropdown can populate
        $data['classes']   = $this->classRepo->assignedAll(); 
        
        // 3. Fetch the filtered homework data (adjust the repo method if yours is named differently)
        // Note: If your repository doesn't have a specific search method, 
        // you might need to use $this->repo->getPaginateAll() temporarily just to stop the crash.
        $data['homeworks'] = $this->repo->search($request); 

        return view('backend.homework.index', compact('data'));
    }


    public function show(Request $request)
    {
        $data['homework']        = $this->repo->show($request->id);

        $request = new Request([
            'class'     => $data['homework']->classes_id,
            'section'   => $data['homework']->section_id,
            'exam_type' => $data['homework']->exam_type_id,
            'subject'   => $data['homework']->subject_id
        ]);

        return view('backend.homework.view', compact('data'));
    }

    public function create()
    {
        $data['classes']                = $this->classSetupRepo->all();
        $data['title']                  = ___('examination.homework');
        $data['question_groups']    =  $this->quesgrouprepo->getHomeworkGroup();
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
        $data['homework']              = $this->repo->show($id);
        $data['classes']               = $this->classSetupRepo->all();
        $data['sections']              = $this->classSetupRepo->getSections($data['homework']->classes_id);

        $request->merge([
            'classes_id' => $data['homework']->classes_id,
            'section_id' => $data['homework']->section_id
        ]);

        $data['subjects']              = $this->assignSubjectRepo->getSubjects($request);
        
        // ADDED THIS LINE: Fetch question groups so the dropdown works on Edit page
        $data['question_groups']       = $this->quesgrouprepo->getHomeworkGroup(); 
        
        $data['title']                 = ___('examination.homework');
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
        if ($result['status']):
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        else:
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        endif;
    }

    public function students(Request $request)
    {
        $data['homework']   = $this->repo->show($request->homework_id);
        $data['students']   = SessionClassStudent::with(['homeworkStudent' => function ($query) use ($data) {
            $query->where('homework_id', $data['homework']->id);
        }])
            ->where('session_id', setting('session'))
            ->where('classes_id', $data['homework']->classes_id)
            ->where('section_id', $data['homework']->section_id)
            ->get();

        $data['view'] = view('backend.homework.evaluation', compact('data'))->render();
        return response()->json($data);
    }

    public function evaluationSubmit(Request $request)
    {
        $result = $this->repo->evaluationSubmit($request);
        if ($result['status']) {
            return redirect()->route('homework.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function viewQuestions(Request $request)
    {

        $data = $this->repo->show($request->id);

        return view('backend.online-examination.online-exam.view_questions', compact('data'));
    }

// ==========================================
// NAZ
// ==========================================

public function markSubmit(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $totalMark = 0;
                foreach ($request->answer_ids as $key => $value) {
                    $row                = AnswerChildren::find($value);
                    if ($row) {
                        $row->evaluation_mark = array_key_exists($key, $request->marks) ? (int) $request->marks[$key][0] : 0;
                        $row->save();
                        $totalMark      += $row->evaluation_mark;
                    }
                }

                $row         = Answer::where('homework_id', $request->homework_id)->where('student_id', $request->student_id)->first();
                $row->result = $totalMark;
                $row->save();

                $homeworkStudent = HomeworkStudent::where('homework_id', $request->homework_id)->where('student_id', $request->student_id)->first();
                if ($homeworkStudent) {
                    $homeworkStudent->marks =  $row->result;
                    $homeworkStudent->save();

                    // ==========================================
                    // BRAINOVA E6 POINTS HOOK - DO NOT DELETE
                    // ==========================================
                    // Currently set so 1 Mark = 10 E6 Points
                    $e6PointsEarned = $row->result * 10; 

                    \Illuminate\Support\Facades\DB::table('students')
                        ->where('id', $request->student_id)
                        ->increment('total_score', $e6PointsEarned);
                    // ==========================================
                }
            });

            return redirect()->route('homework.index')->with('success', ___('alert.Evaluation successfully'));

        } catch (\Throwable $th) {
            return redirect()->route('homework.index')->with('danger', ___('alert.something_went_wrong_please_try_again'));
        }
    }
    public function getAllQuestions(Request $request)
{
    // 1. Fetch the questions from the database
    $questions = DB::table('question_banks')
                ->where('question_group_id', $request->id)
                ->get();

    // 2. Build the HTML table rows
    $html = "";
    foreach($questions as $q) {
        $html .= "<tr>
                    <td>
                        <input type='checkbox' name='questions_ids[]' value='".$q->id."' data-mark='".$q->marks."'>
                    </td>
                    <td>".$q->question."</td>
                    <td>".$q->marks."</td>
                  </tr>";
    }

    // 3. Send the HTML back to your AJAX call
    return $html;
}



public function storeExternalQuiz(Request $request)
{
    // 1. Security Check
    if ($request->header('X-Brainova-Token') !== 'Bnova_Secure_789') {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // 2. Validation (Prevents 500 errors on missing data)
    $request->validate([
        'class_id' => 'required',
        'questions' => 'required|array',
        'questions.*.question_text' => 'required',
        'questions.*.correct_answer' => 'required',
    ]);

    try {
        return DB::transaction(function () use ($request) {
            // 3. Create Master Quiz
            $homework = Homework::create([
                'classes_id'      => $request->class_id,
                'section_id'      => $request->section_id,
                'subject_id'      => $request->subject_id,
                'task_type'       => 'quiz',
                'marks'           => $request->total_marks,
                'date'            => now()->format('Y-m-d'),
                'submission_date' => $request->due_date,
                'status'          => 1,
            ]);

            // 4. Process Questions
            foreach ($request->questions as $q) {
                $questionId = DB::table('question_banks')->insertGetId([
                    'question'       => $q['question_text'],
                    'option_a'       => $q['option_a'] ?? null,
                    'option_b'       => $q['option_b'] ?? null,
                    'option_c'       => $q['option_c'] ?? null,
                    'option_d'       => $q['option_d'] ?? null,
                    'correct_answer' => $q['correct_answer'],
                    'hint_text'      => $q['hint'] ?? null,
                    'explanation'    => $q['explanation'] ?? null,
                    'timer_seconds'  => $q['timer'] ?? 0,
                    'created_at'     => now(),
                    'updated_at'     => now()
                ]);

                DB::table('homework_questions')->insert([
                    'homework_id'      => $homework->id,
                    'question_bank_id' => $questionId,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            return response()->json([
                'status' => 'success', 
                'message' => 'Quiz imported successfully',
                'homework_id' => $homework->id
            ]);
        });
    } catch (\Exception $e) {
        \Log::error("Quiz Import Failed: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'Database error occurred.'], 500);
    }
}
    
    public function getInventoryIds(Request $request)
    {
        // Simple Token Security
        if ($request->header('X-Brainova-Token') !== 'Bnova_Secure_789') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Fetch Real Database IDs and Names
        return response()->json([
            'classes'  => DB::table('classes')->select('id', 'name')->where('status', 1)->get(),
            'sections' => DB::table('sections')->select('id', 'name')->where('status', 1)->get(),
            'subjects' => DB::table('subjects')->select('id', 'name')->where('status', 1)->get(),
        ]);
    }
// ==========================================
// NAZ
// ==========================================

}
