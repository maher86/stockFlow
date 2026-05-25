<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceNote>
 */
class InvoiceNoteFactory extends Factory
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
            'body' => fake()->sentence(),
        ];
    }
}
