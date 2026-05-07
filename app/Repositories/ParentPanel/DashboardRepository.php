<?php

namespace App\Repositories\ParentPanel;

use App\Models\Event;
use App\Models\Homework;
use App\Models\HomeworkStudent;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\ClassRoutine;
use App\Models\Academic\SubjectAssign;
use Illuminate\Support\Facades\Session;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\Academic\SubjectAssignChildren;
use App\Models\StudentInfo\SessionClassStudent;
use App\Interfaces\ParentPanel\DashboardInterface;

class DashboardRepository implements DashboardInterface
{
    public function index()
    {
        try {
            $parent           = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();
            if(Session::get('student_id'))
                $student        = Student::where('id', Session::get('student_id'))->first();
            else
                $student        = Student::where('parent_guardian_id', $parent->id)->latest()->first();

            Session::put('student_id', @$student->id);

            if($student){
                $classSection   = SessionClassStudent::where('session_id', setting('session'))
                                ->where('student_id', @$student->id)
                                ->first();
                
                $subjectTeacher = SubjectAssign::where('session_id', setting('session'))
                                ->where('classes_id', @$classSection->classes_id)
                                ->where('section_id', @$classSection->section_id)
                                ->first();
                $data['totalSubject']   = SubjectAssignChildren::where('subject_assign_id', @$subjectTeacher->id)
                                ->distinct('subject_id')
                                ->count();
                $data['totalTeacher']   = SubjectAssignChildren::where('subject_assign_id', @$subjectTeacher->id)
                                ->distinct('staff_id')
                                ->count();
                $data['totalClass']     = ClassRoutine::where('classes_id', @$classSection->classes_id)
                                ->where('section_id', @$classSection->section_id)
                                ->where('session_id', setting('session'))
                                ->count();
                $data['totalEvent']     = Event::where('session_id', setting('session'))
                                ->active()->where('date', '>=', date('Y-m-d'))
                                ->orderBy('date')
                                ->count();
                $data['events']         = Event::where('session_id', setting('session'))
                                ->active()->where('date', '>=', date('Y-m-d'))
                                ->orderBy('date')
                                ->take(5)
                                ->get();
                $data['homework_total_marks'] = $this->homeworkTotalMarksForStudent($student);
            } else {
                $data['homework_total_marks'] = null;
            }
            $data['student'] = $student;
            return $data;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function search($request)
    {
        try {
            Session::put('student_id', $request->student);

            $parent           = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();
            $student          = Student::where('id', Session::get('student_id'))->first();

            $classSection   = SessionClassStudent::where('session_id', setting('session'))
                            ->where('student_id', @$student->id)
                            ->first();
            
            $subjectTeacher = SubjectAssign::where('session_id', setting('session'))
                            ->where('classes_id', @$classSection->classes_id)
                            ->where('section_id', @$classSection->section_id)
                            ->first();
            $data['totalSubject']   = SubjectAssignChildren::where('subject_assign_id', @$subjectTeacher->id)
                            ->distinct('subject_id')
                            ->count();
            $data['totalTeacher']   = SubjectAssignChildren::where('subject_assign_id', @$subjectTeacher->id)
                            ->distinct('staff_id')
                            ->count();
            $data['totalClass']     = ClassRoutine::where('classes_id', @$classSection->classes_id)
                            ->where('section_id', @$classSection->section_id)
                            ->where('session_id', setting('session'))
                            ->count();
            $data['totalEvent']     = Event::where('session_id', setting('session'))
                            ->active()->where('date', '>=', date('Y-m-d'))
                            ->orderBy('date')
                            ->count();
            $data['events']         = Event::where('session_id', setting('session'))
                            ->active()->where('date', '>=', date('Y-m-d'))
                            ->orderBy('date')
                            ->take(5)
                            ->get();
            $data['student'] = $student;
            $data['homework_total_marks'] = $this->homeworkTotalMarksForStudent($student);

            return $data;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Sum of graded homework marks for the child in the current session (same rule as student panel).
     */
    private function homeworkTotalMarksForStudent(?Student $student): ?float
    {
        if (!$student) {
            return null;
        }

        $sessionId = setting('session');
        $hwTable = (new Homework())->getTable();
        $hwStats = HomeworkStudent::query()
            ->join($hwTable, $hwTable . '.id', '=', 'homework_students.homework_id')
            ->where('homework_students.student_id', $student->id)
            ->whereNotNull('homework_students.marks')
            ->where($hwTable . '.session_id', $sessionId)
            ->selectRaw('COUNT(*) as c, SUM(homework_students.marks) as sum_m')
            ->first();

        if ($hwStats && (int) $hwStats->c > 0) {
            return round((float) $hwStats->sum_m, 1);
        }

        return null;
    }
}
