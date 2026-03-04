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
        $branchId = $data['branch_id'] ?? 1;
        $order = $this->orderRepo->findById((int)$data['order_id'], $branchId);

        // جلب رقم الوردية من الطلب إذا لم يتم إرساله
        if (!isset($data['shift_id'])) {
            $data['shift_id'] = $order->shift_id;
        }

        $payment = $this->paymentRepo->create($data);

        // تحديث حالة الطلب بناءً على مجموع المدفوعات
        $totalPaid = $this->paymentRepo->getByOrderId($order->id)->sum('amount');

        if ($totalPaid >= $order->total_amount) {
            $this->orderRepo->updateStatus($order->id, 'completed', $branchId);
        }

        return $payment;
    }

    public function getOrderPayments(int $orderId): Collection
    {
        return $this->paymentRepo->getByOrderId($orderId);
    }
}
