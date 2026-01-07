<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShelfRequest;
use App\Http\Requests\UpdateShelfRequest;
use App\Models\Shelf;
use Illuminate\Http\Response;

class ShelfController extends Controller
{
    public function index()
    {
        $query = Shelf::with(['location', 'category']);

        if (request('location_id')) {
            $query->where('location_id', request('location_id'));
        }

        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        return response()->json([
            'data' => $query->orderBy('location_id')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreShelfRequest $request)
    {
        $shelf = Shelf::create($request->validated());

        return response()->json([
            'data' => $shelf->load(['location', 'category']),
            'message' => 'Shelf created successfully',
        ], Response::HTTP_CREATED);
    }

    public function show(Shelf $shelf)
    {
        return response()->json([
            'data' => $shelf->load(['location', 'category']),
        ]);
    }

    public function update(UpdateShelfRequest $request, Shelf $shelf)
    {
        $shelf->update($request->validated());

        return response()->json([
            'data' => $shelf->load(['location', 'category']),
            'message' => 'Shelf updated successfully',
        ]);
    }

    public function destroy(Shelf $shelf)
    {
        $shelf->delete();

        return response()->json([
            'message' => 'Shelf deleted successfully',
        ]);
    }
}
