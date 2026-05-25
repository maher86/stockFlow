<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_suppliers(): void
    {
        $this->authenticate();

        Supplier::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/suppliers');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_create_supplier(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/suppliers', [
            'name' => 'Dubai Textile Supply',
            'phone' => '050-1234567',
            'location' => 'Dubai, UAE',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Dubai Textile Supply')
            ->assertJsonPath('data.phone', '050-1234567')
            ->assertJsonPath('data.location', 'Dubai, UAE');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Dubai Textile Supply',
        ]);
    }

    public function test_supplier_creation_fails_without_name(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/suppliers', [
            'phone' => '050-1234567',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_authenticated_user_can_view_supplier(): void
    {
        $this->authenticate();

        $supplier = Supplier::factory()->create();

        $response = $this->getJson('/api/v1/suppliers/'.$supplier->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $supplier->id)
            ->assertJsonPath('data.name', $supplier->name);
    }

    public function test_authenticated_user_can_update_supplier(): void
    {
        $this->authenticate();

        $supplier = Supplier::factory()->create([
            'name' => 'Old Supplier',
            'phone' => '050-1111111',
        ]);

        $response = $this->putJson('/api/v1/suppliers/'.$supplier->id, [
            'name' => 'Updated Supplier',
            'phone' => null,
            'location' => 'Ajman, UAE',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Supplier')
            ->assertJsonPath('data.phone', null)
            ->assertJsonPath('data.location', 'Ajman, UAE');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Updated Supplier',
            'phone' => null,
        ]);
    }

    public function test_authenticated_user_can_delete_supplier(): void
    {
        $this->authenticate();

        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson('/api/v1/suppliers/'.$supplier->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    public function test_guest_cannot_access_suppliers(): void
    {
        $this->getJson('/api/v1/suppliers')->assertUnauthorized();
    }

    private function authenticate(): void
    {
        Sanctum::actingAs($this->user);
    }
}
