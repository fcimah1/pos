<?php

namespace App\Services;

use App\Repository\Interface\OrderRepositoryInterface;
use App\DTOs\CreateOrderDTO;
use App\Events\NewOrderEvent;
use App\Models\Branch;
use App\Models\Shift;

class OrderService
{
    public function __construct(private OrderRepositoryInterface $repo) {}

    public function create(CreateOrderDTO $dto)
    {
        $branch = Branch::findOrFail($dto->branch_id);
        
        // البحث عن shift مفتوح أو إنشاء واحد جديد
        $openShift = Shift::where('branch_id', $dto->branch_id)
            ->where('status', 'open')
            ->latest()
            ->first();
        
        if (!$openShift) {
            $openShift = Shift::create([
                'branch_id' => $dto->branch_id,
                'user_id' => $dto->user_id,
                'opening_cash' => 0,
                'opened_at' => now(),
                'status' => 'open',
            ]);
        }

        $subtotal = 0;
        $itemsArray = [];
        foreach ($dto->items as $i) {
            $line = $i->unit_price * $i->quantity;
            $subtotal += $line;
            $itemsArray[] = [
                'product_id' => $i->product_id ?? null,
                'quantity' => $i->quantity,
                'unit_price' => $i->unit_price,
            ];
        }

        $taxAmount = ($subtotal - $dto->discount_amount) * ($branch->tax_rate / 100);
        $totalAmount = $subtotal + $taxAmount - $dto->discount_amount + $dto->delivery_charge;

        $orderData = [
            'branch_id' => $dto->branch_id,
            'user_id' => $dto->user_id,
            'shift_id' => $openShift->id,
            'customer_id' => $dto->customer_id,
            'delivery_address_id' => $dto->delivery_address_id,
            'delivery_person_id' => $dto->delivery_person_id,
            'order_number' => 'ORD-' . time(),
            'type' => $dto->type,
            'table_number' => $dto->table_number,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $dto->discount_amount,
            'delivery_charge' => $dto->delivery_charge,
            'total_amount' => $totalAmount,
            'notes' => $dto->notes,
            'status' => $dto->status ?? 'suspended',
            'payment_status' => $dto->payment_status ?? 'unpaid',
        ];

        $order = $this->repo->create($orderData, $itemsArray);

        NewOrderEvent::dispatch($order);

        return $order;
    }
}
