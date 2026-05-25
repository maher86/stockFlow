<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_factory_creates_a_persisted_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => $supplier->name,
        ]);
    }

    public function test_supplier_allows_optional_phone_and_location(): void
    {
        $supplier = Supplier::factory()->create([
            'phone' => null,
            'location' => null,
        ]);

        $this->assertNull($supplier->phone);
        $this->assertNull($supplier->location);
    }
}
