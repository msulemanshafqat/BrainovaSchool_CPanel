<?php

namespace App\Repositories\BehaviourRecord;

use App\Enums\RoleEnum;
use App\Models\StudentInfo\Student;
use Modules\BehaviourRecord\Entities\StudentIncidentAssign;

class BehaviourRecordRepository
{

    public function list($request)
    {
        $query = Student::with([
            'behaviourRecord.incident',
            'session_class_student.class',
            'session_class_student.section'
        ]);

        $hasFilter = $request->filled('class') || $request->filled('section') || $request->filled('keyword');

        if (!$hasFilter) {
            $query->whereHas('behaviourRecord');
        }

        // Filter by class and section
        if ($request->filled('class') || $request->filled('section')) {
            $query->whereHas('session_class_student', function ($q) use ($request) {
                if ($request->filled('class')) {
                    $q->where('classes_id', $request->class);
                }
                if ($request->filled('section')) {
                    $q->where('section_id', $request->section);
                }
            });
        }

        // Keyword search
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $keyword = $request->keyword;
                $q->where('first_name', 'like', "%$keyword%")
                    ->orWhere('last_name', 'like', "%$keyword%")
                    ->orWhere('admission_no', 'like', "%$keyword%");
            });
        }

        return $query->paginate(10);
    }

    public function store($request)
    {
        try {
            $assignRecord = new StudentIncidentAssign();
            $assignRecord->incident_id = $request->incident_id;
            $assignRecord->student_id = $request->student_id;
            $assignRecord->request_by = auth()->user()->id;
            if (auth()->user()->role_id == RoleEnum::SUPERADMIN) {
                $assignRecord->approved_by = auth()->user()->id;
                $assignRecord->status = 'approved';
            }
            $assignRecord->short_note = $request->short_note;
            $assignRecord->save();
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function getRecordByStudentId($id)
    {
        return Student::with([
            'behaviourRecord.incident',
            'behaviourRecord.requestBy:id,name',
            'behaviourRecord.approvedBy:id,name',
            'session_class_student.class',
            'session_class_student.section'
        ])->where('id', $id)->first();
    }

    public function getRecordByStudent($id)
    {
        return StudentIncidentAssign::with('student', 'appeal', 'incident')
            ->where('student_id', $id)
            ->latest()
            ->paginate(10);
    }
}
