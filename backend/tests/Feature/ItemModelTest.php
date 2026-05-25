<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\ItemConditionValue;
use App\Enums\ItemType;
use App\Enums\PriceTier;
use App\Enums\Season;
use App\Models\Item;
use App\Models\ItemCondition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_factory_creates_a_persisted_item(): void
    {
        $item = Item::factory()->create([
            'season' => Season::Summer,
            'gender' => Gender::Women,
            'type' => ItemType::Dress,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => $item->name,
            'season' => Season::Summer->value,
            'gender' => Gender::Women->value,
            'type' => ItemType::Dress->value,
        ]);
    }

    public function test_item_belongs_to_package(): void
    {
        $item = Item::factory()->create();

        $this->assertTrue($item->package()->exists());
    }

    public function test_item_enum_fields_are_cast(): void
    {
        $item = Item::factory()->create([
            'season' => Season::Winter,
            'gender' => Gender::Men,
            'type' => ItemType::Jacket,
        ]);

        $this->assertSame(Season::Winter, $item->season);
        $this->assertSame(Gender::Men, $item->gender);
        $this->assertSame(ItemType::Jacket, $item->type);
    }

    public function test_item_condition_factory_creates_a_persisted_condition(): void
    {
        $condition = ItemCondition::factory()->create([
            'condition' => ItemConditionValue::Perfect,
            'price_tier' => PriceTier::N3,
        ]);

        $this->assertDatabaseHas('item_conditions', [
            'id' => $condition->id,
            'condition' => ItemConditionValue::Perfect->value,
            'price_tier' => PriceTier::N3->value,
        ]);
    }

    public function test_item_has_conditions(): void
    {
        $item = Item::factory()->create();
        ItemCondition::factory()->create([
            'item_id' => $item->id,
        ]);

        $this->assertCount(1, $item->conditions);
    }
}
