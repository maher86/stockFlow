<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(1000, 10000);
        $discount = fake()->numberBetween(0, 500);
        $total = max(0, $subtotal - $discount);

        return [
            'customer_id' => Customer::factory(),
            'invoice_number' => fake()->unique()->bothify('INV-####'),
            'status' => InvoiceStatus::Draft,
            'issued_at' => fake()->dateTimeBetween('-30 days')->format('Y-m-d'),
            'due_at' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'paid_amount' => 0,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => InvoiceStatus::Paid,
            'paid_amount' => $attributes['total'] ?? 0,
        ]);
    }
}
