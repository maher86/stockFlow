<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    /** @return LengthAwarePaginator<Supplier> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::query()
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Supplier
    {
        return Supplier::query()->find($id);
    }

    /**
     * @param  array{name: string, phone?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Supplier
    {
        return Supplier::query()->create($data);
    }

    /**
     * @param  array{name?: string, phone?: string|null, location?: string|null}  $data
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->refresh();
    }

    public function delete(Supplier $supplier): bool
    {
        return (bool) $supplier->delete();
    }
}
