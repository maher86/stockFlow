<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceHistory;
use App\Models\InvoiceNote;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\InvoiceService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    private FakeInvoiceRepository $repository;

    private InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new FakeInvoiceRepository;
        $this->service = new InvoiceService($this->repository);
    }

    public function test_it_paginates_invoices(): void
    {
        $paginator = new LengthAwarePaginator([], 3, 10);
        $this->repository->paginator = $paginator;

        $this->assertSame($paginator, $this->service->paginate(10));
        $this->assertSame(10, $this->repository->lastPerPage);
    }

    public function test_it_finds_invoice_or_fails(): void
    {
        $invoice = $this->makeInvoice();
        $this->repository->invoice = $invoice;

        $this->assertSame($invoice, $this->service->findOrFail(5));
        $this->assertSame(5, $this->repository->lastId);
    }

    public function test_it_throws_when_invoice_is_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findOrFail(5);
    }

    public function test_it_creates_invoice_with_calculated_totals_and_items(): void
    {
        $invoice = $this->makeInvoice();
        $this->repository->invoice = $invoice;
        $data = [
            'customer_id' => 1,
            'invoice_number' => 'INV-1001',
            'issued_at' => '2026-05-25',
            'discount' => 50,
            'items' => [
                [
                    'description' => 'Blue Jacket',
                    'quantity' => 2,
                    'unit_price' => 300,
                ],
                [
                    'description' => 'White Shirt',
                    'quantity' => 1,
                    'unit_price' => 200,
                ],
            ],
        ];

        $this->assertSame($invoice, $this->service->create($data));
        $this->assertSame(800, $this->repository->lastData['subtotal']);
        $this->assertSame(750, $this->repository->lastData['total']);
        $this->assertCount(2, $this->repository->lastItems);
        $this->assertSame(600, $this->repository->lastItems[0]['line_total']);
        $this->assertSame('created', $this->repository->lastHistoryEvent);
    }

    public function test_it_updates_invoice_and_records_history(): void
    {
        $invoice = $this->makeInvoice([
            'discount' => 0,
        ]);
        $this->repository->invoice = $invoice;

        $this->assertSame($invoice, $this->service->update($invoice, [
            'discount' => 100,
            'items' => [
                [
                    'description' => 'Blue Jacket',
                    'quantity' => 2,
                    'unit_price' => 300,
                ],
            ],
        ]));

        $this->assertSame(500, $this->repository->lastData['total']);
        $this->assertSame('updated', $this->repository->lastHistoryEvent);
    }

    public function test_it_updates_status_and_adds_note(): void
    {
        $invoice = $this->makeInvoice();
        $this->repository->invoice = $invoice;

        $this->assertSame($invoice, $this->service->updateStatus($invoice, InvoiceStatus::Paid));
        $this->assertSame(InvoiceStatus::Paid->value, $this->repository->lastData['status']);

        $note = $this->service->addNote($invoice, 'Paid by bank transfer.');

        $this->assertSame('Paid by bank transfer.', $note->body);
        $this->assertSame('note_added', $this->repository->lastHistoryEvent);
    }

    /**
     * @param  array<string, int|string|null>  $attributes
     */
    private function makeInvoice(array $attributes = []): Invoice
    {
        return new Invoice(array_merge([
            'customer_id' => 1,
            'invoice_number' => 'INV-1001',
            'status' => InvoiceStatus::Draft,
            'issued_at' => '2026-05-25',
            'due_at' => null,
            'subtotal' => 0,
            'discount' => 0,
            'total' => 0,
            'paid_amount' => 0,
            'notes' => null,
        ], $attributes));
    }
}

final class FakeInvoiceRepository implements InvoiceRepositoryInterface
{
    public ?Invoice $invoice = null;

    /** @var array<string, int|string|null> */
    public array $lastData = [];

    /** @var list<array{item_id?: int|null, description: string, quantity: int, unit_price: int, line_total: int}> */
    public array $lastItems = [];

    public ?int $lastId = null;

    public ?int $lastPerPage = null;

    public ?string $lastHistoryEvent = null;

    private LengthAwarePaginatorContract $fallbackPaginator;

    public LengthAwarePaginatorContract $paginator;

    public function __construct()
    {
        $this->fallbackPaginator = new LengthAwarePaginator([], 0, 15);
        $this->paginator = $this->fallbackPaginator;
    }

    /** @return LengthAwarePaginatorContract<Invoice> */
    public function paginate(int $perPage = 15): LengthAwarePaginatorContract
    {
        $this->lastPerPage = $perPage;

        return $this->paginator;
    }

    public function find(int $id): ?Invoice
    {
        $this->lastId = $id;

        return $this->invoice;
    }

    /**
     * @param  array{customer_id: int, invoice_number: string, status?: string, issued_at: string, due_at?: string|null, subtotal?: int, discount?: int, total?: int, paid_amount?: int, notes?: string|null}  $data
     */
    public function create(array $data): Invoice
    {
        $this->lastData = $data;

        return $this->invoice ?? new Invoice($data);
    }

    /**
     * @param  array{customer_id?: int, invoice_number?: string, status?: string, issued_at?: string, due_at?: string|null, subtotal?: int, discount?: int, total?: int, paid_amount?: int, notes?: string|null}  $data
     */
    public function update(Invoice $invoice, array $data): Invoice
    {
        $this->lastData = $data;

        return $this->invoice ?? $invoice;
    }

    public function delete(Invoice $invoice): bool
    {
        return true;
    }

    /**
     * @param  list<array{item_id?: int|null, description: string, quantity: int, unit_price: int, line_total: int}>  $items
     */
    public function syncItems(Invoice $invoice, array $items): Invoice
    {
        $this->lastItems = $items;

        return $this->invoice ?? $invoice;
    }

    public function addNote(Invoice $invoice, string $body): InvoiceNote
    {
        return new InvoiceNote([
            'invoice_id' => $invoice->id,
            'body' => $body,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public function addHistory(Invoice $invoice, string $event, ?array $payload = null): InvoiceHistory
    {
        $this->lastHistoryEvent = $event;

        return new InvoiceHistory([
            'invoice_id' => $invoice->id,
            'event' => $event,
            'payload' => $payload,
        ]);
    }
}
