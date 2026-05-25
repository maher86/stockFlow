<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    /** @return LengthAwarePaginator<Customer> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Customer::query()
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Customer
    {
        return Customer::query()->find($id);
    }

    /**
     * @param  array{name: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Customer
    {
        return Customer::query()->create($data);
    }

    /**
     * @param  array{name?: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->refresh();
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }
}
