<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use App\Models\AttributeValue;
use Illuminate\Http\Response;

class AttributeValueController extends Controller
{
    public function index()
    {
        $query = AttributeValue::with('attribute');
        if (request('attribute_id')) {
            $query->where('attribute_id', request('attribute_id'));
        }
        return response()->json([
            'data' => $query->orderBy('value')->get(),
        ]);
    }

    public function store(StoreAttributeValueRequest $request)
    {
        $value = AttributeValue::create($request->validated());
        return response()->json([
            'data' => $value->load('attribute'),
            'message' => 'Attribute value created successfully',
        ], Response::HTTP_CREATED);
    }

    public function show(AttributeValue $attributeValue)
    {
        return response()->json([
            'data' => $attributeValue->load('attribute'),
        ]);
    }

    public function update(UpdateAttributeValueRequest $request, AttributeValue $attributeValue)
    {
        $attributeValue->update($request->validated());
        return response()->json([
            'data' => $attributeValue->load('attribute'),
            'message' => 'Attribute value updated successfully',
        ]);
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();
        return response()->json([
            'message' => 'Attribute value deleted successfully',
        ]);
    }
}
