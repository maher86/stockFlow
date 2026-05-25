<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment('StockFlow API is ready.');
})->purpose('Display an inspiring message');
