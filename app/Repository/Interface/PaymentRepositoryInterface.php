<?php

namespace App\Repository\Interface;

use App\Models\Payment;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;
    public function getByOrderId(int $orderId): Collection;
}
