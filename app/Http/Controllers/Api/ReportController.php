<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;

class ReportController
{
    public function daily(Request $request)
    {
        $branchId = $request->user()->branch_id;
        $date = $request->query('date', now()->toDateString());

        $orders = Order::where('branch_id', $branchId)
            ->whereDate('created_at', $date);

        $totalSales = (float)$orders->sum('total_amount');
        $totalTax = (float)$orders->sum('tax_amount');
        $orderCount = $orders->count();

        $paymentBreakdown = $orders->with('payments')
            ->get()
            ->flatMap(fn($o) => $o->payments)
            ->groupBy('method')
            ->map(fn($group) => $group->sum('amount'))
            ->toArray();

        return response()->json([
            'date' => $date,
            'total_sales' => $totalSales,
            'total_tax' => $totalTax,
            'orders_count' => $orderCount,
            'payment_breakdown' => $paymentBreakdown,
        ]);
    }
}
