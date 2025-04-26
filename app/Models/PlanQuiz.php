<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanQuiz extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
