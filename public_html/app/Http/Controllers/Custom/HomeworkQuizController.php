<?php
/* START: Daily Homework Quiz Feature - Production Mode */
namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeworkQuizController extends Controller
{
    public function store(Request $request)
    {
        // Validating the document and the new title field
        $request->validate([
            'title' => 'required',
            'document' => 'required|file',
            'class' => 'required',
            'section' => 'required',
            'subject' => 'required',
            'date' => 'required',
        ]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::beginTransaction();

            $homeworkId = DB::table('homework')->insertGetId([
                'title' => $request->title, // <--- SAVES YOUR AUTO-GENERATED TITLE
                'session_id' => setting('session'), 
                'classes_id' => $request->class,
                'section_id' => $request->section,
                'subject_id' => $request->subject,
                'date' => $request->date,
                'submission_date' => $request->submission_date ?? $request->date,
                'description' => $request->description,
                'task_type' => 'quiz', 
                'created_by' => Auth::id() ?? 1, 
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Process CSV if a document was uploaded and task type is 'quiz'
            if ($request->hasFile('document') && $request->task_type === 'quiz') {
                $file = $request->file('document');
                $handle = fopen($file->getPathname(), "r");
                $header = true;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($header) {
                        $header = false; 
                        continue;
                    }

                    if (isset($data[0]) && !empty(trim($data[0]))) {
                        DB::table('homework_quiz_questions')->insert([
                            'homework_id' => $homeworkId,
                            'question' => $data[0] ?? '-',
                            'option_a' => $data[1] ?? '-',
                            'option_b' => $data[2] ?? '-',
                            'option_c' => $data[3] ?? '-',
                            'option_d' => $data[4] ?? '-',
                            'correct_answer' => $data[5] ?? '-',
                            'hint' => $data[6] ?? null,
                            'explanation' => $data[7] ?? null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
                fclose($handle);
            }

            DB::commit();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // PRODUCTION: Redirect back to the index page with a success message
            return redirect()->route('homework.index')->with('success', 'Homework/Quiz created successfully!');

        } catch (\Throwable $th) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 
            // PRODUCTION: Redirect back with the error message
            return redirect()->back()->with('error', 'Error saving quiz: ' . $th->getMessage());
        }
    }
}
/* END: Daily Homework Quiz Feature - Production Mode */