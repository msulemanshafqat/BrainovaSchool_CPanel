<?php


namespace App\Http\Controllers\Api\Parent;


use App\Http\Controllers\Controller;
use App\Http\Resources\Student\ChatConversionResource;
use App\Http\Resources\Student\ChatListResource;
use App\Models\Staff\Staff;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use Modules\LiveChat\Interfaces\LiveChatInterface;

class ParentLivechatAPIController extends Controller
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
            $data = $this->livechat->guardianChatList($request);
            $data['users'] = ChatListResource::collection($data['users']);
            return $this->responseWithSuccess('Guardian chat list', $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError('Something went wrong');
        }
    }


    public function sendMessage(Request $request)
    {
        try {
            $id = encryptFunction($request->receiver_id);
            $request->merge(['user_id' => $id]);
            $result = $this->livechat->store($request);

            if ($result->original) {
                return $this->responseWithSuccess($result->original['message'], '');
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400);
            }

        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), [], 400);
        }
    }


    public function chatConversions(Request $request, $id)
    {
        $user = Staff::find($id);
        if (empty($user)) {
            return $this->responseWithError('User not found');
        }
        $this->livechat->readMessages(encryptFunction($user->user_id));

        $authUser = auth()->user();

        $data['sender'] = [
            'id' => $authUser->id,
            'name' => $authUser->name,
            'avatar' => '',
        ];

        $data['receiver'] = [
            'id' => $user->user_id,
            'name' => $user->first_name . ' ' . $user->last_name,
            'avatar' => '',
        ];

        $messages = $this->livechat->model()->UserReceiverIdOrReceiverUserId($user->user_id)->orderBy('created_at', 'ASC')->get();
        $data['messages'] = ChatConversionResource::collection($messages);

        return $this->responseWithSuccess('Conversions', $data, 200);
    }
}
