<?php

namespace Modules\LiveChat\Http\Controllers;

use App\Enums\RoleEnum;
use App\Interfaces\UserInterface;
use App\Models\Staff\Staff;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\Student;
use App\Models\User;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Modules\LiveChat\Http\Requests\MessageRequest;
use Modules\LiveChat\Interfaces\LiveChatInterface;

class MessageController extends Controller
{
    use ApiReturnFormatTrait;

    protected $authUser;
    protected $users;
    protected $message;

    public function __construct(UserInterface $userInterface, LiveChatInterface $liveChatInterface)
    {
        $this->users = $userInterface;
        $this->message = $liveChatInterface;
    }

    public function chat($id)
    {
        $data['messages'] = $this->message->model()->UserReceiverIdOrReceiverUserId($id)->orderBy('created_at','ASC')->get();
        $data['title'] = ___('student_info.Live_Chat');
        $data['user'] = $this->users->model()->with('image')->where('id', $id)->first();
        return view('livechat::message.index', compact('data'));
    }

    public function store(MessageRequest $request, $id)
    {
        try {
            $request->merge(['user_id' => $id]);
            $result = $this->message->store($request);
            if ($result->original['result']) {
                return $this->responseWithSuccess($result->original['message'], @globalAsset(auth()->user()->image->original)); // return success response
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
            }
        } catch (\Throwable $th) {dd($th);
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
        }
    }


    public function AdminChat($id)
    {
        $user = User::find(decryptFunction($id));
        $this->message->readMessages($id);
        $data['messages'] = $this->message->model()->UserReceiverIdOrReceiverUserId($user->id)->orderBy('created_at','ASC')->get();
        $data['title'] = ___('student_info.Live_Chat');
        $data['user'] = $user;
        return view('livechat::message.admin')->with($data);
    }

    public function adminStore(MessageRequest $request, $id)
    {
        try {
            $request->merge(['user_id' => $id]);
            $result = $this->message->store($request);

            if ($result->original['result']) {
                return response()->json($result->original);
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
            }
        } catch (\Throwable $th) {

            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
        }
    }

    public function studentChat($id)
    {
        $decryptedId = decryptFunction($id);

        $staff = Staff::with('upload')->where('user_id', $decryptedId)->first();

        $parentUser = null;
        if (! $staff && ParentGuardian::where('user_id', $decryptedId)->exists()) {
            $parentUser = User::with('upload')->find($decryptedId);
        }

        if ($staff) {
            $user = $staff;
        } elseif ($parentUser) {
            $name = trim((string) ($parentUser->name ?? ''));
            $parts = preg_split('/\s+/', $name, 2, PREG_SPLIT_NO_EMPTY);
            $parentUser->first_name = $parts[0] ?? $name;
            $parentUser->last_name = $parts[1] ?? '';
            $parentUser->user_id = $parentUser->id;
            $user = $parentUser;
        } else {
            abort(404, 'User not found.');
        }

        // Mark messages as read
        $this->message->readMessages($id);

        // Fetch messages
        $data['messages'] = $this->message->model()
            ->UserReceiverIdOrReceiverUserId($user->user_id)
            ->orderBy('created_at', 'ASC')
            ->get();

        // Set additional view data
        $data['title'] = ___('student_info.Live_Chat');
        $data['user'] = $user;

        return view('livechat::message.student')->with($data);
    }


    public function studentStore(MessageRequest $request, $id)
    {
        try {
            $request->merge(['user_id' => $id]);
            $result = $this->message->store($request);

            if ($result->original['result']) {
                return response()->json($result->original);
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
            }
        } catch (\Throwable $th) {

            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
        }
    }
    public function instructorChat(Request $request, $id)
    {
        $user_id = null;
        $authUserRoleId = auth()->user()->role_id;

        if ($authUserRoleId != RoleEnum::SUPERADMIN || $authUserRoleId != RoleEnum::ADMIN){
            $user = User::find(decryptFunction($id));
            $user_id = $user->id;
        }else{
            if ($request->filled('user')  && $request->user == 'guardian') {
                $user = ParentGuardian::where('user_id', decryptFunction($id))->first();
            }else{
                $user = Student::where('user_id', decryptFunction($id))->first();
            }
            $user_id = $user->user_id;
        }

        $this->message->readMessages($id);
        $data['messages'] = $this->message->model()->UserReceiverIdOrReceiverUserId($user_id)->orderBy('created_at','ASC')->get();
        $data['title'] = ___('student_info.Live_Chat');
        $data['user'] = $user;
        return view('livechat::message.instructor', compact('data'));
    }


    public function instructorStore(MessageRequest $request, $id)
    {
        try {
            $request->merge(['user_id' => $id]);
            $result = $this->message->store($request);
            if ($result->original['result']) {
                return response()->json($result->original); // return success response
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
            }
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
        }
    }

    public function guardianChat($id)
    {
        $user = Staff::where('user_id', decryptFunction($id))->first();
        $this->message->readMessages($id);
        $data['messages'] = $this->message->model()->UserReceiverIdOrReceiverUserId($user->user_id)->orderBy('created_at','ASC')->get();
        $data['title'] = ___('student_info.Live_Chat');
        $data['user'] = $user;
        return view('livechat::message.guardian')->with($data);
    }

    public function guardianStore(MessageRequest $request, $id)
    {
        try {
            $request->merge(['user_id' => $id]);
            $result = $this->message->store($request);
            if ($result->original['result']) {
                return response()->json($result->original); // return success response
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
            }
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400); // return error response
        }
    }

    public function messageRead($id)
    {
        $this->message->readMessages($id);
        return $this->responseWithSuccess(___('student_info.message_read_successfully'), []); // return success response
    }

    public function lastMessage($id)
    {
    }
}
