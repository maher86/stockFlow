<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Models\InvoiceHistory;
use App\Models\InvoiceNote;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    /** @return LengthAwarePaginator<Invoice> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::query()
            ->with(['customer', 'items', 'invoiceNotes', 'history'])
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Invoice
    {
        return Invoice::query()
            ->with(['customer', 'items', 'invoiceNotes', 'history'])
            ->find($id);
    }

    /**
     * @param  array{customer_id: int, invoice_number: string, status?: string, issued_at: string, due_at?: string|null, subtotal?: int, discount?: int, total?: int, paid_amount?: int, notes?: string|null}  $data
     */
    public function create(array $data): Invoice
    {
        return Invoice::query()->create($data)->load(['customer', 'items', 'invoiceNotes', 'history']);
    }

    /**
     * @param  array{customer_id?: int, invoice_number?: string, status?: string, issued_at?: string, due_at?: string|null, subtotal?: int, discount?: int, total?: int, paid_amount?: int, notes?: string|null}  $data
     */
    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);

        return $invoice->refresh()->load(['customer', 'items', 'invoiceNotes', 'history']);
    }

    public function delete(Invoice $invoice): bool
    {
        return (bool) $invoice->delete();
    }

    /**
     * @param  list<array{item_id?: int|null, description: string, quantity: int, unit_price: int, line_total: int}>  $items
     */
    public function syncItems(Invoice $invoice, array $items): Invoice
    {
        DB::transaction(function () use ($invoice, $items): void {
            $invoice->items()->delete();
            $invoice->items()->createMany($items);
        });

        return $invoice->refresh()->load(['customer', 'items', 'invoiceNotes', 'history']);
    }

    public function addNote(Invoice $invoice, string $body): InvoiceNote
    {
        return $invoice->invoiceNotes()->create([
            'body' => $body,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public function addHistory(Invoice $invoice, string $event, ?array $payload = null): InvoiceHistory
    {
        return $invoice->history()->create([
            'event' => $event,
            'payload' => $payload,
        ]);
    }
}
