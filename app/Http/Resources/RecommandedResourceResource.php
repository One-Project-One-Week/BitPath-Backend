<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecommandedResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            // 'skill_id' => $this->skill_id,
            'links' => ResourceLinkResource::collection($this->whenLoaded('resourceLinks')),
        ];
    }
}
