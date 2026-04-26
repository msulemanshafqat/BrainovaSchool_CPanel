<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BrainovaE6ScoreController extends Controller
{
    public function syncScore(Request $request)
    {
        // 1. Security Check
        $secretKey = "BrainovaBridge2026!";
        if ($request->secret_key !== $secretKey) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        // 2. Self-Healing: Ensure the 'total_score' column exists at the END
        if (!Schema::hasColumn('students', 'total_score')) {
            Schema::table('students', function (Blueprint $table) {
                // Removing ->after('id') forces it to the end of the table
                $table->integer('total_score')->default(0); 
            });
        }

        // 3. Data Extraction
        $studentId = $request->student_id;
        $score     = $request->score;
        $quizId    = $request->quiz_id;

        // 4. Begin Transaction
        DB::beginTransaction();

        try {
            // Action A: Record Quiz
            DB::table('homework_students')->insert([
                'student_id'  => $studentId,
                'homework_id' => $quizId, 
                'homework'    => $quizId, 
                'date'        => now()->format('Y-m-d'),
                'marks'       => $score,
                'branch_id'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Action B: Increment Universal E⁶ Points
            DB::table('students')
                ->where('id', $studentId)
                ->increment('total_score', $score);

            DB::commit();

            return response()->json([
                'status' => true, 
                'message' => 'Brainova: Column fixed and Score synced!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false, 
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getLeaderboard(Request $request)
    {
        // 1. Security Check
        $secretKey = "BrainovaBridge2026!";
        if ($request->secret_key !== $secretKey) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $studentId = $request->student_id;

        // 2. Fetch Top 10 Students
        $topStudents = DB::table('students')
            ->select('id', 'first_name', 'last_name', 'total_score')
            ->orderBy('total_score', 'desc')
            ->limit(10)
            ->get();

        // 3. Get Current Student's Rank & Score
        $currentStudent = DB::table('students')
            ->where('id', $studentId)
            ->select('total_score')
            ->first();

        // Calculate rank (How many students have more points than me + 1)
        $myRank = DB::table('students')
            ->where('total_score', '>', $currentStudent->total_score ?? 0)
            ->count() + 1;

        return response()->json([
            'status' => true,
            'leaderboard' => $topStudents,
            'my_score' => $currentStudent->total_score ?? 0,
            'my_rank' => $myRank
        ]);
    }
}