<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'email' => $this->email,
            'longest_streak' => $this->longest_streak,
            'profile_picture' => $this->profile_picture,
            'current_streak' => $this->current_streak,
            'last_studied_date' => $this->last_studied_date,
        ];
    }
}
