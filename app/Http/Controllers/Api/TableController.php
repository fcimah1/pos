<?php

namespace App\Http\Controllers\Api;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController
{
    public function index(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        
        $tables = Table::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with(['orders' => function ($query) {
                $query->whereIn('status', ['open', 'suspended']);
            }])
            ->get()
            ->map(function ($table) {
                $occupied = $table->orders->isNotEmpty();
                return [
                    'id' => $table->id,
                    'number' => $table->number,
                    'capacity' => $table->capacity,
                    'is_available' => $table->is_available && !$occupied,
                    'occupied' => $occupied,
                    'current_order' => $table->orders->first(),
                ];
            });

        return response()->json($tables);
    }
}
