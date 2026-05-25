<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    /** @return LengthAwarePaginator<Customer> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->customers->paginate($perPage);
    }

    public function find(int $id): ?Customer
    {
        return $this->customers->find($id);
    }

    public function findOrFail(int $id): Customer
    {
        $customer = $this->find($id);

        if (! $customer instanceof Customer) {
            throw new ModelNotFoundException('Customer not found.');
        }

        return $customer;
    }

    /**
     * @param  array{name: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Customer
    {
        return $this->customers->create($data);
    }

    /**
     * @param  array{name?: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function update(Customer $customer, array $data): Customer
    {
        return $this->customers->update($customer, $data);
    }

    public function delete(Customer $customer): bool
    {
        return $this->customers->delete($customer);
    }
}
