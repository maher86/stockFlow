<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemConditionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'condition' => $this->resource->condition->value,
            'price_tier' => $this->resource->price_tier->value,
            'quantity' => $this->resource->quantity,
        ];
    }
}
