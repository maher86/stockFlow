<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'supplier_id' => $this->resource->supplier_id,
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'reference' => $this->resource->reference,
            'name' => $this->resource->name,
            'total_items' => $this->resource->total_items,
            'status' => $this->resource->status->value,
            'received_at' => $this->resource->received_at?->toDateString(),
            'sorted_at' => $this->resource->sorted_at?->toISOString(),
            'notes' => $this->resource->notes,
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}
