<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\SystemNotification;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    use ApiReturnFormatTrait;

    public function notifications(Request $request)
    {

       $notifications = SystemNotification::where('reciver_id', auth()->id())->where('is_read', 0)->orderBy('id', 'desc')->take(10)->get();

       return $this->responseWithSuccess('Notifications list',NotificationResource::collection($notifications));
    }
}
