<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'question',
        'options',
        'correct_answer',
        'user_answer',
        'is_correct',
    ];    

    public function planQuiz()
    {
        return $this->belongsTo(PlanQuiz::class, 'quiz_id');
    }
}
