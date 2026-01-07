<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('categories', 'Categories/Index')->name('categories.index');
    Route::inertia('locations', 'Locations/Index')->name('locations.index');
    Route::inertia('shelves', 'Shelves/Index')->name('shelves.index');
    Route::inertia('inventories', 'Inventories/Index')->name('inventories.index');
    Route::inertia('attributes', 'Attributes/Index')->name('attributes.index');
    Route::inertia('variants', 'Variants/Index')->name('variants.index');
    Route::inertia('stock-in', 'StockIn/Index')->name('stock-in.index');
    Route::inertia('stock-movements', 'StockMovements/Index')->name('stock-movements.index');
    Route::inertia('qr/scan', 'QR/Scan')->name('qr.scan');
    Route::get('alerts', [App\Http\Controllers\Api\AlertController::class, 'index'])->name('alerts.index');
    Route::get('variants/{id}', function ($id) {
        return Inertia::render('Variants/Show', ['id' => $id]);
    })->name('variants.show');

    // Internal API Routes (Session Auth)
    Route::prefix('api')->group(function () {
        Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
        Route::apiResource('locations', App\Http\Controllers\Api\LocationController::class);
        Route::apiResource('shelves', App\Http\Controllers\Api\ShelfController::class);
        Route::apiResource('inventories', App\Http\Controllers\Api\InventoryController::class);
        Route::apiResource('attributes', App\Http\Controllers\Api\AttributeController::class);
        Route::apiResource('attribute-values', App\Http\Controllers\Api\AttributeValueController::class);
        Route::apiResource('variants', App\Http\Controllers\Api\VariantController::class);
        Route::post('stock-in', [App\Http\Controllers\Api\StockInController::class, 'store']);
        Route::get('stock-movements', [App\Http\Controllers\Api\StockMovementController::class, 'index']);
        Route::post('stock-movements', [App\Http\Controllers\Api\StockMovementController::class, 'store']);
        Route::post('stock-movements/{id}/return', [App\Http\Controllers\Api\StockMovementController::class, 'returnStock']);
        Route::get('qr/variant/{variant}', [App\Http\Controllers\Api\QrCodeController::class, 'generate']);
        Route::get('qr/download-all', [App\Http\Controllers\Api\QrCodeController::class, 'downloadAll']);
        Route::get('qr/scan', [App\Http\Controllers\Api\QrCodeController::class, 'show']);
    });
});

require __DIR__ . '/settings.php';
