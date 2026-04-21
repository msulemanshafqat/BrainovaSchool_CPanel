<?php

namespace Modules\BehaviourRecord\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\CommonHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\BehaviourRecord\Entities\AppealConversation;

class AppealConversationController extends Controller
{

    use CommonHelperTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('behaviourrecord::index');
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
    public function store(Request $request)
    {
        try {
            $conversation = new AppealConversation();
            $conversation->appeal_id = $request->appeal_id;
            $conversation->message = $request->message;
            $conversation->sender_id = auth()->id();
            $conversation->receiver_id = $request->receiver_id;
            if ($request->hasFile('attachment')) {
                $conversation->attachment_id = $this->UploadImageCreate($request->attachment, 'backend/uploads/appeals');
            }

            $conversation->save();
            return back()->with('success', 'Message sent successfully');
        }catch (\Exception $e){
            dd($e->getMessage());
        }
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
