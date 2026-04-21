<?php

namespace App\Models;

use App\Models\OnlineExamination\QuestionBank;
use Illuminate\Database\Eloquent\Model;

class HomeworkQuestion extends Model
{
    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function question()
    {
        return $this->belongsTo(QuestionBank::class,'question_bank_id','id');
    }
}
