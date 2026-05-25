<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface
{
    /**
     * @return array<string, int>
     */
    public function overview(): array;

    /**
     * @return list<array{month: string, total: int}>
     */
    public function monthlyRevenue(): array;

    /**
     * @return list<array{condition: string, quantity: int}>
     */
    public function conditionBreakdown(): array;

    /**
     * @return list<array{type: string, quantity: int}>
     */
    public function typeBreakdown(): array;
}
