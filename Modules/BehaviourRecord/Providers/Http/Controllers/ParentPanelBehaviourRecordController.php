<?php

namespace Modules\BehaviourRecord\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\Student;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\BehaviourRecord\BehaviourRecordRepository;
use App\Repositories\BehaviourRecord\StudentBehaviourRecordRepository;
use App\Repositories\BehaviourRecord\StudentIncidentAssignAppealRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ParentPanelBehaviourRecordController extends Controller
{

    private $classSetupRepo;
    private $behaviourRecordRepo;
    private $studentBehaviourRecordRepo;
    private $incidentAppealRepo;

    function __construct(
        ClassSetupRepository                  $classSetupRepo,
        BehaviourRecordRepository             $behaviourRecordRepo,
        StudentIncidentAssignAppealRepository $incidentAppealRepo,
        StudentBehaviourRecordRepository      $studentBehaviourRecordRepo)
    {
        $this->classSetupRepo     = $classSetupRepo;
        $this->behaviourRecordRepo = $behaviourRecordRepo;
        $this->incidentAppealRepo = $incidentAppealRepo;
        $this->studentBehaviourRecordRepo = $studentBehaviourRecordRepo;
    }

    public function index(Request $request)
    {
        $data['title'] = ___('behaviourrecord.Behaviour Record');
        $data['request'] = $request;
        $data['records'] = [];

        $parent                 = ParentGuardian::where('user_id', Auth::user()->id)->first();
        $data['students']       = Student::where('parent_guardian_id', $parent->id)->get();
        $data['fees_assigned']  = [];

        if ($request->filled('student_id')) {
            $data['records']  = $this->behaviourRecordRepo->getRecordByStudent($request->student_id);
        }

        return view('behaviourrecord::parent-panel.record', compact('data') );
    }


    public function details($id)
    {
        $data = $this->incidentAppealRepo->details($id);
        $data['title'] = ___('behaviourrecord.Appeal Details');
        return view('behaviourrecord::parent-panel.appeal_details', compact('data'));
    }

    public function appealStore(Request $request)
    {
        $result = $this->studentBehaviourRecordRepo->store($request);
        if ($result) {
            return back()->with('success', 'Appeal sent successfully');
        } else {
            return back()->with('error', 'Something went wrong');
        }

    }
}
