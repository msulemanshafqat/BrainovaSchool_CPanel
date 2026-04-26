<?php

namespace App\Http\Controllers;

use App\Models\StudentInfo\SessionClassStudent;
use App\Models\StudentInfo\Student;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\Academic\SectionRepository;
use App\Repositories\Examination\ExamAssignRepository;
use App\Repositories\GenderRepository;
use App\Repositories\Report\FeesCollectionRepository;
use App\Repositories\SpecialDiscountRepository;
use App\Repositories\Staff\DepartmentRepository;
use App\Repositories\StudentInfo\ParentGuardianRepository;
use App\Repositories\StudentInfo\StudentCategoryRepository;
use App\Repositories\StudentInfo\StudentRepository;
use Illuminate\Http\Request;

class DiscountReportController extends Controller
{

    private $genderRepo;
    private $categoryRepo;
    private $classRepo;
    private $studentRepo;
    private $departmentRepo;
    private $parentRepo;
    private $discountRepo;
    private $sectionRepo;


    function __construct(
        GenderRepository $genderRepo,
        StudentCategoryRepository $categoryRepo,
        ClassesRepository $classRepo,
        StudentRepository $studentRepo,
        ParentGuardianRepository $parentRepo,
        DepartmentRepository $departmentRepo,
        SpecialDiscountRepository $discountRepo,
        SectionRepository $sectionRepo,
    )
    {

        $this->genderRepo        = $genderRepo;
        $this->categoryRepo      = $categoryRepo;
        $this->classRepo         = $classRepo;
        $this->studentRepo       = $studentRepo;
        $this->parentRepo        = $parentRepo;
        $this->departmentRepo    = $departmentRepo;
        $this->discountRepo    = $discountRepo;
        $this->sectionRepo       = $sectionRepo;
    }


    public function index(Request $request)
    {
        $data['classes']   = $this->classRepo->assignedAll();
        $data['discounts'] = $this->discountRepo->all();
        $data['sections']  = [];
        $data['students']  = [];
        $data['request']   = $request;

        $query = SessionClassStudent::query()
            ->with('student.specialDiscount.discount') // eager load nested discount
            ->whereHas('student.specialDiscount');     // filter only students having special discount

        if ($request->filled('class')) {
            $query->where('classes_id', $request->class);
        }

        if ($request->filled('section')) {
            $query->where('section_id', $request->section);
        }

        if ($request->filled('student')) {
            $query->where('student_id', $request->student);
        }

        if ($request->filled('discount')) {
            $query->whereHas('student.specialDiscount', function ($q) use ($request) {
                $q->where('special_discount_id', $request->discount);
            });
        }

        $data['records'] = $query->paginate(10);

        return view('backend.fees.fees-discount.special-discount.report', compact('data'));
    }

}
