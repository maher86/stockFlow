<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'package_id' => $this->resource->package_id,
            'package' => new PackageResource($this->whenLoaded('package')),
            'sku' => $this->resource->sku,
            'name' => $this->resource->name,
            'season' => $this->resource->season->value,
            'gender' => $this->resource->gender->value,
            'type' => $this->resource->type->value,
            'quantity' => $this->resource->quantity,
            'notes' => $this->resource->notes,
            'conditions' => ItemConditionResource::collection($this->whenLoaded('conditions')),
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}
