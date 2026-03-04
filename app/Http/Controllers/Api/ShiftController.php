<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ShiftOpenRequest;
use App\Http\Requests\ShiftCloseRequest;
use App\Services\ShiftService;
use App\Services\ExpenseService;
use App\Events\ShiftClosedEvent;
use Illuminate\Http\Request;
use App\Models\Shift;

class ShiftController
{
    public function __construct(
        private ShiftService $service,
        private ExpenseService $expenseService
    ) {}

    public function openShift(ShiftOpenRequest $request)
    {
        $validated = $request->validated();
        $branchId = $request->user()?->branch_id ?? 1;
        $userId = $request->user()?->id ?? 1;

        $existing = $this->service->getCurrentShift($branchId);
        if ($existing && $existing->user_id === $userId) {
            return response()->json([
                'message' => 'لديك وردية مفتوحة بالفعل',
                'shift' => $existing,
            ], 422);
        }

        $shift = $this->service->openShift($branchId, $userId, (float)$validated['opening_cash']);

        return response()->json($shift, 201);
    }

    public function closeShift(ShiftCloseRequest $request)
    {
        $validated = $request->validated();
        $branchId = $request->user()?->branch_id ?? 1;

        $shift = $this->service->getCurrentShift($branchId);
        if (!$shift) {
            return response()->json(['message' => 'لا توجد وردية مفتوحة حالياً'], 404);
        }

        // حساب المبيعات والبيانات المالية
        $totalSales = $shift->orders()->where('status', 'paid')->sum('total_amount');
        $expenses = $this->expenseService->totalByShift($branchId, $shift->id);
        $expectedCash = $shift->opening_cash + $totalSales - $expenses;
        $closingCash = (float)$validated['closing_cash'];

        $shift = $this->service->closeShift($shift->id, $closingCash);

        // تحديث إضافي للبيانات المالية
        $shift->update([
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
            'variance' => $closingCash - $expectedCash
        ]);

        event(new ShiftClosedEvent($shift));

        return response()->json($shift);
    }

    public function getCurrentShift(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $shift = $this->service->getCurrentShift($branchId);

        if (!$shift) {
            return response()->json(['message' => 'لا توجد وردية مفتوحة'], 404);
        }

        return response()->json($shift);
    }

    public function byDate(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $date = $request->query('date', now()->toDateString());
        $shifts = Shift::where('branch_id', $branchId)
            ->whereDate('opened_at', $date)
            ->orderBy('opened_at', 'asc')
            ->get(['id','opened_at','closed_at','user_id','status','opening_cash','closing_cash']);
        return response()->json($shifts);
    }
}
