<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use App\Models\InvoiceHistory;
use App\Models\InvoiceNote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
    /** @return LengthAwarePaginator<Invoice> */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Invoice;

    /**
     * @param  array{customer_id: int, invoice_number: string, status?: string, issued_at: string, due_at?: string|null, subtotal?: int, discount?: int, total?: int, paid_amount?: int, notes?: string|null}  $data
     */
    public function create(array $data): Invoice;

    /**
     * @param  array{customer_id?: int, invoice_number?: string, status?: string, issued_at?: string, due_at?: string|null, subtotal?: int, discount?: int, total?: int, paid_amount?: int, notes?: string|null}  $data
     */
    public function update(Invoice $invoice, array $data): Invoice;

    public function delete(Invoice $invoice): bool;

    /**
     * @param  list<array{item_id?: int|null, description: string, quantity: int, unit_price: int, line_total: int}>  $items
     */
    public function syncItems(Invoice $invoice, array $items): Invoice;

    public function addNote(Invoice $invoice, string $body): InvoiceNote;

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public function addHistory(Invoice $invoice, string $event, ?array $payload = null): InvoiceHistory;
}
