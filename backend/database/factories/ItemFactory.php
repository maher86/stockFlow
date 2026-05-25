<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\ItemType;
use App\Enums\Season;
use App\Models\Item;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'package_id' => Package::factory(),
            'sku' => fake()->optional()->bothify('ITM-####'),
            'name' => fake()->words(2, true),
            'season' => fake()->randomElement(Season::cases()),
            'gender' => fake()->randomElement(Gender::cases()),
            'type' => fake()->randomElement(ItemType::cases()),
            'quantity' => fake()->numberBetween(1, 50),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
