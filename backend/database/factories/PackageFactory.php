<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PackageStatus;
use App\Models\Package;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'reference' => fake()->bothify('PKG-####'),
            'name' => fake()->words(3, true),
            'total_items' => fake()->numberBetween(0, 500),
            'status' => PackageStatus::Unsorted,
            'received_at' => fake()->dateTimeBetween('-30 days')->format('Y-m-d'),
            'sorted_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function sorted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PackageStatus::Sorted,
            'sorted_at' => now(),
        ]);
    }
}
