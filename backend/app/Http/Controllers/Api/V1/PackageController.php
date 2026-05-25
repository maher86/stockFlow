<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Http\Resources\PackageResource;
use App\Services\PackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(
        private readonly PackageService $packages,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);

        return PackageResource::collection($this->packages->paginate($perPage))
            ->response();
    }

    public function store(StorePackageRequest $request): JsonResponse
    {
        /** @var array{supplier_id: int, reference?: string|null, name: string, total_items?: int, received_at: string, notes?: string|null} $data */
        $data = $request->validated();
        $package = $this->packages->create($data);

        return (new PackageResource($package))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $package): JsonResponse
    {
        return (new PackageResource($this->packages->findOrFail($package)))
            ->response();
    }

    public function update(UpdatePackageRequest $request, int $package): JsonResponse
    {
        /** @var array{supplier_id?: int, reference?: string|null, name?: string, total_items?: int, received_at?: string, notes?: string|null} $data */
        $data = $request->validated();
        $existingPackage = $this->packages->findOrFail($package);
        $updatedPackage = $this->packages->update($existingPackage, $data);

        return (new PackageResource($updatedPackage))
            ->response();
    }

    public function sort(int $package): JsonResponse
    {
        $existingPackage = $this->packages->findOrFail($package);
        $sortedPackage = $this->packages->sort($existingPackage);

        return (new PackageResource($sortedPackage))
            ->response();
    }
}
