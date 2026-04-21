<?php

namespace Modules\BehaviourRecord\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BehaviourRecordStoreRequest;
use App\Models\StudentInfo\Student;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\BehaviourRecord\BehaviourRecordRepository;
use App\Repositories\Certificate\CertificateRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\BehaviourRecord\Entities\Incident;
use Modules\BehaviourRecord\Entities\StudentIncidentAssign;

class BehaviourRecordController extends Controller
{

    private $classSetupRepo;
    private $behaviourRecordRepo;

    function __construct(ClassSetupRepository $classSetupRepo, BehaviourRecordRepository $behaviourRecordRepo)
    {
        $this->classSetupRepo     = $classSetupRepo;
        $this->behaviourRecordRepo = $behaviourRecordRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['title'] = ___('behaviourrecord.Behaviour Record');
        $data['request'] = $request;
        $data['classes'] = $this->classSetupRepo->all();
        $data['sections'] = [];
        $data['students'] = $this->behaviourRecordRepo->list($request);
        $data['incidents'] = Incident::all();

        return view('behaviourrecord::record', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('behaviourrecord::create');
    }

    public function getStudentRecord($id)
    {
        $record = $this->behaviourRecordRepo->getRecordByStudentId($id);
        return response()->json($record);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BehaviourRecordStoreRequest $request)
    {
        $result = $this->behaviourRecordRepo->store($request);
        if ($result) {
            return back()->with('success', ___('behaviourrecord.Behaviour Record Added Successfully'));
        }
        return back()->with('error', ___('behaviourrecord.Something Went Wrong'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('behaviourrecord::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('behaviourrecord::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
