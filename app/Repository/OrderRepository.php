<?php

namespace App\Repository;

use App\DTOs\CreateOrderDTO;
use App\Models\Order;
use App\Models\Table;
use App\Repository\Interface\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAll(int $branchId, array $filters = [])
    {
        return Order::where('branch_id', $branchId)
            ->with(['items.product', 'customer', 'deliveryPerson'])
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['shift_id']), fn($q) => $q->where('shift_id', $filters['shift_id']))
            ->when(isset($filters['customer_id']), fn($q) => $q->where('customer_id', $filters['customer_id']))
            ->latest()
            ->paginate(50);
    }

    public function getSuspended(int $branchId)
    {
        return Order::where('branch_id', $branchId)
            ->where('status', 'suspended')
            ->with(['items.product', 'customer'])
            ->latest()
            ->get();
    }

    public function findById(int $id, int $branchId)
    {
        return Order::where('branch_id', $branchId)
            ->with(['items.product', 'customer', 'deliveryPerson', 'payments'])
            ->findOrFail($id);
    }

    public function create(CreateOrderDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            // توليد رقم الطلب تلقائياً بصيغة ORD-YYYYMMDD-XXXXXX
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'branch_id'           => $dto->branch_id,
                'user_id'             => $dto->user_id,
                'order_number'        => $orderNumber,
                'type'                => $dto->type,
                'table_number'        => $dto->table_number,
                'customer_id'         => $dto->customer_id,
                'delivery_address_id' => $dto->delivery_address_id,
                'delivery_person_id'  => $dto->delivery_person_id,
                'shift_id'            => $dto->shift_id,
                'status'              => $dto->status,
                'payment_status'      => $dto->payment_status,
                'subtotal'            => $dto->subtotal,
                'tax_amount'          => $dto->tax_amount,
                'discount_amount'     => $dto->discount_amount,
                'delivery_charge'     => $dto->delivery_charge,
                'total_amount'        => $dto->total_amount,
                'notes'               => $dto->notes,
            ]);

            foreach ($dto->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);
            }

            if ($order->table_number) {
                $this->updateTableAvailability($order->table_number, $order->branch_id);
            }

            return $order->load('items');
        });
    }

    public function update(int $id, CreateOrderDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $order = Order::where('branch_id', $dto->branch_id)->findOrFail($id);

            $order->update([
                'customer_id' => $dto->customer_id ?? $order->customer_id,
                'delivery_address_id' => $dto->delivery_address_id ?? $order->delivery_address_id,
                'delivery_person_id' => $dto->delivery_person_id ?? $order->delivery_person_id,
                'notes' => $dto->notes ?? $order->notes,
                'type' => $dto->type,
                'table_number' => $dto->table_number,
                'status' => $dto->status,
                'payment_status' => $dto->payment_status,
                'shift_id' => $dto->shift_id ?? $order->shift_id,
                'subtotal' => $dto->subtotal,
                'tax_amount' => $dto->tax_amount,
                'discount_amount' => $dto->discount_amount,
                'delivery_charge' => $dto->delivery_charge,
                'total_amount' => $dto->total_amount,
            ]);

            $order->items()->delete();

            foreach ($dto->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);
            }

            if ($order->table_number) {
                $this->updateTableAvailability($order->table_number, $order->branch_id);
            }

            return $order->load('items.product', 'customer');
        });
    }

    public function updateStatus(int $id, string $status, int $branchId)
    {
        $order = Order::where('branch_id', $branchId)->findOrFail($id);

        $order->update([
            'status' => $status,
            'completed_at' => $status === 'completed' ? now() : $order->completed_at,
        ]);

        if ($order->table_number) {
            $this->updateTableAvailability($order->table_number, $order->branch_id);
        }

        return $order;
    }

    public function updateTableAvailability(?string $tableNumber, int $branchId)
    {
        if (!$tableNumber) return;

        $hasActiveOrders = Order::where('table_number', $tableNumber)
            ->where('branch_id', $branchId)
            ->whereIn('status', ['open', 'suspended'])
            ->exists();

        Table::where('number', $tableNumber)
            ->where('branch_id', $branchId)
            ->update(['is_available' => !$hasActiveOrders]);
    }

    public function settleDriverOrders(int $driverId, array $orderIds): bool
    {
        return Order::where('delivery_person_id', $driverId)
            ->whereIn('id', $orderIds)
            ->update(['is_driver_settled' => true]);
    }
}
