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
});

require __DIR__ . '/settings.php';
