<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_customers(): void
    {
        $this->authenticate();

        Customer::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/customers');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_create_customer(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Mariam Ali',
            'phone' => '050-1112223',
            'email' => 'mariam@example.com',
            'location' => 'Dubai, UAE',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Mariam Ali')
            ->assertJsonPath('data.phone', '050-1112223')
            ->assertJsonPath('data.email', 'mariam@example.com')
            ->assertJsonPath('data.location', 'Dubai, UAE');

        $this->assertDatabaseHas('customers', [
            'name' => 'Mariam Ali',
        ]);
    }

    public function test_customer_creation_fails_without_name(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/customers', [
            'phone' => '050-1112223',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_customer_creation_fails_with_invalid_email(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Mariam Ali',
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_view_customer(): void
    {
        $this->authenticate();

        $customer = Customer::factory()->create();

        $response = $this->getJson('/api/v1/customers/'.$customer->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $customer->id)
            ->assertJsonPath('data.name', $customer->name);
    }

    public function test_authenticated_user_can_update_customer(): void
    {
        $this->authenticate();

        $customer = Customer::factory()->create([
            'name' => 'Old Customer',
            'email' => 'old@example.com',
        ]);

        $response = $this->putJson('/api/v1/customers/'.$customer->id, [
            'name' => 'Updated Customer',
            'phone' => null,
            'email' => null,
            'location' => 'Ajman, UAE',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Customer')
            ->assertJsonPath('data.phone', null)
            ->assertJsonPath('data.email', null)
            ->assertJsonPath('data.location', 'Ajman, UAE');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
            'email' => null,
        ]);
    }

    public function test_authenticated_user_can_delete_customer(): void
    {
        $this->authenticate();

        $customer = Customer::factory()->create();

        $response = $this->deleteJson('/api/v1/customers/'.$customer->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_guest_cannot_access_customers(): void
    {
        $this->getJson('/api/v1/customers')->assertUnauthorized();
    }

    private function authenticate(): void
    {
        Sanctum::actingAs($this->user);
    }
}
