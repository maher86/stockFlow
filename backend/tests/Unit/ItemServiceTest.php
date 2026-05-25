<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\Gender;
use App\Enums\ItemConditionValue;
use App\Enums\ItemType;
use App\Enums\PriceTier;
use App\Enums\Season;
use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Services\ItemService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    private FakeItemRepository $repository;

    private ItemService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new FakeItemRepository;
        $this->service = new ItemService($this->repository);
    }

    public function test_it_paginates_items(): void
    {
        $paginator = new LengthAwarePaginator([], 3, 10);
        $this->repository->paginator = $paginator;

        $this->assertSame($paginator, $this->service->paginate(10));
        $this->assertSame(10, $this->repository->lastPerPage);
    }

    public function test_it_finds_an_item_or_fails(): void
    {
        $item = $this->makeItem();
        $this->repository->item = $item;

        $this->assertSame($item, $this->service->findOrFail(5));
        $this->assertSame(5, $this->repository->lastId);
    }

    public function test_it_throws_when_item_is_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(5);
    }

    public function test_it_creates_item_and_syncs_conditions(): void
    {
        $item = $this->makeItem();
        $this->repository->item = $item;
        $data = [
            'package_id' => 1,
            'name' => 'Blue Jacket',
            'season' => Season::Winter->value,
            'gender' => Gender::Men->value,
            'type' => ItemType::Jacket->value,
            'quantity' => 12,
            'conditions' => [
                [
                    'condition' => ItemConditionValue::Perfect->value,
                    'price_tier' => PriceTier::N3->value,
                    'quantity' => 5,
                ],
            ],
        ];

        $this->assertSame($item, $this->service->create($data));
        $this->assertArrayNotHasKey('conditions', $this->repository->lastData);
        $this->assertCount(1, $this->repository->lastConditions);
    }

    public function test_it_updates_item_and_syncs_conditions_when_provided(): void
    {
        $item = $this->makeItem();
        $this->repository->item = $item;
        $data = [
            'name' => 'Updated Item',
            'conditions' => [
                [
                    'condition' => ItemConditionValue::Good->value,
                    'price_tier' => PriceTier::N2->value,
                    'quantity' => 8,
                ],
            ],
        ];

        $this->assertSame($item, $this->service->update($item, $data));
        $this->assertSame($item, $this->repository->lastItem);
        $this->assertArrayNotHasKey('conditions', $this->repository->lastData);
        $this->assertCount(1, $this->repository->lastConditions);
    }

    public function test_it_deletes_an_item(): void
    {
        $item = $this->makeItem();
        $this->repository->deleteResult = true;

        $this->assertTrue($this->service->delete($item));
        $this->assertSame($item, $this->repository->lastItem);
    }

    /**
     * @param  array<string, int|string|null>  $attributes
     */
    private function makeItem(array $attributes = []): Item
    {
        return new Item(array_merge([
            'package_id' => 1,
            'sku' => 'ITM-1001',
            'name' => 'Blue Jacket',
            'season' => Season::Winter->value,
            'gender' => Gender::Men->value,
            'type' => ItemType::Jacket->value,
            'quantity' => 12,
            'notes' => null,
        ], $attributes));
    }
}

final class FakeItemRepository implements ItemRepositoryInterface
{
    public ?Item $item = null;

    public ?Item $lastItem = null;

    /** @var array<string, int|string|null> */
    public array $lastData = [];

    /** @var list<array{condition: string, price_tier: string, quantity: int}> */
    public array $lastConditions = [];

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

    /** @return LengthAwarePaginatorContract<Item> */
    public function paginate(int $perPage = 15): LengthAwarePaginatorContract
    {
        $this->lastPerPage = $perPage;

        return $this->paginator;
    }

    public function find(int $id): ?Item
    {
        $this->lastId = $id;

        return $this->item;
    }

    /**
     * @param  array{package_id: int, sku?: string|null, name: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null}  $data
     */
    public function create(array $data): Item
    {
        $this->lastData = $data;

        return $this->item ?? new Item($data);
    }

    /**
     * @param  array{package_id?: int, sku?: string|null, name?: string, season?: string, gender?: string, type?: string, quantity?: int, notes?: string|null}  $data
     */
    public function update(Item $item, array $data): Item
    {
        $this->lastItem = $item;
        $this->lastData = $data;

        return $this->item ?? $item;
    }

    public function delete(Item $item): bool
    {
        $this->lastItem = $item;

        return $this->deleteResult;
    }

    /**
     * @param  list<array{condition: string, price_tier: string, quantity: int}>  $conditions
     */
    public function syncConditions(Item $item, array $conditions): Item
    {
        $this->lastItem = $item;
        $this->lastConditions = $conditions;

        return $this->item ?? $item;
    }
}
