<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'skill_id',
        'is_finished',
        'total_tasks',
        'completed_tasks'
    ];

    public function roadmapSkill()
    {
        return $this->belongsTo(RoadmapSkill::class, 'skill_id');
    }
    

    public function planParticipants()
    {
        return $this->hasMany(PlanParticipant::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
