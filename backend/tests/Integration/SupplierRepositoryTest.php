<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SupplierRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(SupplierRepositoryInterface::class);
    }

    public function test_it_paginates_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $suppliers = $this->repository->paginate(2);

        $this->assertSame(3, $suppliers->total());
        $this->assertCount(2, $suppliers->items());
    }

    public function test_it_finds_a_supplier_by_id(): void
    {
        $supplier = Supplier::factory()->create();

        $found = $this->repository->find($supplier->id);

        $this->assertNotNull($found);
        $this->assertTrue($supplier->is($found));
    }

    public function test_it_creates_a_supplier(): void
    {
        $supplier = $this->repository->create([
            'name' => 'Dubai Textile Supply',
            'phone' => '050-1234567',
            'location' => 'Dubai, UAE',
        ]);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Dubai Textile Supply',
        ]);
    }

    public function test_it_updates_a_supplier(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'Old Supplier',
        ]);

        $updated = $this->repository->update($supplier, [
            'name' => 'Updated Supplier',
            'phone' => null,
        ]);

        $this->assertSame('Updated Supplier', $updated->name);
        $this->assertNull($updated->phone);
    }

    public function test_it_deletes_a_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $deleted = $this->repository->delete($supplier);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }
}
