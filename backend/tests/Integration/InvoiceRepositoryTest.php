<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(InvoiceRepositoryInterface::class);
    }

    public function test_it_paginates_invoices(): void
    {
        Invoice::factory()->count(3)->create();

        $invoices = $this->repository->paginate(2);

        $this->assertSame(3, $invoices->total());
        $this->assertCount(2, $invoices->items());
    }

    public function test_it_creates_and_updates_invoice(): void
    {
        $customer = Customer::factory()->create();

        $invoice = $this->repository->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-1001',
            'status' => InvoiceStatus::Draft->value,
            'issued_at' => '2026-05-25',
            'due_at' => null,
            'subtotal' => 1000,
            'discount' => 100,
            'total' => 900,
            'paid_amount' => 0,
            'notes' => null,
        ]);

        $updated = $this->repository->update($invoice, [
            'status' => InvoiceStatus::Pending->value,
            'paid_amount' => 100,
        ]);

        $this->assertSame(InvoiceStatus::Pending, $updated->status);
        $this->assertSame(100, $updated->paid_amount);
    }

    public function test_it_syncs_items_and_records_notes_and_history(): void
    {
        $invoice = Invoice::factory()->create();
        $item = Item::factory()->create();

        $updated = $this->repository->syncItems($invoice, [
            [
                'item_id' => $item->id,
                'description' => 'Blue Jacket',
                'quantity' => 2,
                'unit_price' => 300,
                'line_total' => 600,
            ],
        ]);
        $note = $this->repository->addNote($updated, 'Customer requested delivery.');
        $history = $this->repository->addHistory($updated, 'updated', ['total' => 600]);

        $this->assertCount(1, $updated->items);
        $this->assertSame('Customer requested delivery.', $note->body);
        $this->assertSame(['total' => 600], $history->payload);
    }

    public function test_it_deletes_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $deleted = $this->repository->delete($invoice);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }
}
