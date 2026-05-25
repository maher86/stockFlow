<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemService
{
    public function __construct(
        private readonly ItemRepositoryInterface $items,
    ) {}

    /** @return LengthAwarePaginator<Item> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->items->paginate($perPage);
    }

    public function find(int $id): ?Item
    {
        return $this->items->find($id);
    }

    public function findOrFail(int $id): Item
    {
        $item = $this->find($id);

        if (! $item instanceof Item) {
            throw new ModelNotFoundException('Item not found.');
        }

        return $item;
    }

    /**
     * @param  array{package_id: int, sku?: string|null, name: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null, conditions?: list<array{condition: string, price_tier: string, quantity: int}>}  $data
     */
    public function create(array $data): Item
    {
        $conditions = $data['conditions'] ?? [];
        unset($data['conditions']);

        $item = $this->items->create($data);

        if ($conditions !== []) {
            return $this->items->syncConditions($item, $conditions);
        }

        return $item;
    }

    /**
     * @param  array{package_id?: int, sku?: string|null, name?: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null, conditions?: list<array{condition: string, price_tier: string, quantity: int}>}  $data
     */
    public function update(Item $item, array $data): Item
    {
        $conditions = $data['conditions'] ?? null;
        unset($data['conditions']);

        $updatedItem = $this->items->update($item, $data);

        if (is_array($conditions)) {
            return $this->items->syncConditions($updatedItem, $conditions);
        }

        return $updatedItem;
    }

    /**
     * @param  list<array{condition: string, price_tier: string, quantity: int}>  $conditions
     */
    public function syncConditions(Item $item, array $conditions): Item
    {
        return $this->items->syncConditions($item, $conditions);
    }

    public function delete(Item $item): bool
    {
        return $this->items->delete($item);
    }
}
