<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\SupplierService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class SupplierServiceTest extends TestCase
{
    private FakeSupplierRepository $repository;

    private SupplierService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new FakeSupplierRepository;
        $this->service = new SupplierService($this->repository);
    }

    public function test_it_paginates_suppliers(): void
    {
        $paginator = new LengthAwarePaginator([], 3, 10);
        $this->repository->paginator = $paginator;

        $this->assertSame($paginator, $this->service->paginate(10));
        $this->assertSame(10, $this->repository->lastPerPage);
    }

    public function test_it_finds_a_supplier(): void
    {
        $supplier = Supplier::factory()->make();
        $this->repository->supplier = $supplier;

        $this->assertSame($supplier, $this->service->find(5));
        $this->assertSame(5, $this->repository->lastId);
    }

    public function test_it_finds_a_supplier_or_fails(): void
    {
        $supplier = Supplier::factory()->make();
        $this->repository->supplier = $supplier;

        $this->assertSame($supplier, $this->service->findOrFail(5));
        $this->assertSame(5, $this->repository->lastId);
    }

    public function test_it_throws_when_supplier_is_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(5);
    }

    public function test_it_creates_a_supplier(): void
    {
        $data = [
            'name' => 'Sharjah Clothing Supply',
            'phone' => '050-7654321',
            'location' => 'Sharjah, UAE',
        ];
        $supplier = Supplier::factory()->make($data);
        $this->repository->supplier = $supplier;

        $this->assertSame($supplier, $this->service->create($data));
        $this->assertSame($data, $this->repository->lastData);
    }

    public function test_it_updates_a_supplier(): void
    {
        $supplier = Supplier::factory()->make([
            'name' => 'Old Supplier',
        ]);
        $data = [
            'name' => 'Updated Supplier',
        ];
        $updated = Supplier::factory()->make($data);
        $this->repository->supplier = $updated;

        $this->assertSame($updated, $this->service->update($supplier, $data));
        $this->assertSame($supplier, $this->repository->lastSupplier);
        $this->assertSame($data, $this->repository->lastData);
    }

    public function test_it_deletes_a_supplier(): void
    {
        $supplier = Supplier::factory()->make();
        $this->repository->deleteResult = true;

        $this->assertTrue($this->service->delete($supplier));
        $this->assertSame($supplier, $this->repository->lastSupplier);
    }
}

final class FakeSupplierRepository implements SupplierRepositoryInterface
{
    public ?Supplier $supplier = null;

    public ?Supplier $lastSupplier = null;

    /** @var array<string, string|null> */
    public array $lastData = [];

    public ?int $lastId = null;

    public ?int $lastPerPage = null;

    public bool $deleteResult = false;

    private LengthAwarePaginatorContract $fallbackPaginator;

    public LengthAwarePaginatorContract $paginator;

    public function __construct()
    {
        $this->fallbackPaginator = new LengthAwarePaginator([], 0, 15);
        $this->paginator = $this->fallbackPaginator;
    }

    /** @return LengthAwarePaginatorContract<Supplier> */
    public function paginate(int $perPage = 15): LengthAwarePaginatorContract
    {
        $this->lastPerPage = $perPage;

        return $this->paginator;
    }

    public function find(int $id): ?Supplier
    {
        $this->lastId = $id;

        return $this->supplier;
    }

    /**
     * @param  array{name: string, phone?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Supplier
    {
        $this->lastData = $data;

        return $this->supplier ?? new Supplier($data);
    }

    /**
     * @param  array{name?: string, phone?: string|null, location?: string|null}  $data
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $this->lastSupplier = $supplier;
        $this->lastData = $data;

        return $this->supplier ?? $supplier;
    }

    public function delete(Supplier $supplier): bool
    {
        $this->lastSupplier = $supplier;

        return $this->deleteResult;
    }
}
