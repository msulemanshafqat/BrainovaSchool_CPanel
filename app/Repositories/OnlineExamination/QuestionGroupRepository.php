<?php

namespace App\Repositories\OnlineExamination;

use App\Enums\Settings;
use App\Interfaces\OnlineExamination\QuestionGroupInterface;
use App\Models\OnlineExamination\QuestionGroup;
use App\Traits\ReturnFormatTrait;

class QuestionGroupRepository implements QuestionGroupInterface
{
    use ReturnFormatTrait;
    private $model;

    public function __construct(QuestionGroup $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->active()->where('is_homework', 0)->get();
    }

    public function getAll()
    {
        return $this->model->latest()->where('is_homework', 0)->paginate(Settings::PAGINATE);
    }
    public function getHomeworkGroup()
    {
        return $this->model->latest()->where('is_homework', 1)->paginate(Settings::PAGINATE);
    }

    public function search($request)
    {
        $result = $this->model;

        if($request->keyword != "") {
            $result = $result->where('name', 'LIKE', "%{$request->keyword}%");
        }

        return $result->latest()->paginate(Settings::PAGINATE);
    }

    public function store($request)
    {
        try {
            $row              = new $this->model;

            $row->session_id  = setting('session');
            $row->name        = $request->name;
            $row->is_homework = $request->is_homework ? 1 : 0;
            $row->status      = $request->status;
            $row->save();
            return $row;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function update($request, $id)
    {
        try {
            $row              = $this->model->findOrfail($id);
            $row->name        = $request->name;
            $row->status      = $request->status;
            $row->save();
            return $row;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $row = $this->model->find($id);
            $row->delete();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }
}
