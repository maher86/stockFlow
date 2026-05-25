<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboard,
    ) {}

    /**
     * @return array<string, int>
     */
    public function overview(): array
    {
        return $this->dashboard->overview();
    }

    /**
     * @return array{monthly: list<array{month: string, total: int}>, conditions: list<array{condition: string, quantity: int}>, types: list<array{type: string, quantity: int}>}
     */
    public function reports(): array
    {
        return [
            'monthly' => $this->dashboard->monthlyRevenue(),
            'conditions' => $this->dashboard->conditionBreakdown(),
            'types' => $this->dashboard->typeBreakdown(),
        ];
    }
}
