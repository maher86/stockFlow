<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customers,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);

        return CustomerResource::collection($this->customers->paginate($perPage))
            ->response();
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        /** @var array{name: string, phone?: string|null, email?: string|null, location?: string|null} $data */
        $data = $request->validated();
        $customer = $this->customers->create($data);

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $customer): JsonResponse
    {
        return (new CustomerResource($this->customers->findOrFail($customer)))
            ->response();
    }

    public function update(UpdateCustomerRequest $request, int $customer): JsonResponse
    {
        /** @var array{name?: string, phone?: string|null, email?: string|null, location?: string|null} $data */
        $data = $request->validated();
        $existingCustomer = $this->customers->findOrFail($customer);
        $updatedCustomer = $this->customers->update($existingCustomer, $data);

        return (new CustomerResource($updatedCustomer))
            ->response();
    }

    public function destroy(int $customer): JsonResponse
    {
        $existingCustomer = $this->customers->findOrFail($customer);

        $this->customers->delete($existingCustomer);

        return response()->json(status: 204);
    }
}
