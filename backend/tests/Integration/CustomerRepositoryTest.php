<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CustomerRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(CustomerRepositoryInterface::class);
    }

    public function test_it_paginates_customers(): void
    {
        Customer::factory()->count(3)->create();

        $customers = $this->repository->paginate(2);

        $this->assertSame(3, $customers->total());
        $this->assertCount(2, $customers->items());
    }

    public function test_it_finds_a_customer_by_id(): void
    {
        $customer = Customer::factory()->create();

        $found = $this->repository->find($customer->id);

        $this->assertNotNull($found);
        $this->assertTrue($customer->is($found));
    }

    public function test_it_creates_a_customer(): void
    {
        $customer = $this->repository->create([
            'name' => 'Mariam Ali',
            'phone' => '050-1112223',
            'email' => 'mariam@example.com',
            'location' => 'Dubai, UAE',
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Mariam Ali',
        ]);
    }

    public function test_it_updates_a_customer(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Old Customer',
        ]);

        $updated = $this->repository->update($customer, [
            'name' => 'Updated Customer',
            'email' => null,
        ]);

        $this->assertSame('Updated Customer', $updated->name);
        $this->assertNull($updated->email);
    }

    public function test_it_deletes_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $deleted = $this->repository->delete($customer);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }
}
