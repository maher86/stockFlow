<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PackageStatus;
use App\Models\Package;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PackageControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_packages(): void
    {
        $this->authenticate();

        Package::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/packages');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_create_package(): void
    {
        $this->authenticate();

        $supplier = Supplier::factory()->create();

        $response = $this->postJson('/api/v1/packages', [
            'supplier_id' => $supplier->id,
            'reference' => 'PKG-1001',
            'name' => 'Summer Bale',
            'total_items' => 120,
            'received_at' => '2026-05-25',
            'notes' => 'Received at warehouse gate.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.supplier_id', $supplier->id)
            ->assertJsonPath('data.reference', 'PKG-1001')
            ->assertJsonPath('data.name', 'Summer Bale')
            ->assertJsonPath('data.total_items', 120)
            ->assertJsonPath('data.status', PackageStatus::Unsorted->value);

        $this->assertDatabaseHas('packages', [
            'supplier_id' => $supplier->id,
            'name' => 'Summer Bale',
            'status' => PackageStatus::Unsorted->value,
        ]);
    }

    public function test_package_creation_fails_without_supplier(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/packages', [
            'name' => 'Summer Bale',
            'received_at' => '2026-05-25',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['supplier_id']);
    }

    public function test_authenticated_user_can_view_package(): void
    {
        $this->authenticate();

        $package = Package::factory()->create();

        $response = $this->getJson('/api/v1/packages/'.$package->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $package->id)
            ->assertJsonPath('data.name', $package->name);
    }

    public function test_authenticated_user_can_update_package(): void
    {
        $this->authenticate();

        $package = Package::factory()->create([
            'name' => 'Old Package',
            'total_items' => 50,
        ]);

        $response = $this->patchJson('/api/v1/packages/'.$package->id, [
            'name' => 'Updated Package',
            'total_items' => 200,
            'notes' => null,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Package')
            ->assertJsonPath('data.total_items', 200)
            ->assertJsonPath('data.notes', null);

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => 'Updated Package',
            'total_items' => 200,
        ]);
    }

    public function test_authenticated_user_can_sort_unsorted_package(): void
    {
        $this->authenticate();

        $package = Package::factory()->create([
            'status' => PackageStatus::Unsorted,
            'sorted_at' => null,
        ]);

        $response = $this->postJson('/api/v1/packages/'.$package->id.'/sort');

        $response->assertOk()
            ->assertJsonPath('data.status', PackageStatus::Sorted->value);

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'status' => PackageStatus::Sorted->value,
        ]);

        $this->assertNotNull($package->refresh()->sorted_at);
    }

    public function test_sorting_package_fails_when_not_unsorted(): void
    {
        $this->authenticate();

        $package = Package::factory()->sorted()->create();

        $response = $this->postJson('/api/v1/packages/'.$package->id.'/sort');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_guest_cannot_access_packages(): void
    {
        $this->getJson('/api/v1/packages')->assertUnauthorized();
    }

    private function authenticate(): void
    {
        Sanctum::actingAs($this->user);
    }
}
