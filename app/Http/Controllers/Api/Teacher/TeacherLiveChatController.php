<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Models\User;
use App\Models\Staff\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiReturnFormatTrait;
use App\Http\Resources\Student\ChatListResource;
use Modules\LiveChat\Interfaces\LiveChatInterface;
use App\Http\Resources\Student\ChatConversionResource;

class TeacherLiveChatController extends Controller
{

    protected $livechat;

    public function __construct(LiveChatInterface $livechat)
    {
        $this->livechat = $livechat;
    }

    use ApiReturnFormatTrait;

    public function chatList(Request $request)
    {
        try {
            $data = $this->livechat->instructorChatList($request);
            $data['users'] = ChatListResource::collection($data['users']);
            return $this->responseWithSuccess('Teacher chat list', $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError('Something went wrong');
        }
    }


     public function chatConversions(Request $request, $id)
    {
        try{
            $reciver_user = User::find($id);

            if (empty($reciver_user)) {
                return $this->responseWithError('User not found');
            }
            $this->livechat->readMessages(encryptFunction($id));

            $authUser = auth()->user();

            $data['sender'] = [
                'id' => $authUser->id,
                'name' => $authUser->name,
                'avatar' => @globalAsset($authUser->upload->path),
            ];

            $data['receiver'] = [
                'id' => $reciver_user->id,
                'name' => $reciver_user->name,
                'avatar' => @globalAsset($reciver_user->upload->path),
            ];

            $messages = $this->livechat->model()->UserReceiverIdOrReceiverUserId($reciver_user->id)->orderBy('created_at', 'ASC')->get();
            $data['messages'] = ChatConversionResource::collection($messages);

            return $this->responseWithSuccess('Conversions', $data, 200);

        }catch (\Exception $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $id = encryptFunction($request->receiver_id);
            $request->merge(['user_id' => $id]);
            $result = $this->livechat->store($request);

            if ($result->original) {
                return $this->responseWithSuccess($result->original['message'], @globalAsset(auth()->user()->image->original));
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400);
            }

        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400);
        }
    }
}
