<?php

namespace App\Http\Controllers\StudentPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\HomeworkStudent;
use App\Http\Requests\StudentPanel\HomeworkSubmit;
use App\Repositories\StudentPanel\Homework\HomeworkInterface;

class HomeworkController extends Controller
{
    private $repo;

    public function __construct(HomeworkInterface $repo)
    {
        $this->repo = $repo;
    }

    // =========================================================================
    // HOMEWORK LIST
    // =========================================================================

    public function index()
    {
        $data['homeworks'] = $this->repo->index();
        $data['title']     = ___('examination.homework');

        return view('student-panel.homeworks', compact('data'));
    }

    // =========================================================================
    // STANDARD HOMEWORK SUBMISSION (file upload)
    // =========================================================================

    public function submit(HomeworkSubmit $request)
    {
        $result = $this->repo->submit($request);
        return response()->json($result);
    }

    public function homeworkAnswer($id)
    {
        $data = $this->repo->show($id);
        return view('student-panel.homework-question-view', compact('data'));
    }

    public function homeworkAnswerSubmit(HomeworkSubmit $request)
    {
        try {
            $this->repo->submit($request);
            return redirect()->route('student-panel-homeworks.index')
                ->with('success', 'Homework submitted successfully');
        } catch (\Throwable $th) {
            \Log::error('Homework Submit Error: ' . $th->getMessage());
            return redirect()->route('student-panel-homeworks.index')
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    // =========================================================================
    // QUIZ — TAKE OR REVIEW
    // =========================================================================

    /**
     * Load the quiz for a student.
     *
     * First-time visit  → interactive quiz (shuffle questions, start timer).
     * Already submitted → read-only review mode showing correct/incorrect answers.
     *
     * Students can NEVER reattempt a submitted quiz.
     */
    public function takeQuiz($id)
    {
        $student  = Auth::user()->student;
        $homework = DB::table('homework')->where('id', $id)->first();

        if (!$homework) {
            return redirect()->route('student-panel-homeworks.index')
                ->with('error', 'Quiz not found.');
        }

        // Check if already submitted
        $submission = HomeworkStudent::where('student_id', $student->id)
            ->where('homework_id', $id)
            ->first();

        // Load questions — shuffled for live quiz, ordered for review
        if ($submission) {
            $questions = DB::table('homework_quiz_questions')
                ->where('homework_id', $id)
                ->orderBy('id')
                ->get();
        } else {
            $questions = DB::table('homework_quiz_questions')
                ->where('homework_id', $id)
                ->inRandomOrder()
                ->get();
        }

        $data['title']      = $homework->title ?? 'Quiz';
        $data['homework']   = $homework;
        $data['questions']  = $questions;
        $data['submission'] = $submission; // null = first attempt; object = already done
        $data['isReview']   = $submission !== null;

        return view('student-panel.take-quiz', compact('data'));
    }

    // =========================================================================
    // QUIZ SUBMISSION (AJAX)
    // =========================================================================

    /**
     * Receives quiz answers from the JS frontend, grades them server-side,
     * stores the result in homework_students, and awards Brainova E6 Points.
     *
     * Scoring: each correct answer earns (homework.marks / total_questions) marks.
     * Hint penalty: 50% deducted from that question's marks.
     * Total is rounded to 2 decimal places.
     */
    public function submitInteractiveQuiz(Request $request)
    {
        DB::beginTransaction();
        try {
            $student    = Auth::user()->student;
            $homeworkId = (int) $request->homework_id;

            // Guard: prevent reattempt
            $alreadySubmitted = HomeworkStudent::where('student_id', $student->id)
                ->where('homework_id', $homeworkId)
                ->exists();

            if ($alreadySubmitted) {
                return response()->json([
                    'status'  => 'already_submitted',
                    'message' => 'You have already submitted this quiz.',
                ]);
            }

            // Load homework to get total marks and question count
            $homework       = DB::table('homework')->where('id', $homeworkId)->first();
            $totalQuestions = DB::table('homework_quiz_questions')
                ->where('homework_id', $homeworkId)
                ->count();

            $marksPerQuestion = ($totalQuestions > 0 && $homework->marks > 0)
                ? (float) $homework->marks / $totalQuestions
                : 1;

            // Grade answers sent from JS
            // $request->answers = [ quiz_question_id => 'selected_option_text', ... ]
            // $request->hints   = [ quiz_question_id => true, ... ]
            $answers      = $request->answers ?? [];
            $hintsUsed    = $request->hints   ?? [];
            $earnedMarks  = 0;

            foreach ($answers as $questionId => $selectedAnswer) {
                $question = DB::table('homework_quiz_questions')
                    ->where('id', $questionId)
                    ->where('homework_id', $homeworkId)
                    ->first();

                if (!$question) continue;

                $isCorrect = strtolower(trim($selectedAnswer))
                          === strtolower(trim($question->correct_answer));

                if ($isCorrect) {
                    $points = $marksPerQuestion;
                    // 50% hint penalty
                    if (!empty($hintsUsed[$questionId])) {
                        $points *= 0.5;
                    }
                    $earnedMarks += $points;
                }
            }

            $earnedMarks = round($earnedMarks, 2);

            // Record submission — no file upload for quizzes, homework column is nullable
            HomeworkStudent::create([
                'student_id'  => $student->id,
                'homework_id' => $homeworkId,
                'homework'    => null,
                'marks'       => $earnedMarks,
                'date'        => now()->format('Y-m-d'),
            ]);

            // Brainova E6 Points Hook — 1 mark = config multiplier points
            $e6Points = (int) round($earnedMarks * config('brainova.e6_points_per_mark', 10));
            if ($e6Points > 0) {
                DB::table('students')
                    ->where('id', $student->id)
                    ->increment('total_score', $e6Points);
            }

            DB::commit();

            return response()->json([
                'status'      => 'success',
                'message'     => 'Quiz submitted!',
                'earned'      => $earnedMarks,
                'total'       => $homework->marks,
                'e6_points'   => $e6Points,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Quiz Submit Error: ' . $th->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    // =========================================================================
    // CSV TEMPLATE DOWNLOAD
    // =========================================================================

    public function downloadSample()
    {
        $path = public_path('Quiz_Template.csv');

        if (file_exists($path)) {
            return response()->download($path, 'Quiz_Template.csv', ['Content-Type' => 'text/csv']);
        }

        return redirect()->back()->with('error', 'Template file not found.');
    }
}
