<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceNote;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoices,
    ) {}

    /** @return LengthAwarePaginator<Invoice> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->invoices->paginate($perPage);
    }

    public function find(int $id): ?Invoice
    {
        return $this->invoices->find($id);
    }

    public function findOrFail(int $id): Invoice
    {
        $invoice = $this->find($id);

        if (! $invoice instanceof Invoice) {
            throw new ModelNotFoundException('Invoice not found.');
        }

        return $invoice;
    }

    /**
     * @param  array{customer_id: int, invoice_number: string, status?: string, issued_at: string, due_at?: string|null, discount?: int, paid_amount?: int, notes?: string|null, items?: list<array{item_id?: int|null, description: string, quantity: int, unit_price: int}>}  $data
     */
    public function create(array $data): Invoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $totals = $this->calculateTotals($items, (int) ($data['discount'] ?? 0));
        $invoice = $this->invoices->create(array_merge($data, $totals, [
            'status' => $data['status'] ?? InvoiceStatus::Draft->value,
            'paid_amount' => (int) ($data['paid_amount'] ?? 0),
        ]));

        if ($items !== []) {
            $invoice = $this->invoices->syncItems($invoice, $this->prepareItems($items));
        }

        $this->invoices->addHistory($invoice, 'created', [
            'status' => $invoice->status->value,
            'total' => $invoice->total,
        ]);

        return $invoice;
    }

    /**
     * @param  array{customer_id?: int, invoice_number?: string, status?: string, issued_at?: string, due_at?: string|null, discount?: int, paid_amount?: int, notes?: string|null, items?: list<array{item_id?: int|null, description: string, quantity: int, unit_price: int}>}  $data
     */
    public function update(Invoice $invoice, array $data): Invoice
    {
        $items = $data['items'] ?? null;
        unset($data['items']);

        if (is_array($items)) {
            $data = array_merge($data, $this->calculateTotals($items, (int) ($data['discount'] ?? $invoice->discount)));
        }

        $updatedInvoice = $this->invoices->update($invoice, $data);

        if (is_array($items)) {
            $updatedInvoice = $this->invoices->syncItems($updatedInvoice, $this->prepareItems($items));
        }

        $this->invoices->addHistory($updatedInvoice, 'updated', [
            'total' => $updatedInvoice->total,
        ]);

        return $updatedInvoice;
    }

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice
    {
        $updatedInvoice = $this->invoices->update($invoice, [
            'status' => $status->value,
        ]);

        $this->invoices->addHistory($updatedInvoice, 'status_changed', [
            'from' => $invoice->status->value,
            'to' => $status->value,
        ]);

        return $updatedInvoice;
    }

    public function addNote(Invoice $invoice, string $body): InvoiceNote
    {
        $note = $this->invoices->addNote($invoice, $body);

        $this->invoices->addHistory($invoice, 'note_added', [
            'note_id' => $note->id,
        ]);

        return $note;
    }

    public function delete(Invoice $invoice): bool
    {
        return $this->invoices->delete($invoice);
    }

    /**
     * @param  list<array{item_id?: int|null, description: string, quantity: int, unit_price: int}>  $items
     * @return array{subtotal: int, discount: int, total: int}
     */
    private function calculateTotals(array $items, int $discount): array
    {
        $subtotal = array_sum(array_map(
            static fn (array $item): int => $item['quantity'] * $item['unit_price'],
            $items
        ));

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ];
    }

    /**
     * @param  list<array{item_id?: int|null, description: string, quantity: int, unit_price: int}>  $items
     * @return list<array{item_id?: int|null, description: string, quantity: int, unit_price: int, line_total: int}>
     */
    private function prepareItems(array $items): array
    {
        return array_map(static fn (array $item): array => [
            'item_id' => $item['item_id'] ?? null,
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'line_total' => $item['quantity'] * $item['unit_price'],
        ], $items);
    }
}
