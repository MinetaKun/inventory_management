<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Location::orderBy('name')->get(),
        ]);
    }

    public function store(StoreLocationRequest $request)
    {
        $location = Location::create($request->validated());

        return response()->json([
            'data' => $location,
            'message' => 'Location created successfully',
        ], Response::HTTP_CREATED);
    }

    public function show(Location $location)
    {
        return response()->json([
            'data' => $location,
        ]);
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $location->update($request->validated());

        return response()->json([
            'data' => $location,
            'message' => 'Location updated successfully',
        ]);
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return response()->json([
            'message' => 'Location deleted successfully',
        ]);
    }
}
