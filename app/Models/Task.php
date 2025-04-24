<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'plan_id', 
        'is_finished',
        'day_number',
        'task',
        'topic'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
