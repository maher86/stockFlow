<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AppBootsTest extends TestCase
{
    public function test_application_boots_successfully(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertJsonPath('name', 'StockFlow')
            ->assertJsonPath('status', 'ok');
    }
}
