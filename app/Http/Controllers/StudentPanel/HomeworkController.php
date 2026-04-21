<?php

namespace App\Http\Controllers\StudentPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentInfo\Student;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OnlineExamination\Answer;
use App\Models\OnlineExamination\AnswerChildren;
use App\Http\Requests\StudentPanel\HomeworkSubmit;
use App\Repositories\StudentPanel\Homework\HomeworkInterface;

class HomeworkController extends Controller
{
    private $repo;

    function __construct(HomeworkInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $data['homeworks'] = $this->repo->index();
        $data['title']     = 'homework';

        return view('student-panel.homeworks', compact('data'));
    }

    public function submit(HomeworkSubmit $request)
    {
        $result = $this->repo->submit($request);
        return response()->json($result);
    }



    public function homeworkAnswerSubmit(HomeworkSubmit $request)
    {
        try {
            $this->repo->submit($request);
            $this->answerSubmit($request);
            return redirect()->route('student-panel-homeworks.index')->with('success', 'Homework submitted successfully');

        } catch (\Throwable $th) {
            // Safely log the error behind the scenes instead of crashing the screen
            \Log::error('Homework Submit Error: ' . $th->getMessage());
            return redirect()->route('student-panel-homeworks.index')->with('error', 'Something went wrong. Please try again.');
        }
    }


     public function answerSubmit($request){

        DB::beginTransaction();
        try {
            $student = Student::where('user_id', Auth::user()->id)->first();
            $totalMarks = 0;
            
            
            
            // Fetch the specific question to get its actual mark value
            $questionData = DB::table('question_banks')->where('id', $questionId)->first();
            $marksPerQuestion = $questionData->marks ?? 0;

            $row                 = new Answer();
            $row->homework_id    = $request->homework_id;
            $row->student_id     = $student->id;
            $row->save();

            foreach ($request->answer as $questionId => $value) {
                if($value != ""){
                    // Fetch correct answer from DB
                    $qBank = DB::table('question_banks')->where('id', $questionId)->first();
                    
                    $scoreForThis = 0;
                    $isCorrect = (strtolower(trim($value)) == strtolower(trim($qBank->correct_answer)));

                    if($isCorrect) {
                        $scoreForThis = $marksPerQuestion;
                        // Check if hint was used for THIS specific question
                        if(isset($request->hint_used[$questionId]) && $request->hint_used[$questionId] == 1) {
                            $scoreForThis = $scoreForThis / 2; // 50% Penalty
                        }
                    }

                    $totalMarks += $scoreForThis;

                    $child                   = new AnswerChildren();
                    $child->answer_id        = $row->id;
                    $child->question_bank_id = $questionId;
                    $child->answer           = $value;
                    $child->evaluation_mark  = $scoreForThis; // Saving individual score
                    $child->save();
                }
            }

            // Update the main answer record with total marks
            $row->marks = $totalMarks; 
            $row->save();

            // ==========================================
            // BRAINOVA E6 SCORE INCREMENT
            // ==========================================
            $e6Points = $totalMarks * 10;
            $student->increment('total_score', $e6Points);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th; // Useful for debugging
        }
    }
public function downloadSample()
{
    // We use the absolute path found in your log file
    $filePath = '/home/fjojsvvy/public_html/public/Quiz_Template.csv';

    if (file_exists($filePath)) {
        return response()->download($filePath, 'Quiz_Template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    // If it still fails, this will show you exactly what path it tried to use
    return "Error: File not found at " . $filePath;
}


    public function homeworkAnswer($id)
    {
        $data = $this->repo->show($id);
        return view('student-panel.homework-question-view', compact('data'));
    }
    public function takeQuiz($id)
    {
        // 1. Fetch the main homework details
        $homework = DB::table('homework')->where('id', $id)->first();
        
        // 2. Fetch the questions and SHUFFLE them randomly to prevent cheating!
        $questions = DB::table('homework_quiz_questions')
                        ->where('homework_id', $id)
                        ->inRandomOrder() 
                        ->get();
                        
        $data['title'] = $homework->title ?? 'Take Quiz';
        $data['homework'] = $homework;
        $data['questions'] = $questions;
        
        // 3. Return the new interactive quiz view (we will build this next)
        return view('student-panel.take-quiz', compact('data'));
    }
public function submitInteractiveQuiz(Request $request)
    {
        DB::beginTransaction();
        try {
            $student = \App\Models\StudentInfo\Student::where('user_id', Auth::user()->id)->first();
            $homeworkId = $request->homework_id;
            $score = $request->score;

            // 1. Create the base Answer record
            $row                 = new \App\Models\OnlineExamination\Answer();
            $row->homework_id    = $homeworkId;
            $row->student_id     = $student->id;
            $row->save();

            // 2. Fetch a valid Upload ID to satisfy the strict foreign key!
            // We just grab the very first upload in your system to act as a placeholder.
            $placeholderUpload = DB::table('uploads')->first();
            $uploadId = $placeholderUpload ? $placeholderUpload->id : 1; 

            // 3. Mark the homework as fully submitted in the LMS system 
            DB::table('homework_students')->updateOrInsert(
                ['homework_id' => $homeworkId, 'student_id' => $student->id],
                [
                    'homework'   => $uploadId, // <-- THE FIX: Hands the database a real file ID!
                    'marks'      => $score, 
                    'created_at' => now(), 
                    'updated_at' => now()
                ]
            );

            // 4. BRAINOVA E6 SCORE INCREMENT 
            $e6Points = $score * 10;
            $student->increment('total_score', $e6Points);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Quiz Saved!']);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Quiz AJAX Submit Error: ' . $th->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $th->getMessage() . ' (Line: ' . $th->getLine() . ')'
            ], 500);
        }
    }
    
}
