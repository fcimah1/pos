<?php

namespace App\Http\Controllers\Api;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController
{
    public function __construct(private PaymentService $service) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string',
            'status' => 'required|string',
        ]);

        $payment = $this->service->createPayment($validated);
        return response()->json($payment, 201);
    }

    public function getOrderPayments($orderId)
    {
        return response()->json($this->service->getOrderPayments((int)$orderId));
    }
}
