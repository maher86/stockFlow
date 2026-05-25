<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Package;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PackageRepositoryInterface
{
    /** @return LengthAwarePaginator<Package> */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Package;

    /**
     * @param  array{supplier_id: int, reference?: string|null, name: string, total_items?: int, status?: string, received_at: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function create(array $data): Package;

    /**
     * @param  array{supplier_id?: int, reference?: string|null, name?: string, total_items?: int, status?: string, received_at?: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function update(Package $package, array $data): Package;
}
