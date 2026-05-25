<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\PackageStatus;
use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Services\PackageService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PackageServiceTest extends TestCase
{
    private FakePackageRepository $repository;

    private PackageService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new FakePackageRepository;
        $this->service = new PackageService($this->repository);
    }

    public function test_it_paginates_packages(): void
    {
        $paginator = new LengthAwarePaginator([], 3, 10);
        $this->repository->paginator = $paginator;

        $this->assertSame($paginator, $this->service->paginate(10));
        $this->assertSame(10, $this->repository->lastPerPage);
    }

    public function test_it_finds_a_package_or_fails(): void
    {
        $package = $this->makePackage();
        $this->repository->package = $package;

        $this->assertSame($package, $this->service->findOrFail(5));
        $this->assertSame(5, $this->repository->lastId);
    }

    public function test_it_throws_when_package_is_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(5);
    }

    public function test_it_creates_packages_as_unsorted(): void
    {
        $data = [
            'supplier_id' => 1,
            'reference' => 'PKG-1001',
            'name' => 'Summer Bale',
            'total_items' => 120,
            'status' => PackageStatus::Complete->value,
            'received_at' => '2026-05-25',
        ];
        $package = $this->makePackage([
            'status' => PackageStatus::Unsorted,
        ]);
        $this->repository->package = $package;

        $this->assertSame($package, $this->service->create($data));
        $this->assertSame(PackageStatus::Unsorted->value, $this->repository->lastData['status']);
        $this->assertNull($this->repository->lastData['sorted_at']);
    }

    public function test_it_updates_package_without_status_fields(): void
    {
        $package = $this->makePackage();
        $data = [
            'name' => 'Updated Package',
            'status' => PackageStatus::Complete->value,
            'sorted_at' => '2026-05-25 10:00:00',
        ];
        $updated = $this->makePackage([
            'name' => 'Updated Package',
        ]);
        $this->repository->package = $updated;

        $this->assertSame($updated, $this->service->update($package, $data));
        $this->assertArrayNotHasKey('status', $this->repository->lastData);
        $this->assertArrayNotHasKey('sorted_at', $this->repository->lastData);
    }

    public function test_it_sorts_an_unsorted_package(): void
    {
        $package = $this->makePackage([
            'status' => PackageStatus::Unsorted,
        ]);
        $sorted = $this->makePackage([
            'status' => PackageStatus::Sorted,
        ]);
        $this->repository->package = $sorted;

        $this->assertSame($sorted, $this->service->sort($package));
        $this->assertSame(PackageStatus::Sorted->value, $this->repository->lastData['status']);
        $this->assertArrayHasKey('sorted_at', $this->repository->lastData);
    }

    public function test_it_rejects_sorting_a_package_that_is_not_unsorted(): void
    {
        $package = $this->makePackage([
            'status' => PackageStatus::Sorted,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->sort($package);
    }

    /**
     * @param  array<string, int|string|PackageStatus|null>  $attributes
     */
    private function makePackage(array $attributes = []): Package
    {
        return new Package(array_merge([
            'supplier_id' => 1,
            'reference' => 'PKG-1001',
            'name' => 'Summer Bale',
            'total_items' => 120,
            'status' => PackageStatus::Unsorted,
            'received_at' => '2026-05-25',
            'sorted_at' => null,
            'notes' => null,
        ], $attributes));
    }
}

final class FakePackageRepository implements PackageRepositoryInterface
{
    public ?Package $package = null;

    public ?Package $lastPackage = null;

    /** @var array<string, int|string|null> */
    public array $lastData = [];

    public ?int $lastId = null;

    public ?int $lastPerPage = null;

    private LengthAwarePaginatorContract $fallbackPaginator;

    public LengthAwarePaginatorContract $paginator;

    public function __construct()
    {
        $this->fallbackPaginator = new LengthAwarePaginator([], 0, 15);
        $this->paginator = $this->fallbackPaginator;
    }

    /** @return LengthAwarePaginatorContract<Package> */
    public function paginate(int $perPage = 15): LengthAwarePaginatorContract
    {
        $this->lastPerPage = $perPage;

        return $this->paginator;
    }

    public function find(int $id): ?Package
    {
        $this->lastId = $id;

        return $this->package;
    }

    /**
     * @param  array{supplier_id: int, reference?: string|null, name: string, total_items?: int, status?: string, received_at: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function create(array $data): Package
    {
        $this->lastData = $data;

        return $this->package ?? new Package($data);
    }

    /**
     * @param  array{supplier_id?: int, reference?: string|null, name?: string, total_items?: int, status?: string, received_at?: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function update(Package $package, array $data): Package
    {
        $this->lastPackage = $package;
        $this->lastData = $data;

        return $this->package ?? $package;
    }
}
