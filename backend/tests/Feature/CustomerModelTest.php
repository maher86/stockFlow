<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_factory_creates_a_persisted_customer(): void
    {
        $customer = Customer::factory()->create();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => $customer->name,
        ]);
    }

    public function test_customer_allows_optional_contact_fields(): void
    {
        $customer = Customer::factory()->create([
            'phone' => null,
            'email' => null,
            'location' => null,
        ]);

        $this->assertNull($customer->phone);
        $this->assertNull($customer->email);
        $this->assertNull($customer->location);
    }
}
