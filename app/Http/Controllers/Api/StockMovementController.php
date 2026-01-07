<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockMovementRequest;
use App\Models\StockMovement;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['variant.inventory', 'location', 'fromLocation', 'toLocation', 'movedBy']);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by variant
        if ($request->has('variant_id')) {
            $query->where('variant_id', $request->variant_id);
        }
        
        // Filter by location (either from or to)
        if ($request->has('location_id')) {
            $query->where(function($q) use ($request) {
                $q->where('from_location_id', $request->location_id)
                  ->orWhere('to_location_id', $request->location_id)
                  ->orWhere('location_id', $request->location_id);
            });
        }

        $stockMovements = $query->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'data' => $stockMovements,
        ]);
    }

    public function store(StoreStockMovementRequest $request)
    {
        try {
            DB::beginTransaction();

            $variant = Variant::findOrFail($request->variant_id);
            $quantity = $request->quantity;
            $fromLocationId = $request->from_location_id;
            
            // Check stock availability at source location
            $currentStock = $this->getStockAtLocation($request->variant_id, $fromLocationId);
            
            if ($currentStock < $quantity) {
                return response()->json([
                    'message' => 'Insufficient stock at source location.',
                    'current_stock' => $currentStock,
                ], 422);
            }

            // Determine type and status
            $type = $request->to_location_id ? 'transfer' : 'out'; 
            $status = $request->status; // 'pending' for temporary issue, 'completed' for direct transfer

            // Create movement record
            $movement = StockMovement::create([
                'variant_id' => $request->variant_id,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $request->to_location_id,
                'location_id' => $fromLocationId, // Keeping primary location as source for context
                'moved_by' => Auth::id(),
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $request->assign_to_type === 'team' ? 'Issued to Team' : 'Stock Transfer',
                'notes' => $request->notes,
                'status' => $status,
                'due_date' => $request->due_date,
            ]);

            DB::commit();

            return response()->json([
                'data' => $movement->load(['variant', 'fromLocation', 'toLocation', 'movedBy']),
                'message' => 'Stock movement recorded successfully',
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to record movement',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function returnStock(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $originalMovement = StockMovement::findOrFail($id);

            if ($originalMovement->status === 'returned' || $originalMovement->status === 'cancelled') {
                return response()->json(['message' => 'Stock already returned or cancelled'], 422);
            }

            // Create return movement
            // Logic: Reverse of original. From = Original To (or null), To = Original From
            $returnMovement = StockMovement::create([
                'variant_id' => $originalMovement->variant_id,
                'from_location_id' => $originalMovement->to_location_id,
                'to_location_id' => $originalMovement->from_location_id,
                'location_id' => $originalMovement->from_location_id, // Ops happening at original source
                'moved_by' => Auth::id(),
                'type' => 'return',
                'quantity' => $originalMovement->quantity,
                'reason' => 'Return of issued stock',
                'notes' => $request->notes ?? 'Returned from team/location',
                'status' => 'completed',
            ]);

            // Update original movement status
            $originalMovement->update(['status' => 'returned']);

            DB::commit();

            return response()->json([
                'data' => $returnMovement,
                'message' => 'Stock returned successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to return stock',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Calculate stock quantity at a specific location
     * based on movement history.
     */
    private function getStockAtLocation($variantId, $locationId)
    {
        // 1. Sum of IN movements to this location (Stock In)
        $in = StockMovement::where('variant_id', $variantId)
            ->where(function ($q) use ($locationId) {
                $q->where('to_location_id', $locationId)
                  ->orWhere(function($sub) use ($locationId) {
                      $sub->whereNull('from_location_id') // Initial Stock In logic
                          ->where('location_id', $locationId);
                  });
            })
            ->where('status', 'completed')
            ->sum('quantity');

        // 2. Sum of OUT movements from this location (Transfers Out, Issues)
        $out = StockMovement::where('variant_id', $variantId)
            ->where('from_location_id', $locationId)
            ->whereIn('status', ['completed', 'pending']) // Pending issues still reduce available stock
            ->sum('quantity');
            
        // 3. Sum of TRANSFERS IN to this location
        // Should be covered by matching to_location_id in #1 if we handle types correctly?
        // Let's be precise:
        // 'in' type usually means from external. 'transfer' means internal.
        // My query #1 covers 'to_location_id' = locationId. This catches Transfers IN.
        // My query #1 covers 'location_id' = locationId AND 'from_location_id' IS NULL. This catches Initial Stock In.
        
        // Wait, query #1 logic is:
        // (to_location_id = X) OR (from is null AND location_id = X)
        // This is robust.
        
        // Query #2 covers:
        // from_location_id = X.
        // This covers Transfers OUT and Issues (Out).
        
        // What about Returns?
        // Return type: From X to Y. 
        // If From X: it's an Out for X.
        // If To Y: it's an In for Y.
        // My queries verify from_location_id and to_location_id, so it should handle returns automatically.

        return $in - $out;
    }
}
