<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\CustomerService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    private FakeCustomerRepository $repository;

    private CustomerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new FakeCustomerRepository;
        $this->service = new CustomerService($this->repository);
    }

    public function test_it_paginates_customers(): void
    {
        $paginator = new LengthAwarePaginator([], 3, 10);
        $this->repository->paginator = $paginator;

        $this->assertSame($paginator, $this->service->paginate(10));
        $this->assertSame(10, $this->repository->lastPerPage);
    }

    public function test_it_finds_a_customer(): void
    {
        $customer = Customer::factory()->make();
        $this->repository->customer = $customer;

        $this->assertSame($customer, $this->service->find(5));
        $this->assertSame(5, $this->repository->lastId);
    }

    public function test_it_finds_a_customer_or_fails(): void
    {
        $customer = Customer::factory()->make();
        $this->repository->customer = $customer;

        $this->assertSame($customer, $this->service->findOrFail(5));
    }

    public function test_it_throws_when_customer_is_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(5);
    }

    public function test_it_creates_a_customer(): void
    {
        $data = [
            'name' => 'Mariam Ali',
            'phone' => '050-1112223',
            'email' => 'mariam@example.com',
            'location' => 'Dubai, UAE',
        ];
        $customer = Customer::factory()->make($data);
        $this->repository->customer = $customer;

        $this->assertSame($customer, $this->service->create($data));
        $this->assertSame($data, $this->repository->lastData);
    }

    public function test_it_updates_a_customer(): void
    {
        $customer = Customer::factory()->make([
            'name' => 'Old Customer',
        ]);
        $data = [
            'name' => 'Updated Customer',
        ];
        $updated = Customer::factory()->make($data);
        $this->repository->customer = $updated;

        $this->assertSame($updated, $this->service->update($customer, $data));
        $this->assertSame($customer, $this->repository->lastCustomer);
        $this->assertSame($data, $this->repository->lastData);
    }

    public function test_it_deletes_a_customer(): void
    {
        $customer = Customer::factory()->make();
        $this->repository->deleteResult = true;

        $this->assertTrue($this->service->delete($customer));
        $this->assertSame($customer, $this->repository->lastCustomer);
    }
}

final class FakeCustomerRepository implements CustomerRepositoryInterface
{
    public ?Customer $customer = null;

    public ?Customer $lastCustomer = null;

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

    /** @return LengthAwarePaginatorContract<Customer> */
    public function paginate(int $perPage = 15): LengthAwarePaginatorContract
    {
        $this->lastPerPage = $perPage;

        return $this->paginator;
    }

    public function find(int $id): ?Customer
    {
        $this->lastId = $id;

        return $this->customer;
    }

    /**
     * @param  array{name: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function create(array $data): Customer
    {
        $this->lastData = $data;

        return $this->customer ?? new Customer($data);
    }

    /**
     * @param  array{name?: string, phone?: string|null, email?: string|null, location?: string|null}  $data
     */
    public function update(Customer $customer, array $data): Customer
    {
        $this->lastCustomer = $customer;
        $this->lastData = $data;

        return $this->customer ?? $customer;
    }

    public function delete(Customer $customer): bool
    {
        $this->lastCustomer = $customer;

        return $this->deleteResult;
    }
}
