<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use Illuminate\Http\Response;

class InventoryController extends Controller
{
    public function index()
    {
        $query = Inventory::with(['category', 'addedBy']);

        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        return response()->json([
            'data' => $query->orderBy('product_name')->get(),
        ]);
    }

    public function store(StoreInventoryRequest $request)
    {
        $inventory = Inventory::create([
            ...$request->validated(),
            'added_by' => auth()->id() ?? 1,
        ]);

        return response()->json([
            'data' => $inventory->load(['category', 'addedBy']),
            'message' => 'Inventory created successfully',
        ], Response::HTTP_CREATED);
    }

    public function show(Inventory $inventory)
    {
        return response()->json([
            'data' => $inventory->load(['category', 'addedBy']),
        ]);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $inventory->update($request->validated());

        return response()->json([
            'data' => $inventory->load(['category', 'addedBy']),
            'message' => 'Inventory updated successfully',
        ]);
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return response()->json([
            'message' => 'Inventory deleted successfully',
        ]);
    }
}
