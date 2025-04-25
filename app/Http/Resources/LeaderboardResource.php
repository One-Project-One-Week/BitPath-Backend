<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
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
            'name' => $this->name,
            'longest_streak' => $this->longest_streak,
            'current_streak' => $this->current_streak,
            'last_studied_date' => $this->last_studied_date,
            'profile_picture' => $this->profile_picture,
            'roadmap_count' => $this->roadmaps->count(),
        ];
    }
}
