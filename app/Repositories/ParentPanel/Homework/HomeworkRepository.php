<?php

namespace App\Repositories\ParentPanel\Homework;

use App\Models\Homework;
use App\Models\HomeworkStudent;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\SessionClassStudent;

class HomeworkRepository implements HomeworkInterface
{
    use ReturnFormatTrait, CommonHelperTrait;

    private $model;

    public function __construct(Homework $model)
    {
        $this->model = $model;
    }

    /**
     * Initial page load.
     * Mirrors the same parent-lookup pattern as ParentPanel\DashboardRepository
     * which is known to work correctly on this installation.
     *
     * Pattern: ParentGuardian.user_id → Student.parent_guardian_id
     */
    public function indexParent(): array
    {
        // Default safe return — blade always gets the expected keys
        $data = [
            'students' => collect(),
            'student'  => null,
            'homeworks' => collect(),
        ];

        try {
            $parent = ParentGuardian::where('user_id', Auth::user()->id)->first();

            if (!$parent) {
                return $data;
            }

            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();

            // Restore last-selected child from session
            $studentId = Session::get('student_id');
            if ($studentId) {
                $data['student'] = Student::where('id', $studentId)
                    ->where('parent_guardian_id', $parent->id)
                    ->first();
            }

            return $data;

        } catch (\Throwable $th) {
            \Log::error('Parent Homework indexParent Error: ' . $th->getMessage());
            return $data;
        }
    }

    /**
     * Parent selects a child → load that child's homework with submission records.
     * Uses get() (not paginate) so the blade can compute stats across all homework.
     */
    public function search($request): array
    {
        $data = [
            'students' => collect(),
            'student'  => null,
            'homeworks' => collect(),
        ];

        try {
            $parent = ParentGuardian::where('user_id', Auth::user()->id)->first();

            if (!$parent) {
                return $data;
            }

            $studentId = (int) $request->student;
            Session::put('student_id', $studentId);

            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();
            $data['student']  = Student::where('id', $studentId)
                ->where('parent_guardian_id', $parent->id)
                ->first();

            if (!$data['student']) {
                return $data;
            }

            // Resolve current class/section for this child
            $classSection = SessionClassStudent::where('session_id', setting('session'))
                ->where('student_id', $studentId)
                ->latest()
                ->first();

            if (!$classSection) {
                return $data;
            }

            // Load all homework for this child's class/section
            $homeworks = $this->model::with(['subject', 'class', 'section', 'upload'])
                ->where('session_id', setting('session'))
                ->where('classes_id', $classSection->classes_id)
                ->where('section_id', $classSection->section_id)
                ->orderByDesc('id')
                ->get();

            // Attach submission record to each homework row
            $homeworks->transform(function ($hw) use ($studentId) {
                $hw->submission_record = HomeworkStudent::where('homework_id', $hw->id)
                    ->where('student_id', $studentId)
                    ->first();
                return $hw;
            });

            $data['homeworks'] = $homeworks;
            return $data;

        } catch (\Throwable $th) {
            \Log::error('Parent Homework Search Error: ' . $th->getMessage());
            return $data;
        }
    }

    // Legacy method kept for compatibility — not used by our new blade
    public function index($request)
    {
        return collect();
    }
}
