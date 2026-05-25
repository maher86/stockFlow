<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkUpdateItemsRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Services\ItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        private readonly ItemService $items,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);

        return ItemResource::collection($this->items->paginate($perPage))
            ->response();
    }

    public function store(StoreItemRequest $request): JsonResponse
    {
        /** @var array{package_id: int, sku?: string|null, name: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null, conditions?: list<array{condition: string, price_tier: string, quantity: int}>} $data */
        $data = $request->validated();
        $item = $this->items->create($data);

        return (new ItemResource($item))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $item): JsonResponse
    {
        return (new ItemResource($this->items->findOrFail($item)))
            ->response();
    }

    public function update(UpdateItemRequest $request, int $item): JsonResponse
    {
        /** @var array{package_id?: int, sku?: string|null, name?: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null, conditions?: list<array{condition: string, price_tier: string, quantity: int}>} $data */
        $data = $request->validated();
        $existingItem = $this->items->findOrFail($item);
        $updatedItem = $this->items->update($existingItem, $data);

        return (new ItemResource($updatedItem))
            ->response();
    }

    public function destroy(int $item): JsonResponse
    {
        $existingItem = $this->items->findOrFail($item);

        $this->items->delete($existingItem);

        return response()->json(status: 204);
    }

    public function bulkUpdate(BulkUpdateItemsRequest $request): JsonResponse
    {
        /** @var array{items: list<array{id: int, package_id?: int, sku?: string|null, name?: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null, conditions?: list<array{condition: string, price_tier: string, quantity: int}>}>} $data */
        $data = $request->validated();
        $updatedItems = [];

        foreach ($data['items'] as $itemData) {
            $item = $this->items->findOrFail($itemData['id']);
            unset($itemData['id']);

            $updatedItems[] = $this->items->update($item, $itemData);
        }

        return ItemResource::collection(collect($updatedItems))
            ->response();
    }
}
