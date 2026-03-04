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

        $totals = $this->calculateTotals($dto->items, $branch, $dto->discount_amount, $dto->delivery_charge);

        $orderData = array_merge($dto->toArray(), [
            'shift_id'     => $openShift->id,
            'order_number' => 'ORD-' . time(),
            'subtotal'       => $totals['subtotal'],
            'tax_amount'     => $totals['tax_amount'],
            'total_amount'   => $totals['total_amount'],
            'status'         => $dto->status ?? 'suspended',
            'payment_status' => $dto->payment_status ?? 'unpaid',
        ]);

        // تحويل الطلبات التي تُنشأ مدفوعة إلى مكتملة، خاصةً للتيك أواي
        if (($dto->type === 'takeaway' && ($dto->payment_status === 'paid' || $dto->status === 'paid' || $dto->status === 'completed'))
            || ($orderData['payment_status'] === 'paid' && $orderData['status'] !== 'suspended')) {
            $orderData['status'] = 'completed';
            $orderData['payment_status'] = 'paid';
        }

        $finalDto = CreateOrderDTO::fromArray($orderData);
        $order = $this->repo->create($finalDto);

        NewOrderEvent::dispatch($order);

        return $order;
    }

    public function update(int $id, CreateOrderDTO $dto)
    {
        $branch = Branch::findOrFail($dto->branch_id);
        $totals = $this->calculateTotals($dto->items, $branch, $dto->discount_amount, $dto->delivery_charge);

        $orderData = array_merge($dto->toArray(), [
            'subtotal'     => $totals['subtotal'],
            'tax_amount'   => $totals['tax_amount'],
            'total_amount' => $totals['total_amount'],
        ]);

        $finalDto = CreateOrderDTO::fromArray($orderData);
        return $this->repo->update($id, $finalDto);
    }

    private function calculateTotals(array $items, Branch $branch, float $discount = 0, float $delivery = 0): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item->unit_price * $item->quantity);
        }

        $discount = (float)$discount;
        $delivery = (float)$delivery;
        $taxRate = (float)($branch->tax_rate ?? 14);

        $taxableAmount = max(0, $subtotal - $discount);
        $taxAmount = $taxableAmount * ($taxRate / 100);
        $totalAmount = $taxableAmount + $taxAmount + $delivery;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ];
    }

    public function settleDriverOrders(int $driverId, array $orderIds): bool
    {
        return $this->repo->settleDriverOrders($driverId, $orderIds);
    }
}
