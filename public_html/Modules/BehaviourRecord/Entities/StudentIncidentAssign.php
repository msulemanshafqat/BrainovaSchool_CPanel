<?php

namespace Modules\BehaviourRecord\Entities;

use App\Models\StudentInfo\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BehaviourRecord\Database\factories\StudentIncidentAssignFactory;
use Modules\MainApp\Entities\User;

class StudentIncidentAssign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): StudentIncidentAssignFactory
    {
        //return StudentIncidentAssignFactory::new();
    }


    public function incident(){
        return $this->belongsTo(Incident::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function requestBy()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function appeal()
    {
        return $this->hasOne(StudentIncidentAssignAppeal::class,'assign_id','id');

    }
}
