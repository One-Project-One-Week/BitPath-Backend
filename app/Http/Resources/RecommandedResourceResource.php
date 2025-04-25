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
         return $this->whenLoaded('resourceLinks', function () {
            return ResourceLinkResource::collection($this->resourceLinks)->resolve();
        }, []);
    }
}
