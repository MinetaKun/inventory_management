<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ShelfController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeValueController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('locations', LocationController::class);
Route::apiResource('shelves', ShelfController::class);
Route::apiResource('inventories', InventoryController::class);
Route::apiResource('attributes', AttributeController::class);
Route::apiResource('attribute-values', AttributeValueController::class);
