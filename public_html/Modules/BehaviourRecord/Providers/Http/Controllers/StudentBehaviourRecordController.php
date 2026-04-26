<?php

namespace Modules\BehaviourRecord\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppealStoreRequest;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\BehaviourRecord\BehaviourRecordRepository;
use App\Repositories\BehaviourRecord\StudentBehaviourRecordRepository;
use App\Repositories\BehaviourRecord\StudentIncidentAssignAppealRepository;
use Modules\BehaviourRecord\Entities\Incident;
use Illuminate\Http\Request;

class StudentBehaviourRecordController extends Controller
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
        $user = auth()->user()->load('student');
        $data['title'] = ___('behaviourrecord.Behaviour Record');
        $data['request'] = $request;
        $data['classes'] = $this->classSetupRepo->all();
        $data['sections'] = [];
        $data['records'] = $this->behaviourRecordRepo->getRecordByStudent($user->student->id);
        $data['incidents'] = Incident::all();
        return view('behaviourrecord::student-panel.record', compact('data'));
    }


    public function appealStore(AppealStoreRequest $request)
    {
        $result = $this->studentBehaviourRecordRepo->store($request);
        if ($result) {
            return back()->with('success', 'Appeal sent successfully');
        } else {
            return back()->with('error', 'Something went wrong');
        }
    }

    public function withdraw($id){
        $result = $this->studentBehaviourRecordRepo->withdraw($id);
        if($result){
            return back()->with('success', 'Appeal withdrawn successfully');
        }
        return back()->with('error', 'Something went wrong');
    }

    public function details($id)
    {
        $data = $this->incidentAppealRepo->details($id);
        $data['title'] = ___('behaviourrecord.Appeal Details');
        return view('behaviourrecord::student-panel.appeal_details', compact('data'));
    }

}
