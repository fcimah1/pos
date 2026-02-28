<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController
{
    public function store(PaymentRequest $request)
    {
        $validated = $request->validated();

        $branchId = $request->user()?->branch_id ?? 1;
        $openShift = \App\Models\Shift::where('branch_id', $branchId)
            ->where('status', 'open')
            ->latest()
            ->firstOrFail();

        $payment = Payment::create([
            'order_id' => $validated['order_id'],
            'shift_id' => $openShift->id,
            'method' => $validated['method'],
            'amount' => $validated['amount'],
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => 'completed',
            'notes' => $validated['notes'] ?? null,
        ]);

        $order = \App\Models\Order::find($validated['order_id']);
        $totalPaid = $order->payments()->sum('amount');
        $totalPayable = $order->total_amount;

        if ($totalPaid >= $totalPayable) {
            $order->update([
                'status' => 'paid',
                'payment_status' => 'paid',
            ]);
        } else {
            $order->update([
                'payment_status' => 'partial',
            ]);
        }

        return response()->json($payment, 201);
    }

    public function getOrderPayments($orderId, Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        
        $payments = Payment::whereHas('order', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
        ->where('order_id', $orderId)
        ->get();

        return response()->json($payments);
    }
}
