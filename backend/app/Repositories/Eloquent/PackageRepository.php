<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PackageRepository implements PackageRepositoryInterface
{
    /** @return LengthAwarePaginator<Package> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Package::query()
            ->with('supplier')
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Package
    {
        return Package::query()
            ->with('supplier')
            ->find($id);
    }

    /**
     * @param  array{supplier_id: int, reference?: string|null, name: string, total_items?: int, status?: string, received_at: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function create(array $data): Package
    {
        return Package::query()->create($data)->load('supplier');
    }

    /**
     * @param  array{supplier_id?: int, reference?: string|null, name?: string, total_items?: int, status?: string, received_at?: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function update(Package $package, array $data): Package
    {
        $package->update($data);

        return $package->refresh()->load('supplier');
    }
}
