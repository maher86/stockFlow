<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AppTest extends TestCase
{
    public function test_health_endpoint_returns_ok_response(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('data.name', 'StockFlow')
            ->assertJsonPath('data.status', 'ok');
    }
}
