<?php

namespace App\Repository;

use App\Models\Payment;
use App\Repository\Interface\PaymentRepositoryInterface;
use Illuminate\Support\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function getByOrderId(int $orderId): Collection
    {
        return Payment::where('order_id', $orderId)->latest()->get();
    }
}
