<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockInRequest;
use App\Models\StockMovement;
use App\Models\Variant;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function store(StoreStockInRequest $request)
    {
        try {
            DB::beginTransaction();

            $variant = Variant::findOrFail($request->variant_id);

            // Increment variant quantity
            $variant->increment('quantity', $request->quantity);

            // Create stock movement record
            $stockMovement = StockMovement::create([
                'variant_id' => $request->variant_id,
                'location_id' => $request->location_id,
                'shelf_id' => null,
                'moved_by' => Auth::id(),
                'type' => 'in',
                'quantity' => $request->quantity,
                'reason' => 'Initial Stock In',
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            DB::commit();

            return response()->json([
                'data' => $stockMovement->load(['variant', 'location', 'movedBy']),
                'message' => 'Stock added successfully',
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add stock',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
