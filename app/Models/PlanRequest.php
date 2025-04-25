<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanRequest extends Model
{
    protected $fillable = [
        'user_id',
        'skill_id',
        'type',
        'duration',
        'days'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roadmapSkill()
    {
        return $this->belongsTo(RoadmapSkill::class, 'skill_id');
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'request_id');
    }
}
