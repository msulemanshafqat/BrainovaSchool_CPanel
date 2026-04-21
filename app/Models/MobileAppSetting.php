<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileAppSetting extends Model
{
    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class, 'icon_path', 'id');
    }

}
