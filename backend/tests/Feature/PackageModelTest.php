<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_factory_creates_a_persisted_package(): void
    {
        $package = Package::factory()->create();

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => $package->name,
            'status' => PackageStatus::Unsorted->value,
        ]);
    }

    public function test_package_belongs_to_supplier(): void
    {
        $package = Package::factory()->create();

        $this->assertTrue($package->supplier()->exists());
    }

    public function test_package_status_is_cast_to_enum(): void
    {
        $package = Package::factory()->create();

        $this->assertSame(PackageStatus::Unsorted, $package->status);
    }
}
