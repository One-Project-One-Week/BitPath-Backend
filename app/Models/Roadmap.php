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
        'user_id',
        'created_user_id',
        'visibility',
    ];

    // protected $with = ['roadmapSkills'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function roadmapSkills()
    {
        return $this->hasMany(RoadmapSkill::class, 'roadmap_id');
    }
}
