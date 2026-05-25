<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Package;
use App\Models\Supplier;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * @return array<string, int>
     */
    public function overview(): array
    {
        $invoiceTotals = Invoice::query()
            ->selectRaw('COALESCE(SUM(total), 0) as revenue_total')
            ->selectRaw('COALESCE(SUM(total - paid_amount), 0) as outstanding_total')
            ->where('status', '!=', InvoiceStatus::Cancelled->value)
            ->first();

        return [
            'suppliers_count' => Supplier::query()->count(),
            'customers_count' => Customer::query()->count(),
            'packages_count' => Package::query()->count(),
            'items_count' => Item::query()->count(),
            'invoices_count' => Invoice::query()->count(),
            'revenue_total' => (int) ($invoiceTotals?->revenue_total ?? 0),
            'outstanding_total' => (int) ($invoiceTotals?->outstanding_total ?? 0),
        ];
    }

    /**
     * @return list<array{month: string, total: int}>
     */
    public function monthlyRevenue(): array
    {
        return Invoice::query()
            ->selectRaw("strftime('%Y-%m', issued_at) as month")
            ->selectRaw('COALESCE(SUM(total), 0) as total')
            ->where('status', '!=', InvoiceStatus::Cancelled->value)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(static fn (Invoice $invoice): array => [
                'month' => (string) $invoice->getAttribute('month'),
                'total' => (int) $invoice->total,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{condition: string, quantity: int}>
     */
    public function conditionBreakdown(): array
    {
        return ItemCondition::query()
            ->selectRaw('condition')
            ->selectRaw('COALESCE(SUM(quantity), 0) as quantity')
            ->groupBy('condition')
            ->orderBy('condition')
            ->get()
            ->map(static fn (ItemCondition $condition): array => [
                'condition' => (string) $condition->condition->value,
                'quantity' => (int) $condition->quantity,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{type: string, quantity: int}>
     */
    public function typeBreakdown(): array
    {
        return Item::query()
            ->selectRaw('type')
            ->selectRaw('COALESCE(SUM(quantity), 0) as quantity')
            ->groupBy('type')
            ->orderBy('type')
            ->get()
            ->map(static fn (Item $item): array => [
                'type' => (string) $item->type->value,
                'quantity' => (int) $item->quantity,
            ])
            ->values()
            ->all();
    }
}
