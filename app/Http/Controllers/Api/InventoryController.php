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
        $data = $request->validated();

        if (empty($data['sku_ref'])) {
            $category = \App\Models\Category::find($data['category_id']);
            $catSlug = $category ? \Illuminate\Support\Str::slug($category->name) : 'GEN';
            $labelSlug = \Illuminate\Support\Str::slug($data['product_label']);
            
            // Sequence based on items in this category
            $count = Inventory::where('category_id', $data['category_id'])->count() + 1;
            
            $generatedSku = strtoupper(sprintf('%s-%s-%03d', $catSlug, $labelSlug, $count));
            
            // Ensure uniqueness
            while (Inventory::where('sku_ref', $generatedSku)->exists()) {
                $count++;
                $generatedSku = strtoupper(sprintf('%s-%s-%03d', $catSlug, $labelSlug, $count));
            }
            $data['sku_ref'] = $generatedSku;
        }

        $inventory = Inventory::create([
            ...$data,
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

    public function nextSequence(\Illuminate\Http\Request $request)
    {
        $categoryId = $request->query('category_id');
        if (!$categoryId) {
            return response()->json(['sequence' => 1]);
        }
        $count = Inventory::where('category_id', $categoryId)->count() + 1;
        return response()->json(['sequence' => $count]);
    }
}
