<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'customer_id' => $this->resource->customer_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'invoice_number' => $this->resource->invoice_number,
            'status' => $this->resource->status->value,
            'issued_at' => $this->resource->issued_at?->toDateString(),
            'due_at' => $this->resource->due_at?->toDateString(),
            'subtotal' => $this->resource->subtotal,
            'discount' => $this->resource->discount,
            'total' => $this->resource->total,
            'paid_amount' => $this->resource->paid_amount,
            'notes' => $this->resource->notes,
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'invoice_notes' => InvoiceNoteResource::collection($this->whenLoaded('invoiceNotes')),
            'history' => InvoiceHistoryResource::collection($this->whenLoaded('history')),
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}
