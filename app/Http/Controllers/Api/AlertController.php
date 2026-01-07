<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Variant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlertController extends Controller
{
    public function index()
    {
        $lowStock = Variant::whereColumn('quantity', '<=', 'min_stock')
            ->with(['inventory'])
            ->get();

        $overdue = StockMovement::overdue()
            ->with(['variant.inventory', 'fromLocation', 'toLocation', 'movedBy'])
            ->get();

        return Inertia::render('Alerts/Index', [
            'lowStock' => $lowStock,
            'overdue' => $overdue,
        ]);
    }
}
