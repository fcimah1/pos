<?php

namespace App\Repository;

use App\Repository\Interface\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $orderData, array $itemsArray): Order
    {
        return DB::transaction(function () use ($orderData, $itemsArray) {
            $order = Order::create($orderData);

            foreach ($itemsArray as $i) {
                $i['order_id'] = $order->id;
                $i['subtotal'] = ((float) $i['unit_price']) * ((int) $i['quantity']);
                OrderItem::create($i);
            }

            return $order->load('items.product');
        });
    }
}
