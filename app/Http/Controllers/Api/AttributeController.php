<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\Response;

class AttributeController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Attribute::orderBy('name')->get(),
        ]);
    }

    public function store(StoreAttributeRequest $request)
    {
        $attribute = Attribute::create($request->validated());
        return response()->json([
            'data' => $attribute,
            'message' => 'Attribute created successfully',
        ], Response::HTTP_CREATED);
    }

    public function show(Attribute $attribute)
    {
        return response()->json([
            'data' => $attribute,
        ]);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $attribute->update($request->validated());
        return response()->json([
            'data' => $attribute,
            'message' => 'Attribute updated successfully',
        ]);
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return response()->json([
            'message' => 'Attribute deleted successfully',
        ]);
    }
}
