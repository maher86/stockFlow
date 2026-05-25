<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    /** @return LengthAwarePaginator<Supplier> */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Supplier;

    /**
     * @param  array{name: string, phone?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Supplier;

    /**
     * @param  array{name?: string, phone?: string|null, location?: string|null}  $data
     */
    public function update(Supplier $supplier, array $data): Supplier;

    public function delete(Supplier $supplier): bool;
}
