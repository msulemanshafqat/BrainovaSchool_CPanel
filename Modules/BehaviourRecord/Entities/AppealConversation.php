<?php

namespace Modules\BehaviourRecord\Entities;

use App\Models\User;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BehaviourRecord\Database\factories\AppealConversationFactory;


class AppealConversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): AppealConversationFactory
    {
        //return AppealConversationFactory::new();
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function attachment()
    {
        return $this->belongsTo(Upload::class, 'attachment_id');
    }
}
