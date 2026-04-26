<?php

namespace App\Repositories\BehaviourRecord;

use Modules\BehaviourRecord\Entities\Incident;

class IncidentRepository
{

    public function all()
    {
        return Incident::all();
    }

    public function store($request)
    {
        try {
            Incident::create($request->validated());
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public function getIncidentById($id)
    {
        return Incident::findOrFail($id);
    }

    public function update($request)
    {
        try {
            Incident::find($request->incident_id)
                ->update($request->validated());
            return true;
        }catch (\Exception $e){
            return false;
        }

    }

    public function delete($id)
    {
        try {
            Incident::destroy($id);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
