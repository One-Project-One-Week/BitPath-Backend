<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_finished' => $this->is_finished,
            'total_tasks' => $this->total_tasks,
            'completed_tasks' => $this->completed_tasks,
            'skill_name' =>  optional($this->planRequest)->roadmapSkill->skill,
            // ],
        ];
    }
}
