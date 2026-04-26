<?php

namespace App\Repositories\BehaviourRecord;

use App\Repositories\Academic\ClassSetupRepository;
use Modules\BehaviourRecord\Entities\StudentIncidentAssignAppeal;

class StudentBehaviourRecordRepository
{

    private $classSetupRepo;
    private $behaviourRecordRepo;
    private $incidentAppealRepo;

    function __construct(ClassSetupRepository $classSetupRepo, BehaviourRecordRepository $behaviourRecordRepo, StudentIncidentAssignAppealRepository $incidentAppealRepo)
    {
        $this->classSetupRepo     = $classSetupRepo;
        $this->behaviourRecordRepo = $behaviourRecordRepo;
        $this->incidentAppealRepo = $incidentAppealRepo;
    }

    public function store($request)
    {
        try {
            $data = new StudentIncidentAssignAppeal();
            $data->title = $request->title;
            $data->description = $request->description;
            $data->assign_id = $request->record_id;
            $data->appeal_by = auth()->id();
            $data->student_id = $request->student_id;
            $data->save();
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function withdraw($id){
        $data = StudentIncidentAssignAppeal::find($id);
        $data->status = 'withdraw';
        $data->save();
        return true;
    }

}
