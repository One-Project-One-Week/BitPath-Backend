<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanQuiz extends Model
{
    protected $fillable = [
        'user_id',
        'skill_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skill()
    {
        return $this->belongsTo(RoadmapSkill::class,'skill_id');
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
