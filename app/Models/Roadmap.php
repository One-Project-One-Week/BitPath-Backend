<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roadmap extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt',
        'title',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->hasOne(Plan::class);
    }

    public function planRequest()
    {
        return $this->hasOne(PlanRequest::class);
    }

    public function roadmapSkills()
    {
        return $this->hasMany(RoadmapSkill::class);
    }
}
