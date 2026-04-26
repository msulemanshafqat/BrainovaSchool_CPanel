<?php

namespace Modules\MainApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\MainApp\Http\Repositories\CityRepository;
use Illuminate\Support\Facades\Schema;
use Modules\MainApp\Entities\Country;
use Modules\MainApp\Http\Requests\City\StoreRequest;
use Modules\MainApp\Http\Requests\City\UpdateRequest;

class CityController extends Controller
{
    private $repo;

    function __construct(CityRepository $repo)
    {

        if (!Schema::hasTable('settings') && !Schema::hasTable('users')  ) {
            abort(400);
        }
        $this->repo       = $repo;
    }

    public function index()
    {
        $data['faqs']  = $this->repo->getAll();
        $data['title'] = ___('settings.City');
        return view('mainapp::city.index', compact('data'));
    }

    public function create()
    {
        $data['title'] = ___('settings.Create City');
        $data['countries'] = Country::get();
        return view('mainapp::city.create', compact('data'));
    }

    public function store(StoreRequest $request)
    {
        $result = $this->repo->store($request);
        if($result['status']){
            return redirect()->route('city.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function edit($id)
    {
        $data['city']   = $this->repo->show($id);
        $data['countries'] = Country::get();
        $data['title'] = ___('settings.Edit City');
        return view('mainapp::city.edit', compact('data'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $result = $this->repo->update($request, $id);
        if($result['status']){
            return redirect()->route('city.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {
        $result = $this->repo->destroy($id);
        if($result['status']):
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        else:
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        endif;
    }
}
