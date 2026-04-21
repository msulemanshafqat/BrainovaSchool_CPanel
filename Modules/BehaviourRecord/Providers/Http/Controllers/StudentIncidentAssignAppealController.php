<?php

namespace Modules\BehaviourRecord\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\BehaviourRecord\StudentIncidentAssignAppealRepository;
use Illuminate\Http\Request;


class StudentIncidentAssignAppealController extends Controller
{

    private $incidentAppealRepo;
    private $classRepo;

    function __construct(StudentIncidentAssignAppealRepository $incidentAppealRepo, ClassesRepository $classRepo)
    {
        $this->incidentAppealRepo = $incidentAppealRepo;
        $this->classRepo = $classRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['title'] = ___('behaviourrecord.Appeal Request');
        $data['appeals'] = $this->incidentAppealRepo->all();
        $data['classes']  = $this->classRepo->assignedAll();
        $data['sections'] = [];
        return view('behaviourrecord::appeal', compact('data'));
    }


    public function reject($id)
    {
        $result = $this->incidentAppealRepo->reject($id);
        if ($result) {
            return back()->with('success', ___('behaviourrecord.Appeal Rejected'));
        } else {
            return back()->with('error', ___('behaviourrecord.Something went wrong'));
        }
    }

    public function withdraw($id)
    {
        $result = $this->incidentAppealRepo->withdraw($id);
        if ($result) {
            return back()->with('success', ___('behaviourrecord.Appeal Withdrawn'));
        } else {
            return back()->with('error', ___('behaviourrecord.Something went wrong'));
        }

    }

    public function details($id)
    {
        $data = $this->incidentAppealRepo->details($id);
        $data['title'] = ___('behaviourrecord.Appeal Details');
        return view('behaviourrecord::appeal_details', compact('data'));
    }

    public function changeStatus(Request $request, $id)
    {
        $result = $this->incidentAppealRepo->changeStatus($request, $id);
        if ($result) {
            return response()->json([
                'message' => 'Status updated successfully',
            ]);
        } else {
            return response()->json(['message' => 'Something went wrong']);
        }
    }


}
