<?php

namespace Modules\MainApp\Http\Repositories;

use App\Enums\Settings;
use App\Traits\ReturnFormatTrait;
use Modules\MainApp\Entities\City;
use Modules\MainApp\Entities\FrequentlyAskedQuestion;
use Modules\MainApp\Http\Interfaces\FAQInterface;

class CityRepository implements FAQInterface
{
    use ReturnFormatTrait;
    private $model;

    public function __construct(City $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->active()->get();
    }

    public function getAll()
    {
        return $this->model->paginate(Settings::PAGINATE);
    }

    public function store($request)
    {
        try {
            $row              = new $this->model;
            $row->country_id    = $request->country_id;
            $row->name      = $request->name;
            $row->status      = 1;
            $row->save();

            return $this->responseWithSuccess(___('alert.created_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
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
            $row->country_id    = $request->country_id;
            $row->name      = $request->name;
            $row->status      = $request->status;
            $row->save();

            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
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
