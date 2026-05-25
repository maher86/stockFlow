<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Enums\Gender;
use App\Enums\ItemConditionValue;
use App\Enums\ItemType;
use App\Enums\PriceTier;
use App\Enums\Season;
use App\Models\Item;
use App\Models\Package;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ItemRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(ItemRepositoryInterface::class);
    }

    public function test_it_paginates_items(): void
    {
        Item::factory()->count(3)->create();

        $items = $this->repository->paginate(2);

        $this->assertSame(3, $items->total());
        $this->assertCount(2, $items->items());
    }

    public function test_it_finds_an_item_by_id(): void
    {
        $item = Item::factory()->create();

        $found = $this->repository->find($item->id);

        $this->assertNotNull($found);
        $this->assertTrue($item->is($found));
    }

    public function test_it_creates_an_item(): void
    {
        $package = Package::factory()->create();

        $item = $this->repository->create([
            'package_id' => $package->id,
            'sku' => 'ITM-1001',
            'name' => 'Blue Jacket',
            'season' => Season::Winter->value,
            'gender' => Gender::Men->value,
            'type' => ItemType::Jacket->value,
            'quantity' => 12,
            'notes' => null,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Blue Jacket',
        ]);
    }

    public function test_it_updates_an_item(): void
    {
        $item = Item::factory()->create([
            'name' => 'Old Item',
        ]);

        $updated = $this->repository->update($item, [
            'name' => 'Updated Item',
            'quantity' => 20,
        ]);

        $this->assertSame('Updated Item', $updated->name);
        $this->assertSame(20, $updated->quantity);
    }

    public function test_it_syncs_item_conditions(): void
    {
        $item = Item::factory()->create();

        $updated = $this->repository->syncConditions($item, [
            [
                'condition' => ItemConditionValue::Perfect->value,
                'price_tier' => PriceTier::N3->value,
                'quantity' => 5,
            ],
        ]);

        $this->assertCount(1, $updated->conditions);
        $this->assertDatabaseHas('item_conditions', [
            'item_id' => $item->id,
            'condition' => ItemConditionValue::Perfect->value,
            'price_tier' => PriceTier::N3->value,
            'quantity' => 5,
        ]);
    }

    public function test_it_deletes_an_item(): void
    {
        $item = Item::factory()->create();

        $deleted = $this->repository->delete($item);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);
    }
}
