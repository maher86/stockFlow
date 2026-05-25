<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ItemRepositoryInterface
{
    /** @return LengthAwarePaginator<Item> */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Item;

    /**
     * @param  array{package_id: int, sku?: string|null, name: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null}  $data
     */
    public function create(array $data): Item;

    /**
     * @param  array{package_id?: int, sku?: string|null, name?: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null}  $data
     */
    public function update(Item $item, array $data): Item;

    public function delete(Item $item): bool;

    /**
     * @param  list<array{condition: string, price_tier: string, quantity: int}>  $conditions
     */
    public function syncConditions(Item $item, array $conditions): Item;
}
