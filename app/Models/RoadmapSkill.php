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
        'level',
        'recommended_resource',
    ];

    protected $with = ['recommandResource'];

    public function roadmap()
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function planRequest()
    {
        return $this->hasOne(PlanRequest::class, 'skill_id');
    }

    public function recommandResource()
    {
        return $this->hasOne(RecommandResource::class, 'skill_id');
    }
}
