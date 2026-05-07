<?php

namespace App\Repositories\Academic;

use App\Enums\Settings;
use App\Models\Academic\Classes;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Academic\SubjectAssignChildren;
use App\Interfaces\Academic\ClassesInterface;
use App\Models\ClassTranslate;

class ClassesRepository implements ClassesInterface
{
    use ReturnFormatTrait;

    private $classes;
    private $classTrans;

    public function __construct(Classes $classes , ClassTranslate $classTrans)
    {
        $this->classes = $classes;
        $this->classTrans = $classTrans;
    }

    public function assignedAll()
    {
        return ClassSetup::active()->where('session_id', setting('session'))->get();
    }

    public function all()
    {
        return $this->classes->active()->get();
    }

    public function getAll()
    {
        return $this->classes->latest()->paginate(Settings::PAGINATE);
    }

    public function getAllForAssignedTeacher(int $staffId)
    {
        $sessionId  = setting('session');
        $childTable = (new SubjectAssignChildren())->getTable();

        $classIds = SubjectAssignChildren::query()
            ->where($childTable . '.staff_id', $staffId)
            ->join('subject_assigns', 'subject_assigns.id', '=', $childTable . '.subject_assign_id')
            ->where('subject_assigns.session_id', $sessionId)
            ->distinct()
            ->pluck('subject_assigns.classes_id')
            ->unique()
            ->filter()
            ->values();

        if ($classIds->isEmpty()) {
            return $this->classes->latest()->whereRaw('1 = 0')->paginate(Settings::PAGINATE);
        }

        return $this->classes->latest()->whereIn('id', $classIds)->paginate(Settings::PAGINATE);
    }

    public function staffTeachesClass(int $staffId, int $classId): bool
    {
        $sessionId  = setting('session');
        $childTable = (new SubjectAssignChildren())->getTable();

        return SubjectAssignChildren::query()
            ->where($childTable . '.staff_id', $staffId)
            ->join('subject_assigns', 'subject_assigns.id', '=', $childTable . '.subject_assign_id')
            ->where('subject_assigns.session_id', $sessionId)
            ->where('subject_assigns.classes_id', $classId)
            ->exists();
    }

    public function store($request)
    {
        try {
            $classesStore              = new $this->classes;
            $classesStore->name        = $request->name;
            $classesStore->status      = $request->status;
            $classesStore->save();
            return $this->responseWithSuccess(___('alert.created_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function show($id)
    {
        return $this->classes->find($id);
    }

    public function update($request, $id)
    {
        try {
            $classesUpdate              = $this->classes->findOrfail($id);
            $classesUpdate->name        = $request->name;
            $classesUpdate->status      = $request->status;
            $classesUpdate->save();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        try {
            $classesDestroy = $this->classes->find($id);
            $classesDestroy->delete();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function translates($id){

        return $this->classTrans->where('class_id',$id)->get()->groupBy('locale');
    }

    public function translateUpdate($request, $id){
        DB::beginTransaction();
        try {
            $delete_old = $this->classTrans->where('class_id',$id)->delete();
            $class = $this->show($id);

            foreach($request->name as $key => $name){
                $row                   = new $this->classTrans;
                $row->class_id        = $id ;
                $row->locale           = $key ;
                $row->name             = $name;
                $row->save();
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }




}
