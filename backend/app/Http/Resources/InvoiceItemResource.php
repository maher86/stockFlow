<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'item_id' => $this->resource->item_id,
            'description' => $this->resource->description,
            'quantity' => $this->resource->quantity,
            'unit_price' => $this->resource->unit_price,
            'line_total' => $this->resource->line_total,
        ];
    }
}
