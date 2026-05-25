<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PackageStatus;
use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PackageService
{
    public function __construct(
        private readonly PackageRepositoryInterface $packages,
    ) {}

    /** @return LengthAwarePaginator<Package> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->packages->paginate($perPage);
    }

    public function find(int $id): ?Package
    {
        return $this->packages->find($id);
    }

    public function findOrFail(int $id): Package
    {
        $package = $this->find($id);

        if (! $package instanceof Package) {
            throw new ModelNotFoundException('Package not found.');
        }

        return $package;
    }

    /**
     * @param  array{supplier_id: int, reference?: string|null, name: string, total_items?: int, status?: string, received_at: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function create(array $data): Package
    {
        $data['status'] = PackageStatus::Unsorted->value;
        $data['sorted_at'] = null;

        return $this->packages->create($data);
    }

    /**
     * @param  array{supplier_id?: int, reference?: string|null, name?: string, total_items?: int, status?: string, received_at?: string, sorted_at?: string|null, notes?: string|null}  $data
     */
    public function update(Package $package, array $data): Package
    {
        unset($data['status'], $data['sorted_at']);

        return $this->packages->update($package, $data);
    }

    /**
     * @throws ValidationException
     */
    public function sort(Package $package): Package
    {
        if ($package->status !== PackageStatus::Unsorted) {
            throw ValidationException::withMessages([
                'status' => ['Only unsorted packages can be marked as sorted.'],
            ]);
        }

        return $this->packages->update($package, [
            'status' => PackageStatus::Sorted->value,
            'sorted_at' => now()->toDateTimeString(),
        ]);
    }
}
