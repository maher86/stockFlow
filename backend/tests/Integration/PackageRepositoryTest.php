<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Enums\PackageStatus;
use App\Models\Package;
use App\Models\Supplier;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PackageRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(PackageRepositoryInterface::class);
    }

    public function test_it_paginates_packages(): void
    {
        Package::factory()->count(3)->create();

        $packages = $this->repository->paginate(2);

        $this->assertSame(3, $packages->total());
        $this->assertCount(2, $packages->items());
    }

    public function test_it_finds_a_package_by_id(): void
    {
        $package = Package::factory()->create();

        $found = $this->repository->find($package->id);

        $this->assertNotNull($found);
        $this->assertTrue($package->is($found));
    }

    public function test_it_creates_a_package(): void
    {
        $supplier = Supplier::factory()->create();

        $package = $this->repository->create([
            'supplier_id' => $supplier->id,
            'reference' => 'PKG-1001',
            'name' => 'Summer Bale',
            'total_items' => 120,
            'status' => PackageStatus::Unsorted->value,
            'received_at' => '2026-05-25',
            'notes' => null,
        ]);

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'supplier_id' => $supplier->id,
            'name' => 'Summer Bale',
        ]);
    }

    public function test_it_updates_a_package(): void
    {
        $package = Package::factory()->create([
            'name' => 'Old Package',
        ]);

        $updated = $this->repository->update($package, [
            'name' => 'Updated Package',
            'total_items' => 200,
        ]);

        $this->assertSame('Updated Package', $updated->name);
        $this->assertSame(200, $updated->total_items);
    }
}
