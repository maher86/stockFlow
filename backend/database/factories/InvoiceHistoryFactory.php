<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceHistory>
 */
class InvoiceHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'event' => fake()->randomElement(['created', 'updated', 'status_changed', 'note_added']),
            'payload' => [
                'message' => fake()->sentence(),
            ],
        ];
    }
}
