<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\ItemConditionValue;
use App\Enums\ItemType;
use App\Enums\PriceTier;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Package;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_overview_stats(): void
    {
        $this->authenticate();

        Supplier::factory()->create();
        Customer::factory()->create();
        Package::factory()->create();
        Item::factory()->create();
        Invoice::factory()->create([
            'status' => InvoiceStatus::Paid,
            'total' => 1000,
            'paid_amount' => 1000,
        ]);
        Invoice::factory()->create([
            'status' => InvoiceStatus::Pending,
            'total' => 500,
            'paid_amount' => 100,
        ]);
        Invoice::factory()->create([
            'status' => InvoiceStatus::Cancelled,
            'total' => 999,
            'paid_amount' => 0,
        ]);

        $response = $this->getJson('/api/v1/dashboard/overview');

        $response->assertOk()
            ->assertJsonPath('data.suppliers_count', 3)
            ->assertJsonPath('data.customers_count', 4)
            ->assertJsonPath('data.packages_count', 2)
            ->assertJsonPath('data.items_count', 1)
            ->assertJsonPath('data.invoices_count', 3)
            ->assertJsonPath('data.revenue_total', 1500)
            ->assertJsonPath('data.outstanding_total', 400);
    }

    public function test_authenticated_user_can_view_reports(): void
    {
        $this->authenticate();

        Invoice::factory()->create([
            'status' => InvoiceStatus::Paid,
            'issued_at' => '2026-05-01',
            'total' => 1000,
        ]);
        Invoice::factory()->create([
            'status' => InvoiceStatus::Pending,
            'issued_at' => '2026-05-15',
            'total' => 500,
        ]);
        Invoice::factory()->create([
            'status' => InvoiceStatus::Paid,
            'issued_at' => '2026-06-01',
            'total' => 250,
        ]);

        $jacket = Item::factory()->create([
            'type' => ItemType::Jacket,
            'quantity' => 3,
        ]);
        $shirt = Item::factory()->create([
            'type' => ItemType::Shirt,
            'quantity' => 7,
        ]);
        ItemCondition::factory()->create([
            'item_id' => $jacket->id,
            'condition' => ItemConditionValue::Perfect,
            'price_tier' => PriceTier::N3,
            'quantity' => 4,
        ]);
        ItemCondition::factory()->create([
            'item_id' => $shirt->id,
            'condition' => ItemConditionValue::Good,
            'price_tier' => PriceTier::N2,
            'quantity' => 6,
        ]);

        $response = $this->getJson('/api/v1/dashboard/reports');

        $response->assertOk()
            ->assertJsonPath('data.monthly.0.month', '2026-05')
            ->assertJsonPath('data.monthly.0.total', 1500)
            ->assertJsonPath('data.monthly.1.month', '2026-06')
            ->assertJsonPath('data.monthly.1.total', 250)
            ->assertJsonPath('data.conditions.0.quantity', 6)
            ->assertJsonPath('data.conditions.1.quantity', 4)
            ->assertJsonPath('data.types.0.quantity', 3)
            ->assertJsonPath('data.types.1.quantity', 7);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->getJson('/api/v1/dashboard/overview')->assertUnauthorized();
        $this->getJson('/api/v1/dashboard/reports')->assertUnauthorized();
    }

    private function authenticate(): void
    {
        Sanctum::actingAs($this->user);
    }
}
