<?php

declare(strict_types=1);

test('application boots successfully', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertJsonPath('name', 'StockFlow')
        ->assertJsonPath('status', 'ok');
});
