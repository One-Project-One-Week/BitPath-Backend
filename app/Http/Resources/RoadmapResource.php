<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoadmapResource extends JsonResource
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
            'prompt' => $this->prompt,
            'title' => $this->title,
            // 'user_id' => $this->user_id,
            'created_user_id' => $this->created_user_id,
            'visibility' => $this->visibility,
            'skills' => RoadmapSkillResource::collection($this->whenLoaded('roadmapSkills')),
        ];
    }
}
