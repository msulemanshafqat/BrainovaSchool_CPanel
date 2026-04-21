<?php

namespace App\Repositories\BehaviourRecord;

use App\Repositories\Academic\ClassSetupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\BehaviourRecord\Entities\AppealConversation;
use Modules\BehaviourRecord\Entities\StudentIncidentAssignAppeal;

class StudentIncidentAssignAppealRepository
{
    public function all()
    {
        return StudentIncidentAssignAppeal::
        with('student:id,first_name,last_name', 'behaviourRecord', 'requestBy:id,name')
            ->latest()
            ->paginate(10);
    }

    public function reject($id)
    {
        try {
            StudentIncidentAssignAppeal::find($id)->delete();
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function withdraw($id)
    {
        try {
            DB::beginTransaction();
            $data = StudentIncidentAssignAppeal::with('behaviourRecord')->where('student_id', $id)->first();
            $data->status = 'withdraw';
            $record = $data->behaviourRecord;
            $record->status = 'withdraw';
            $data->save();
            $record->save();
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }

    public function details($id)
    {
        $appeal = StudentIncidentAssignAppeal::with(['behaviourRecord.incident', 'student', 'requestBy'])
            ->find($id);


        $data['appeal'] = $appeal;

        if ($appeal) {
            $data['messages'] = AppealConversation::with('sender','receiver')->where('appeal_id', $id)
                ->where(function ($q) use ($appeal) {
                    $q->where(function ($q2) use ($appeal) {
                        $q2->where('sender_id', auth()->user()->id)
                            ->orWhere('receiver_id', $appeal->requestBy->id);
                    })->orWhere(function ($q2) use ($appeal) {
                        $q2->orWhere('sender_id', $appeal->requestBy->id)
                            ->where('receiver_id', auth()->user()->id);
                    });
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return $data;
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $appeal = StudentIncidentAssignAppeal::find($id);
            $appeal->status = $request->status;
            $appeal->save();

            if ($request->status == 'granted'){
                $record = $appeal->behaviourRecord;
                $record->delete();
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

}
