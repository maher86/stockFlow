<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_invoices(): void
    {
        $this->authenticate();

        Invoice::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/invoices');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_create_invoice_with_items_and_totals(): void
    {
        $this->authenticate();

        $customer = Customer::factory()->create();
        $item = Item::factory()->create();

        $response = $this->postJson('/api/v1/invoices', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-1001',
            'issued_at' => '2026-05-25',
            'discount' => 50,
            'items' => [
                [
                    'item_id' => $item->id,
                    'description' => 'Blue Jacket',
                    'quantity' => 2,
                    'unit_price' => 300,
                ],
                [
                    'description' => 'White Shirt',
                    'quantity' => 1,
                    'unit_price' => 200,
                ],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.invoice_number', 'INV-1001')
            ->assertJsonPath('data.status', InvoiceStatus::Draft->value)
            ->assertJsonPath('data.subtotal', 800)
            ->assertJsonPath('data.total', 750)
            ->assertJsonPath('data.items.0.line_total', 600);

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-1001',
            'subtotal' => 800,
            'total' => 750,
        ]);
        $this->assertDatabaseHas('invoice_history', [
            'event' => 'created',
        ]);
    }

    public function test_invoice_creation_fails_without_customer(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/invoices', [
            'invoice_number' => 'INV-1001',
            'issued_at' => '2026-05-25',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['customer_id']);
    }

    public function test_authenticated_user_can_view_invoice(): void
    {
        $this->authenticate();

        $invoice = Invoice::factory()->create();

        $response = $this->getJson('/api/v1/invoices/'.$invoice->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $invoice->id)
            ->assertJsonPath('data.invoice_number', $invoice->invoice_number);
    }

    public function test_authenticated_user_can_update_invoice_and_recalculate_totals(): void
    {
        $this->authenticate();

        $invoice = Invoice::factory()->create([
            'discount' => 0,
        ]);

        $response = $this->putJson('/api/v1/invoices/'.$invoice->id, [
            'discount' => 100,
            'items' => [
                [
                    'description' => 'Blue Jacket',
                    'quantity' => 2,
                    'unit_price' => 300,
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.subtotal', 600)
            ->assertJsonPath('data.discount', 100)
            ->assertJsonPath('data.total', 500);

        $this->assertDatabaseHas('invoice_history', [
            'invoice_id' => $invoice->id,
            'event' => 'updated',
        ]);
    }

    public function test_authenticated_user_can_update_invoice_status(): void
    {
        $this->authenticate();

        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::Draft,
        ]);

        $response = $this->patchJson('/api/v1/invoices/'.$invoice->id.'/status', [
            'status' => InvoiceStatus::Paid->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', InvoiceStatus::Paid->value);

        $this->assertDatabaseHas('invoice_history', [
            'invoice_id' => $invoice->id,
            'event' => 'status_changed',
        ]);
    }

    public function test_authenticated_user_can_add_invoice_note(): void
    {
        $this->authenticate();

        $invoice = Invoice::factory()->create();

        $response = $this->postJson('/api/v1/invoices/'.$invoice->id.'/notes', [
            'body' => 'Customer requested delivery.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.body', 'Customer requested delivery.');

        $this->assertDatabaseHas('invoice_notes', [
            'invoice_id' => $invoice->id,
            'body' => 'Customer requested delivery.',
        ]);
    }

    public function test_authenticated_user_can_request_invoice_pdf_placeholder(): void
    {
        $this->authenticate();

        $invoice = Invoice::factory()->create();

        $response = $this->getJson('/api/v1/invoices/'.$invoice->id.'/pdf');

        $response->assertOk()
            ->assertJsonPath('data.invoice_id', $invoice->id)
            ->assertJsonPath('data.message', 'PDF generation placeholder.');
    }

    public function test_authenticated_user_can_delete_invoice(): void
    {
        $this->authenticate();

        $invoice = Invoice::factory()->create();

        $response = $this->deleteJson('/api/v1/invoices/'.$invoice->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }

    public function test_guest_cannot_access_invoices(): void
    {
        $this->getJson('/api/v1/invoices')->assertUnauthorized();
    }

    private function authenticate(): void
    {
        Sanctum::actingAs($this->user);
    }
}
