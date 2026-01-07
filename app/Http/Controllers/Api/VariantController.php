<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVariantRequest;
use App\Http\Requests\UpdateVariantRequest;
use App\Models\Variant;
use App\Models\VariantAttributeValue;
use Illuminate\Http\Response;

class VariantController extends Controller
{
    public function index()
    {
        $query = Variant::with(['inventory.category', 'attributeValues.attribute']);

        if (request('inventory_id')) {
            $query->where('inventory_id', request('inventory_id'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        return response()->json([
            'data' => $query->orderBy('sku')->get(),
        ]);
    }

    public function store(StoreVariantRequest $request)
    {
        $data = $request->only([
            'inventory_id',
            'sku',
            'quantity',
            'image',
            'note',
            'status',
            'min_stock',
        ]);

        if (empty($data['sku'])) {
            $inventory = \App\Models\Inventory::with('category')->find($data['inventory_id']);
            if ($inventory) {
                // Generate SKU: Product-Category-001
                $productSlug = \Illuminate\Support\Str::slug($inventory->product_name);
                $catSlug = $inventory->category ? \Illuminate\Support\Str::slug($inventory->category->name) : 'GEN'; // General if no cat
                
                // Truncate slugs if too long for cleaner SKUs? 
                // Let's take first 3 chars of Cat for "Code"? 
                // User said "Label - category name". Let's stick to full name but slugged.
                
                $count = Variant::where('inventory_id', $inventory->id)->count() + 1;
                $generatedSku = strtoupper(sprintf('%s-%s-%03d', $productSlug, $catSlug, $count));
                
                // Ensure uniqueness
                while (Variant::where('sku', $generatedSku)->exists()) {
                    $count++;
                    $generatedSku = strtoupper(sprintf('%s-%s-%03d', $productSlug, $catSlug, $count));
                }
                $data['sku'] = $generatedSku;
            } else {
                // Fallback
                $data['sku'] = 'VAR-' . uniqid();
            }
        }

        $variant = Variant::create($data);

        // Attach attribute values with their attribute IDs
        $attributeValues = $request->attribute_values;
        $pivotData = [];

        foreach ($attributeValues as $attributeValueId) {
            $attributeValue = \App\Models\AttributeValue::find($attributeValueId);
            if ($attributeValue) {
                $pivotData[$attributeValueId] = [
                    'attribute_id' => $attributeValue->attribute_id,
                ];
            }
        }

        $variant->attributeValues()->attach($pivotData);

        return response()->json([
            'data' => $variant->load(['inventory.category', 'attributeValues.attribute']),
            'message' => 'Variant created successfully',
        ], Response::HTTP_CREATED);
    }

    public function show(Variant $variant)
    {
        return response()->json([
            'data' => $variant->load(['inventory.category', 'attributeValues.attribute']),
        ]);
    }

    public function update(UpdateVariantRequest $request, Variant $variant)
    {
        $variant->update($request->only([
            'inventory_id',
            'sku',
            'quantity',
            'image',
            'note',
            'status',
        ]));

        // Sync attribute values with their attribute IDs
        $attributeValues = $request->attribute_values;
        $pivotData = [];

        foreach ($attributeValues as $attributeValueId) {
            $attributeValue = \App\Models\AttributeValue::find($attributeValueId);
            if ($attributeValue) {
                $pivotData[$attributeValueId] = [
                    'attribute_id' => $attributeValue->attribute_id,
                ];
            }
        }

        $variant->attributeValues()->sync($pivotData);

        return response()->json([
            'data' => $variant->load(['inventory.category', 'attributeValues.attribute']),
            'message' => 'Variant updated successfully',
        ]);
    }

    public function destroy(Variant $variant)
    {
        $variant->delete();

        return response()->json([
            'message' => 'Variant deleted successfully',
        ]);
    }
}
