<?php

namespace App\Services;

use App\Repository\Interface\PaymentRepositoryInterface;
use App\Repository\Interface\OrderRepositoryInterface;
use App\Models\Payment;
use Illuminate\Support\Collection;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepo,
        private OrderRepositoryInterface $orderRepo
    ) {}

    public function createPayment(array $data): Payment
    {
        $payment = $this->paymentRepo->create($data);

        // تحديث حالة الطلب بناءً على مجموع المدفوعات
        $branchId = $data['branch_id'] ?? 1;
        $order = $this->orderRepo->findById((int)$data['order_id'], $branchId);
        $totalPaid = $this->paymentRepo->getByOrderId($order->id)->sum('amount');

        if ($totalPaid >= $order->total_amount) {
            $this->orderRepo->updateStatus($order->id, 'paid', $branchId);
        }

        return $payment;
    }

    public function getOrderPayments(int $orderId): Collection
    {
        return $this->paymentRepo->getByOrderId($orderId);
    }
}
