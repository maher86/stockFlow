<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceHistory;
use App\Models\InvoiceItem;
use App\Models\InvoiceNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_factory_creates_a_persisted_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::Draft,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => InvoiceStatus::Draft->value,
        ]);
    }

    public function test_invoice_belongs_to_customer(): void
    {
        $invoice = Invoice::factory()->create();

        $this->assertTrue($invoice->customer()->exists());
    }

    public function test_invoice_status_is_cast_to_enum(): void
    {
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::Pending,
        ]);

        $this->assertSame(InvoiceStatus::Pending, $invoice->status);
    }

    public function test_invoice_has_items_notes_and_history(): void
    {
        $invoice = Invoice::factory()->create();
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
        ]);
        InvoiceNote::factory()->create([
            'invoice_id' => $invoice->id,
        ]);
        InvoiceHistory::factory()->create([
            'invoice_id' => $invoice->id,
            'payload' => ['status' => InvoiceStatus::Draft->value],
        ]);

        $this->assertCount(1, $invoice->items);
        $this->assertCount(1, $invoice->invoiceNotes);
        $this->assertCount(1, $invoice->history);
        $this->assertSame(['status' => InvoiceStatus::Draft->value], $invoice->history->first()?->payload);
    }
}
