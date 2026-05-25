<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceNoteRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceStatusRequest;
use App\Http\Resources\InvoiceNoteResource;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoices,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);

        return InvoiceResource::collection($this->invoices->paginate($perPage))
            ->response();
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        /** @var array{customer_id: int, invoice_number: string, issued_at: string, due_at?: string|null, discount?: int, paid_amount?: int, notes?: string|null, items?: list<array{item_id?: int|null, description: string, quantity: int, unit_price: int}>} $data */
        $data = $request->validated();
        $invoice = $this->invoices->create($data);

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $invoice): JsonResponse
    {
        return (new InvoiceResource($this->invoices->findOrFail($invoice)))
            ->response();
    }

    public function update(UpdateInvoiceRequest $request, int $invoice): JsonResponse
    {
        /** @var array{customer_id?: int, invoice_number?: string, issued_at?: string, due_at?: string|null, discount?: int, paid_amount?: int, notes?: string|null, items?: list<array{item_id?: int|null, description: string, quantity: int, unit_price: int}>} $data */
        $data = $request->validated();
        $existingInvoice = $this->invoices->findOrFail($invoice);
        $updatedInvoice = $this->invoices->update($existingInvoice, $data);

        return (new InvoiceResource($updatedInvoice))
            ->response();
    }

    public function destroy(int $invoice): JsonResponse
    {
        $existingInvoice = $this->invoices->findOrFail($invoice);

        $this->invoices->delete($existingInvoice);

        return response()->json(status: 204);
    }

    public function updateStatus(UpdateInvoiceStatusRequest $request, int $invoice): JsonResponse
    {
        /** @var array{status: string} $data */
        $data = $request->validated();
        $existingInvoice = $this->invoices->findOrFail($invoice);
        $updatedInvoice = $this->invoices->updateStatus($existingInvoice, InvoiceStatus::from($data['status']));

        return (new InvoiceResource($updatedInvoice))
            ->response();
    }

    public function notes(StoreInvoiceNoteRequest $request, int $invoice): JsonResponse
    {
        /** @var array{body: string} $data */
        $data = $request->validated();
        $existingInvoice = $this->invoices->findOrFail($invoice);
        $note = $this->invoices->addNote($existingInvoice, $data['body']);

        return (new InvoiceNoteResource($note))
            ->response()
            ->setStatusCode(201);
    }

    public function pdf(int $invoice): JsonResponse
    {
        $existingInvoice = $this->invoices->findOrFail($invoice);

        return response()->json([
            'data' => [
                'invoice_id' => $existingInvoice->id,
                'message' => 'PDF generation placeholder.',
            ],
        ]);
    }
}
