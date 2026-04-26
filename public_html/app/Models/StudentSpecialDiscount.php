<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSpecialDiscount extends Model
{
    protected $fillable = [
        'student_id',
        'special_discount_id',
        'short_description',
        'assigned_by',
        'assigned_date',
        'active_status',
    ];


    public function discount()
    {
        return $this->belongsTo(SpecialDiscount::class, 'special_discount_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

}
