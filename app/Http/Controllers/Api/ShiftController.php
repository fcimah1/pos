<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ShiftOpenRequest;
use App\Http\Requests\ShiftCloseRequest;
use App\Models\Shift;
use App\Events\ShiftClosedEvent;
use Illuminate\Http\Request;

class ShiftController
{
    public function openShift(ShiftOpenRequest $request)
    {
        $validated = $request->validated();

        $branchId = $request->user()?->branch_id ?? 1;
        $userId = $request->user()?->id ?? 1;

        $existingShift = Shift::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return response()->json([
                'message' => 'You already have an open shift',
                'shift' => $existingShift,
            ], 422);
        }

        $shift = Shift::create([
            'branch_id' => $branchId,
            'user_id' => $userId,
            'opening_cash' => $validated['opening_cash'],
            'opened_at' => now(),
            'status' => 'open',
        ]);

        return response()->json($shift, 201);
    }

    public function closeShift(ShiftCloseRequest $request)
    {
        $validated = $request->validated();

        $branchId = $request->user()?->branch_id ?? 1;
        $userId = $request->user()?->id ?? 1;

        $shift = Shift::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->firstOrFail();

        $totalSales = $shift->orders()->where('status', 'paid')->sum('total_amount');

        $expectedCash = $shift->opening_cash + $totalSales;
        $closingCash = $validated['closing_cash'];
        $variance = $closingCash - $expectedCash;

        $shift->update([
            'closing_cash' => $closingCash,
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
            'variance' => $variance,
            'closed_at' => now(),
            'status' => 'closed',
        ]);

        event(new ShiftClosedEvent($shift));

        return response()->json($shift);
    }

    public function getCurrentShift(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $userId = $request->user()?->id ?? 1;

        $shift = Shift::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return response()->json(['message' => 'No open shift'], 404);
        }

        return response()->json($shift);
    }
}
