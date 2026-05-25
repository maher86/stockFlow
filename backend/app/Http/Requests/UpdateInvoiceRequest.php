<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $invoiceId = $this->route('invoice');

        return [
            'customer_id' => ['sometimes', 'integer', 'exists:customers,id'],
            'invoice_number' => ['sometimes', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($invoiceId)],
            'issued_at' => ['sometimes', 'date'],
            'due_at' => ['sometimes', 'nullable', 'date'],
            'discount' => ['sometimes', 'integer', 'min:0'],
            'paid_amount' => ['sometimes', 'integer', 'min:0'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'items' => ['sometimes', 'array'],
            'items.*.item_id' => ['sometimes', 'nullable', 'integer', 'exists:items,id'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'integer', 'min:0'],
        ];
    }
}
