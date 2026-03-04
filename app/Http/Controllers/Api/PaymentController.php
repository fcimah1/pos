<?php

namespace App\Http\Controllers\Api;

use App\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class PaymentController
{
    use ApiResponseTrait;

    public function __construct(private PaymentService $service) {}

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'amount' => 'required|numeric|min:0',
                'method' => 'required|string',
                'status' => 'required|string',
            ]);

            $validated['branch_id'] = $request->user()?->branch_id ?? 1;

            $payment = $this->service->createPayment($validated);
            return $this->successResponse($payment, 'تم تسجيل الدفع بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تسجيل عملية الدفع');
        }
    }

    public function getOrderPayments($orderId)
    {
        try {
            $payments = $this->service->getOrderPayments((int)$orderId);
            return $this->successResponse($payments);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب مدفوعات الطلب');
        }
    }
}
