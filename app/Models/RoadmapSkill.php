<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapSkill extends Model
{
    protected $fillable = [
        'roadmap_id',
        'skill',
        'why',
        'duration',
        'recommended_resource',
    ];

    public function roadmap()
    {
        return $this->belongsTo(roadmap::class);
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'skill_id');
    }

    public function planRequest()
    {
        return $this->hasOne(PlanRequest::class, 'skill_id');
    }
}
