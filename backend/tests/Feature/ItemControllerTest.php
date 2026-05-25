<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\ItemConditionValue;
use App\Enums\ItemType;
use App\Enums\PriceTier;
use App\Enums\Season;
use App\Models\Item;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_items(): void
    {
        $this->authenticate();

        Item::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/items');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_create_item_with_conditions(): void
    {
        $this->authenticate();

        $package = Package::factory()->create();

        $response = $this->postJson('/api/v1/items', [
            'package_id' => $package->id,
            'sku' => 'ITM-1001',
            'name' => 'Blue Jacket',
            'season' => Season::Winter->value,
            'gender' => Gender::Men->value,
            'type' => ItemType::Jacket->value,
            'quantity' => 12,
            'conditions' => [
                [
                    'condition' => ItemConditionValue::Perfect->value,
                    'price_tier' => PriceTier::N3->value,
                    'quantity' => 5,
                ],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.package_id', $package->id)
            ->assertJsonPath('data.name', 'Blue Jacket')
            ->assertJsonPath('data.season', Season::Winter->value)
            ->assertJsonPath('data.conditions.0.condition', ItemConditionValue::Perfect->value)
            ->assertJsonPath('data.conditions.0.price_tier', PriceTier::N3->value);

        $this->assertDatabaseHas('items', [
            'package_id' => $package->id,
            'name' => 'Blue Jacket',
        ]);
        $this->assertDatabaseHas('item_conditions', [
            'condition' => ItemConditionValue::Perfect->value,
            'price_tier' => PriceTier::N3->value,
            'quantity' => 5,
        ]);
    }

    public function test_item_creation_fails_without_package(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/v1/items', [
            'name' => 'Blue Jacket',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['package_id']);
    }

    public function test_authenticated_user_can_view_item(): void
    {
        $this->authenticate();

        $item = Item::factory()->create();

        $response = $this->getJson('/api/v1/items/'.$item->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $item->id)
            ->assertJsonPath('data.name', $item->name);
    }

    public function test_authenticated_user_can_update_item_and_conditions(): void
    {
        $this->authenticate();

        $item = Item::factory()->create([
            'name' => 'Old Item',
            'quantity' => 10,
        ]);

        $response = $this->patchJson('/api/v1/items/'.$item->id, [
            'name' => 'Updated Item',
            'quantity' => 20,
            'conditions' => [
                [
                    'condition' => ItemConditionValue::Good->value,
                    'price_tier' => PriceTier::N2->value,
                    'quantity' => 8,
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Item')
            ->assertJsonPath('data.quantity', 20)
            ->assertJsonPath('data.conditions.0.condition', ItemConditionValue::Good->value);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Updated Item',
            'quantity' => 20,
        ]);
        $this->assertDatabaseHas('item_conditions', [
            'item_id' => $item->id,
            'condition' => ItemConditionValue::Good->value,
            'price_tier' => PriceTier::N2->value,
            'quantity' => 8,
        ]);
    }

    public function test_authenticated_user_can_bulk_update_items(): void
    {
        $this->authenticate();

        $first = Item::factory()->create([
            'name' => 'First Item',
        ]);
        $second = Item::factory()->create([
            'name' => 'Second Item',
        ]);

        $response = $this->postJson('/api/v1/items/bulk-update', [
            'items' => [
                [
                    'id' => $first->id,
                    'name' => 'Updated First',
                    'quantity' => 3,
                ],
                [
                    'id' => $second->id,
                    'name' => 'Updated Second',
                    'quantity' => 4,
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Updated First')
            ->assertJsonPath('data.1.name', 'Updated Second');

        $this->assertDatabaseHas('items', [
            'id' => $first->id,
            'name' => 'Updated First',
            'quantity' => 3,
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $second->id,
            'name' => 'Updated Second',
            'quantity' => 4,
        ]);
    }

    public function test_authenticated_user_can_delete_item(): void
    {
        $this->authenticate();

        $item = Item::factory()->create();

        $response = $this->deleteJson('/api/v1/items/'.$item->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);
    }

    public function test_guest_cannot_access_items(): void
    {
        $this->getJson('/api/v1/items')->assertUnauthorized();
    }

    private function authenticate(): void
    {
        Sanctum::actingAs($this->user);
    }
}
