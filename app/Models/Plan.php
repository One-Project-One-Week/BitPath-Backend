<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'request_id',
        'is_finished',
        'total_tasks',
        'completed_tasks'
    ];

public function planRequest()
    {
        return $this->belongsTo(PlanRequest::class, 'request_id');
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
