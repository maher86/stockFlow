<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ItemRepository implements ItemRepositoryInterface
{
    /** @return LengthAwarePaginator<Item> */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Item::query()
            ->with(['package', 'conditions'])
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Item
    {
        return Item::query()
            ->with(['package', 'conditions'])
            ->find($id);
    }

    /**
     * @param  array{package_id: int, sku?: string|null, name: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null}  $data
     */
    public function create(array $data): Item
    {
        return Item::query()->create($data)->load(['package', 'conditions']);
    }

    /**
     * @param  array{package_id?: int, sku?: string|null, name?: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null}  $data
     */
    public function update(Item $item, array $data): Item
    {
        $item->update($data);

        return $item->refresh()->load(['package', 'conditions']);
    }

    public function delete(Item $item): bool
    {
        return (bool) $item->delete();
    }

    /**
     * @param  list<array{condition: string, price_tier: string, quantity: int}>  $conditions
     */
    public function syncConditions(Item $item, array $conditions): Item
    {
        DB::transaction(function () use ($item, $conditions): void {
            $item->conditions()->delete();
            $item->conditions()->createMany($conditions);
        });

        return $item->refresh()->load(['package', 'conditions']);
    }
}
