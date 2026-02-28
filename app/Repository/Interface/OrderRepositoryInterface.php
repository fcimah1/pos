<?php

namespace App\Repository\Interface;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $orderData, array $itemsArray): Order;
}
