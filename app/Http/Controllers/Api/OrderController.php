<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateOrderDTO;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\OrderStatusRequest;
use App\Services\OrderService;

class OrderController
{
    public function store(OrderStoreRequest $request, OrderService $service)
    {
        $data = $request->validated();
        $data['branch_id'] = (int)($request->user()?->branch_id ?? 1);
        $data['user_id'] = (int)($request->user()?->id ?? 1);
        $dto = CreateOrderDTO::fromArray($data);
        $order = $service->create($dto);
        
        // تحديث حالة الطاولة إذا كان طلب صالة
        if ($order->table_number) {
            $this->updateTableAvailability($order->table_number, $order->branch_id);
        }
        
        return response()->json($order, 201);
    }

    public function index(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        
        $orders = Order::where('branch_id', $branchId)
            ->with('items.product', 'customer', 'deliveryPerson')
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->shift_id, function ($query) use ($request) {
                return $query->where('shift_id', $request->shift_id);
            })
            ->latest()
            ->paginate(50);

        return response()->json($orders);
    }

    // جلب الطلبات المعلقة (suspend)
    public function suspended(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        
        $orders = Order::where('branch_id', $branchId)
            ->where('status', 'suspended')
            ->with('items.product', 'customer')
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function show($id, Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        
        $order = Order::where('branch_id', $branchId)
            ->with('items.product', 'customer', 'deliveryPerson', 'payments')
            ->findOrFail($id);

        return response()->json($order);
    }

    public function update(OrderStoreRequest $request, $id)
    {
        Log::info('Updating order', ['id' => $id, 'data' => $request->all()]);
        
        $validated = $request->validated();
        $branchId = $request->user()?->branch_id ?? 1;
        
        $order = Order::where('branch_id', $branchId)->findOrFail($id);
        
        // تحديث بيانات الطلب
        $order->update([
            'customer_id' => $validated['customer_id'] ?? $order->customer_id,
            'delivery_address_id' => $validated['delivery_address_id'] ?? $order->delivery_address_id,
            'delivery_person_id' => $validated['delivery_person_id'] ?? $order->delivery_person_id,
            'notes' => $validated['notes'] ?? $order->notes,
        ]);
        
        // حذف العناصر القديمة وإضافة الجديدة
        $order->items()->delete();
        $subtotal = 0;
        
        foreach ($validated['items'] as $item) {
            $lineTotal = $item['unit_price'] * $item['quantity'];
            $subtotal += $lineTotal;
            
            $order->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $lineTotal,
            ]);
        }
        
        // إعادة حساب المجاميع
        $branch = $order->branch;
        $discountAmount = $validated['discount_amount'] ?? 0;
        $deliveryCharge = $validated['delivery_charge'] ?? 0;
        $taxAmount = ($subtotal - $discountAmount) * ($branch->tax_rate / 100);
        $totalAmount = $subtotal + $taxAmount - $discountAmount + $deliveryCharge;
        
        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'delivery_charge' => $deliveryCharge,
            'total_amount' => $totalAmount,
        ]);
        
        // تحديث حالة الطاولة إذا كان طلب صالة
        if ($order->table_number) {
            $this->updateTableAvailability($order->table_number, $order->branch_id);
        }
        
        return response()->json($order->load('items.product', 'customer'));
    }

    public function updateStatus(OrderStatusRequest $request, $id)
    {
        $validated = $request->validated();

        $branchId = $request->user()?->branch_id ?? 1;
        $order = Order::where('branch_id', $branchId)->findOrFail($id);

        $order->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : $order->completed_at,
        ]);
        
        // تحديث حالة الطاولة إذا كان طلب صالة
        if ($order->table_number) {
            $this->updateTableAvailability($order->table_number, $order->branch_id);
        }

        return response()->json($order);
    }
    
    private function updateTableAvailability($tableNumber, $branchId)
    {
        $hasActiveOrders = Order::where('table_number', $tableNumber)
            ->where('branch_id', $branchId)
            ->whereIn('status', ['open', 'suspended'])
            ->exists();
            
        Table::where('number', $tableNumber)
            ->where('branch_id', $branchId)
            ->update(['is_available' => !$hasActiveOrders]);
    }
}
