<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', static fn () => response()->json([
        'data' => [
            'name' => config('app.name'),
            'status' => 'ok',
        ],
    ]));
});
