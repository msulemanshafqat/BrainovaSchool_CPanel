<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialDiscountStoreRequest;
use App\Models\SpecialDiscount;
use App\Models\Staff\Staff;
use App\Models\StudentInfo\Student;
use App\Models\StudentSpecialDiscount;
use App\Repositories\Academic\SectionRepository;
use App\Repositories\GenderRepository;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\SpecialDiscountRepository;
use App\Repositories\Staff\DepartmentRepository;
use App\Repositories\StudentInfo\ParentGuardianRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Repositories\StudentInfo\StudentCategoryRepository;
use Illuminate\Http\Request;

class SpecialDiscountController extends Controller
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

    public function index()
    {
        $data = [];
        $data['title'] = "Special Discount";
        $data['discounts']   = $this->discountRepo->all();
        return view('backend.fees.fees-discount.special-discount.index',compact('data'));
    }

    public function assignDiscount(Request $request)
    {
        $result = $this->discountRepo->assignDiscount($request);
        if ($result) {
            return back()->with('success', 'Discount assigned successfully');
        }
        return back()->with('danger', 'Something went wrong');
    }


    public function assignStudent(Request $request)
    {
        $data = [];
        $data['title'] = "Student Special Discount Assign";
        $data['classes']    = $this->classRepo->assignedAll();
        $data['sections']   = $this->sectionRepo->all();
        $data['genders']    = $this->genderRepo->all();
        $data['categories'] = $this->categoryRepo->all();
        $data['staff']      = Staff::all();
        $data['parents']    = $this->parentRepo->get();
        $data['discounts']  = $this->discountRepo->all();

        $filters = $request->only(['class', 'section', 'gender', 'student_category', 'staff', 'parent']);

        $data['students'] = $this->studentRepo->getFilteredStudents($filters);
        $studentIds = $data['students']->pluck('student_id')->toArray();
        $data['assignedDiscounts'] = $this->studentRepo->getAssignedDiscounts($studentIds);

        return view('backend.fees.fees-discount.special-discount.assign', compact('data'));
    }


    public function store(SpecialDiscountStoreRequest $request)
    {
        $specialDiscount = $this->discountRepo->store($request);
        if ($specialDiscount) {
            return redirect()->back()->with('success', 'Created Successfully');
        }
        return redirect()->back()->with('danger', 'Something went wrong');
    }

    public function edit($id)
    {
        $data = SpecialDiscount::find($id);
        $data['edit_discount'] = $data;
        $data['title'] = "Special Discount";
        $data['discounts']   = $this->discountRepo->all();
        return view('backend.fees.fees-discount.special-discount.index',compact('data'));
    }

    public function update(SpecialDiscountStoreRequest $request)
    {
        $result = $this->discountRepo->update($request);
        if ($result) {
            return redirect()->back()->with('success', 'Updated Successfully');
        }
        return redirect()->back()->with('danger', 'Something went wrong');
    }

    public function delete($id)
    {
        $result = $this->discountRepo->destroy($id);
        if($result):
            $success[0] = 'Deleted successfully';
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
        else:
            $success[0] = 'Something went wrong!';
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
        endif;
        return response()->json($success);
    }

    public function reportDetails($id)
    {
        $data = Student::with('specialDiscount.discount', 'feesMasters.type')->find($id);
        $data['title'] = "Report Details";
        $data['discount'] = $data->specialDiscount?->discount;

        return view('backend.fees.fees-discount.special-discount.report_details', compact('data'));
    }
}
