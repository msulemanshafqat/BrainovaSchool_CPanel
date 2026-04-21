<?php

namespace Modules\BehaviourRecord\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncidentStoreRequest;
use App\Http\Requests\IncidentUpdateRequest;
use App\Repositories\BehaviourRecord\IncidentRepository;
use Modules\BehaviourRecord\Entities\Incident;

class IncidentController extends Controller
{

    private $incidentRepo;

    public function __construct(IncidentRepository $incidentRepo){
        $this->incidentRepo = $incidentRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = ___('behaviourrecord.Incidents');
        $data['contents'] = Incident::paginate(10);
        return view('behaviourrecord::incident', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('behaviourrecord::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IncidentStoreRequest $request)
    {
        $result = $this->incidentRepo->store($request);
        if ($result) {
            return back()->with('success', ___('incident.Incident Added Successfully'));
        }
        return  back()->with('error', ___('incident.Something went wrong!'));
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
        $incident = $this->incidentRepo->getIncidentById($id);
        return response()->json($incident);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IncidentUpdateRequest $request)
    {

        $result = $this->incidentRepo->update($request);
        if ($result) {
            return redirect()->back()->with('success', 'Incident updated successfully');
        } else {
            return redirect()->back()->with('error', ___('incident.Something went wrong!'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->incidentRepo->delete($id);
        if ($result) {
            $success[0] = 'Deleted Successfully';
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        } else {
            $response = [
                'message' => 'Something went wrong',
                'status' => 'error',
                'title' => ___('alert.oops'),
                'button' => ___('alert.OK'),
            ];
            return response()->json($response);
        }
    }
}
