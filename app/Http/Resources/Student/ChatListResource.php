<?php


namespace App\Http\Resources\Student;


use App\Models\StudentInfo\ParentGuardian;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatListResource extends JsonResource
{

    public function toArray($request)
    {
        if ($this->resource instanceof ParentGuardian) {
            $name = trim(($this->guardian_name ?? '') ?: (optional($this->user)->name ?? '') ?: 'Parent');

            return [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'name' => $name,
                'email' => $this->guardian_email ?? optional($this->user)->email,
                'status' => $this->status,
                'avatar' => @globalAsset(optional($this->user)->upload->path),
                'created_at' => $this->created_at,
                'channel_name' => 'receiver_channel' . $this->user_id,
                'last_message' => $this->lastMessage ? new ChatConversionResource($this->lastMessage) : null,
            ];
        }

        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "name" => $this->first_name . ' ' . $this->last_name,
            "email" => $this->email,
            "status" => $this->status,
            "avatar" => @globalAsset(@$this->upload->path),
            "created_at" => $this->created_at,
            "channel_name" => 'receiver_channel' . $this->user_id,
            "last_message" => $this->lastMessage ? new ChatConversionResource($this->lastMessage) : null
        ];
    }
}
