<?php

namespace Modules\BehaviourRecord\Entities;

use App\Models\StudentInfo\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BehaviourRecord\Database\factories\StudentIncidentAssignAppealFactory;
use Modules\MainApp\Entities\User;

class StudentIncidentAssignAppeal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): StudentIncidentAssignAppealFactory
    {
        //return StudentIncidentAssignAppealFactory::new();
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function behaviourRecord(){
        return $this->belongsTo(StudentIncidentAssign::class, 'assign_id');
    }

    public function requestBy()
    {
        return $this->belongsTo(User::class, 'appeal_by');
    }


}
