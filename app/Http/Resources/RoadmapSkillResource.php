<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoadmapSkillResource extends JsonResource
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
            'roadmap_id' => $this->roadmap_id,
            'skill' => $this->skill,
            'why' => $this->why,
            'duration' => $this->duration,
            'level' => $this->level,
            'recommendedResource' => new RecommandedResourceResource($this->whenLoaded('recommandResource')),
        ];
    }
}
