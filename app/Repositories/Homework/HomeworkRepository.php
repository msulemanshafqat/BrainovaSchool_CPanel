<?php

namespace App\Repositories\Homework;

use App\Models\Homework;
use App\Models\HomeworkQuestion;
use App\Models\HomeworkStudent;
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

    public function all()
    {
        return $this->model->active()->where('session_id', setting('session'))->orderByDesc('id')->get();
    }

    public function getPaginateAll()
    {
        return $this->model::latest()->where('session_id', setting('session'))->whereIn('subject_id', teacherSubjects())->orderByDesc('id')->paginate(10);
    }

    public function search($request)
    {
        $rows = $this->model::query();
        $rows = $rows->where('session_id', setting('session'));
        if($request->class != "") {
            $rows = $rows->where('classes_id', $request->class);
        }
        if($request->section != "") {
            $rows = $rows->where('section_id', $request->section);
        }
        if($request->subject != "") {
            $rows = $rows->where('subject_id', $request->subject);
        }
        return $rows->paginate(10);
    }

public function store($request)
    {
        DB::beginTransaction();
        try {
            $homework = new Homework(); 
            
            // ==================================================
            // 1. SETTING ALL THE HOMEWORK VARIABLES
            // ==================================================
            $homework->session_id        = setting('session');
            $homework->classes_id        = $request->class;
            $homework->section_id        = $request->section;
            $homework->subject_id        = $request->subject;
            
            $homework->title             = $request->title;
            $homework->task_type         = $request->task_type ?? 'homework'; // Default to homework
            
            $homework->date              = $request->date;
            $homework->submission_date   = $request->submission_date;
            
            $homework->marks             = $request->marks;
            $homework->status            = $request->status ?? 1;
            $homework->description       = $request->description;

            // Handle the standard document upload (if attached)
            if ($request->hasFile('document')) {
                // Assuming your trait uses UploadImage for creation (adjust if your LMS uses a different method name)
                $homework->document_id   = $this->UploadImageCreate($request->document, 'backend/uploads/homeworks'); 
            }

            $homework->save();

            // ==================================================
            // 2. HANDLE MANUAL QUESTION SELECTION (Standard LMS)
            // ==================================================
            if($request->questions_ids != null) {
                foreach($request->questions_ids as $question_id) {
                    $assign = new HomeworkQuestion();
                    $assign->homework_id = $homework->id;
                    $assign->question_bank_id = $question_id;
                    $assign->save();
                }
            }

            // ==================================================
            // 3. BULK QUESTION UPLOAD LOGIC (CSV)
            // ==================================================
            if ($request->hasFile('bulk_questions_file')) {
                $file = $request->file('bulk_questions_file');
                $handle = fopen($file->getRealPath(), "r");
                fgetcsv($handle); // Skip the header row

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Create the question in the bank
                    $questionId = DB::table('question_banks')->insertGetId([
                        'question' => $data[0],
                        'options'  => json_encode([$data[1], $data[2], $data[3], $data[4]]),
                        'answer'   => $data[5], // Correct Option (A, B, C, or D)
                        'hint'     => $data[6],
                        'explanation' => $data[7],
                        'marks'    => $request->marks, 
                        'class_id' => $request->class,
                        'section_id' => $request->section,
                        'subject_id' => $request->subject,
                        'status'   => 1,
                        'created_at' => now(),
                    ]);

                    // Link to this specific Homework
                    DB::table('homework_questions')->insert([
                        'homework_id' => $homework->id, 
                        'question_bank_id' => $questionId,
                    ]);
                }
                fclose($handle);
            }
            // ==================================================

            DB::commit();
            return ['status' => true, 'message' => ___('alert.created_successfully')];
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::error('Homework Store Error: ' . $e->getMessage()); // Uncomment to debug if it fails
            return ['status' => false, 'message' => ___('alert.something_went_wrong')];
        }
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $row                    = $this->model->find($id);
            $row->session_id        = setting('session');
            $row->classes_id        = $request->class;
            $row->section_id        = $request->section;
            $row->subject_id        = $request->subject;

            // ==================================================
            // NEW: TITLE & TASK TYPE
            // ==================================================
            $row->title             = $request->title ?? $row->title; // Keep old if not provided
            if ($request->has('task_type')) {
                $row->task_type     = $request->task_type;
            }
            // ==================================================

            $row->date              = $request->date;
            $row->submission_date   = $request->submission_date;

            $row->marks             = $request->marks;
            $row->status            = $request->status ?? 1;
            
            // Only update the document if a new one is uploaded
            if ($request->hasFile('document')) {
                $row->document_id   = $this->UploadImageUpdate($request->document, 'backend/uploads/homeworks', $row->document_id);
            }
            
            $row->description       = $request->description;

            $row->save();

            // Handle Manual Question Selection (Standard LMS Logic)
            if($request->questions_ids != null) {
                HomeworkQuestion::where('homework_id', $row->id)->delete();
                foreach($request->questions_ids as $question_id) {
                    $assign = new HomeworkQuestion();
                    $assign->homework_id = $row->id;
                    $assign->question_bank_id = $question_id;
                    $assign->save();
                }
            }

            // ==================================================
            // NEW: BULK CSV OVERRIDE LOGIC
            // ==================================================
            // If the teacher uploads a new CSV during edit, replace the old questions
            if ($request->hasFile('bulk_questions_file')) {
                
                // Clear the previous questions attached to this quiz
                DB::table('homework_questions')->where('homework_id', $row->id)->delete();

                $file = $request->file('bulk_questions_file');
                $handle = fopen($file->getRealPath(), "r");
                fgetcsv($handle); // Skip the header row

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Create the new question in the bank
                    $questionId = DB::table('question_banks')->insertGetId([
                        'question' => $data[0],
                        'options'  => json_encode([$data[1], $data[2], $data[3], $data[4]]),
                        'answer'   => $data[5],
                        'hint'     => $data[6],
                        'explanation' => $data[7],
                        'marks'    => $request->marks, 
                        'class_id' => $request->class,
                        'section_id' => $request->section,
                        'subject_id' => $request->subject,
                        'status'   => 1,
                        'created_at' => now(),
                    ]);

                    // Link the new question to this Homework/Quiz
                    DB::table('homework_questions')->insert([
                        'homework_id' => $row->id, 
                        'question_bank_id' => $questionId,
                    ]);
                }
                fclose($handle);
            }
            // ==================================================

            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            // Optional: Log the actual error for debugging during development
            // \Log::error('Homework Update Error: ' . $th->getMessage());
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }
public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->find($id);
            
            // Delete the attached file/document
            $this->UploadImageDelete($row->document_id);
            
            // NEW: Clean up the pivot table to prevent orphan records
            DB::table('homework_questions')->where('homework_id', $id)->delete();
            
            $row->delete();
            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    
    
    // ==========================================
   // NAZ
   // ==========================================
    public function evaluationSubmit($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->students as $key => $student) {
                $homework        = HomeworkStudent::where('homework_id', $request->homework_id)->where('student_id', $student)->first();
                
                // Get the marks from the request
                $currentMarks    = $request->marks[$key];
                $homework->marks = $currentMarks;
                $homework->save();

                // ==========================================
                // BRAINOVA E6 POINTS HOOK - DO NOT DELETE
                // ==========================================
                // Using 1 Mark = 10 E6 Points multiplier
                $e6PointsEarned = (int)$currentMarks * 10; 

                if ($e6PointsEarned > 0) {
                    \Illuminate\Support\Facades\DB::table('students')
                        ->where('id', $student)
                        ->increment('total_score', $e6PointsEarned);
                }
                // ==========================================
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.submitted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }
    
    
    // ==========================================
   // NAZ
   // ==========================================
    
                                                    
}
