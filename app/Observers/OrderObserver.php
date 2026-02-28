<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Table;

class OrderObserver
{
    public function created(Order $order): void
    {
        // تحديث حالة الطاولة عند إنشاء طلب جديد
        if ($order->table_number) {
            $this->updateTableAvailability($order);
        }
    }

    public function updated(Order $order): void
    {
        // تحديث حالة الطاولة عند تحديث الطلب
        if ($order->table_number) {
            $this->updateTableAvailability($order);
        }
    }

    public function deleted(Order $order): void
    {
        // تحديث حالة الطاولة عند حذف الطلب
        if ($order->table_number) {
            $this->updateTableAvailability($order);
        }
    }

    private function updateTableAvailability(Order $order): void
    {
        // التحقق من وجود طلبات أخرى مفتوحة أو معلقة على نفس الطاولة
        $hasActiveOrders = Order::where('table_number', $order->table_number)
            ->where('branch_id', $order->branch_id)
            ->whereIn('status', ['open', 'suspended'])
            ->where('id', '!=', $order->id)
            ->exists();

        // تحديث حالة الطاولة
        Table::where('number', $order->table_number)
            ->where('branch_id', $order->branch_id)
            ->update(['is_available' => !$hasActiveOrders]);
    }
}
