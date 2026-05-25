<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\ReportsController;
use App\Http\Controllers\Api\V1\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', static fn () => response()->json([
        'data' => [
            'name' => config('app.name'),
            'status' => 'ok',
        ],
    ]));

    Route::prefix('auth')->group(function (): void {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::apiResource('customers', CustomerController::class);
        Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
        Route::get('/dashboard/reports', [ReportsController::class, 'index']);
        Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus']);
        Route::post('/invoices/{invoice}/notes', [InvoiceController::class, 'notes']);
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf']);
        Route::apiResource('invoices', InvoiceController::class);
        Route::post('/items/bulk-update', [ItemController::class, 'bulkUpdate']);
        Route::apiResource('items', ItemController::class);
        Route::post('/packages/{package}/sort', [PackageController::class, 'sort']);
        Route::apiResource('packages', PackageController::class)->except(['destroy']);
        Route::apiResource('suppliers', SupplierController::class);
    });
});
