<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $suppliers,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);

        return SupplierResource::collection($this->suppliers->paginate($perPage))
            ->response();
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        /** @var array{name: string, phone?: string|null, location?: string|null} $data */
        $data = $request->validated();
        $supplier = $this->suppliers->create($data);

        return (new SupplierResource($supplier))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $supplier): JsonResponse
    {
        return (new SupplierResource($this->suppliers->findOrFail($supplier)))
            ->response();
    }

    public function update(UpdateSupplierRequest $request, int $supplier): JsonResponse
    {
        /** @var array{name?: string, phone?: string|null, location?: string|null} $data */
        $data = $request->validated();
        $existingSupplier = $this->suppliers->findOrFail($supplier);
        $updatedSupplier = $this->suppliers->update($existingSupplier, $data);

        return (new SupplierResource($updatedSupplier))
            ->response();
    }

    public function destroy(int $supplier): JsonResponse
    {
        $existingSupplier = $this->suppliers->findOrFail($supplier);

        $this->suppliers->delete($existingSupplier);

        return response()->json(status: 204);
    }
}
