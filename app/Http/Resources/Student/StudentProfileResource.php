<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StudentProfileResource extends JsonResource
{
    public function toArray($request)
    {
        $avatarPath = @$this->upload->path;
        $avatarPath = @$this->user->upload->path;
        if (Auth::user()->role_id == 6) {
            $sessionClassStudent = sessionClassStudent();

            return [
                'student_id' => $this->id,
                'avatar' => @globalAsset($avatarPath, '40X40.webp'),
                'name' => Auth::user()->name,
                'class' => @$sessionClassStudent->class->name,
                'section' => @$sessionClassStudent->section->name,
                'roll' => @$sessionClassStudent->roll,
                'blood_group' => @$this->blood->name,
                'gender' => @$this->gender->name,
                'date_of_birth' => date('d/m/Y', strtotime($this->dob)),
                'religion' => @$this->religion->name,
            ];
        } else {
            return [
                'staff_id' => $this->id,
                'avatar' => @globalAsset($avatarPath, '40X40.webp'),
                'name' => Auth::user()->name,
                'gender' => @$this->gender->name,
                'date_of_birth' => date('d/m/Y', strtotime($this->dob)),
                'class' => null,
                'section' => null,
                'roll' => null,
                'blood_group' => null,
                'religion' => null,
            ];
        }

    }
}
