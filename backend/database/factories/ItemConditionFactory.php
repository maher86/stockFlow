<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ItemConditionValue;
use App\Enums\PriceTier;
use App\Models\Item;
use App\Models\ItemCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemCondition>
 */
class ItemConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'condition' => fake()->randomElement(ItemConditionValue::cases()),
            'price_tier' => fake()->randomElement(PriceTier::cases()),
            'quantity' => fake()->numberBetween(1, 20),
        ];
    }
}
