<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    /** @return LengthAwarePaginator<Customer> */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Customer;

    /**
     * @param  array{name: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Customer;

    /**
     * @param  array{name?: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function update(Customer $customer, array $data): Customer;

    public function delete(Customer $customer): bool;
}
