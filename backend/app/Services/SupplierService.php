<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierService
{
    public function __construct(
        private readonly SupplierRepositoryInterface $suppliers,
    ) {}

    /** @return LengthAwarePaginator<Supplier> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->suppliers->paginate($perPage);
    }

    public function find(int $id): ?Supplier
    {
        return $this->suppliers->find($id);
    }

    public function findOrFail(int $id): Supplier
    {
        $supplier = $this->find($id);

        if (! $supplier instanceof Supplier) {
            throw new ModelNotFoundException('Supplier not found.');
        }

        return $supplier;
    }

    /**
     * @param  array{name: string, phone?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Supplier
    {
        return $this->suppliers->create($data);
    }

    /**
     * @param  array{name?: string, phone?: string|null, location?: string|null}  $data
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->suppliers->update($supplier, $data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->suppliers->delete($supplier);
    }
}
