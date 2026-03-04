<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Expense;
use App\Models\Shift;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Elibyy\TCPDF\Facades\TCPDF;

class ReportController
{
    public function daily(Request $request)
    {
        $branchId = $request->user()->branch_id;
        $date = $request->query('date', now()->toDateString());
        $shiftId = $request->query('shift_id');

        // إذا تم تمرير shift_id نجمع حسب الوردية، وإلا حسب التاريخ
        if ($shiftId) {
            $orders = Order::where('branch_id', $branchId)
                ->where('shift_id', (int)$shiftId)
                ->where('status', 'completed');
        } else {
            $orders = Order::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->where('status', 'completed');
        }

        $totalSales = (float)$orders->sum('total_amount');
        $totalTax = (float)$orders->sum('tax_amount');
        $orderCount = $orders->count();

        $paymentBreakdown = $orders->with('payments')
            ->get()
            ->flatMap(fn($o) => $o->payments)
            ->groupBy('method')
            ->map(fn($group) => $group->sum('amount'))
            ->toArray();

        // حساب المصاريف: إما حسب shift_id إن تم تمريره، وإلا حسب التاريخ.
        if ($shiftId) {
            $expenses = (float) Expense::where('branch_id', $branchId)
                ->where('shift_id', (int)$shiftId)
                ->sum('amount');
        } else {
            $expenses = (float) Expense::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->sum('amount');
            // في حال لم تُسجّل الطوابع الزمنية (أو اختلاف المنطقة)، نحاول الاعتماد على الوردية المفتوحة في هذا الفرع كحل احتياطي
            if ($expenses == 0) {
                $shift = Shift::where('branch_id', $branchId)
                    ->whereDate('opened_at', $date)
                    ->latest('opened_at')
                    ->first();
                if ($shift) {
                    $fallback = (float) Expense::where('branch_id', $branchId)
                        ->where('shift_id', $shift->id)
                        ->sum('amount');
                    if ($fallback > 0) {
                        $expenses = $fallback;
                    }
                }
            }
        }
        $netCash = $totalSales - $expenses;
        $salesAfterExpenses = $totalSales - $expenses;

        return response()->json([
            'date' => $date,
            'shift_id' => $shiftId ?? null,
            'total_sales' => $totalSales,
            'total_tax' => $totalTax,
            'orders_count' => $orderCount,
            'payment_breakdown' => $paymentBreakdown,
            'expenses' => $expenses,
            'net_cash' => $netCash,
            'sales_after_expenses' => $salesAfterExpenses,
        ]);
    }

    public function dailyPdf(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $date = $request->query('date', now()->toDateString());
        $shiftId = $request->query('shift_id');

        if ($shiftId) {
            $orders = Order::where('branch_id', $branchId)
                ->where('shift_id', (int)$shiftId)
                ->where('status', 'completed');
        } else {
            $orders = Order::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->where('status', 'completed');
        }

        $totalSales = (float)$orders->sum('total_amount');
        $totalTax = (float)$orders->sum('tax_amount');
        $orderCount = $orders->count();
        $paymentBreakdown = $orders->with('payments')
            ->get()
            ->flatMap(fn($o) => $o->payments)
            ->groupBy('method')
            ->map(fn($group) => $group->sum('amount'))
            ->toArray();

        $expenses = (float) Expense::where('branch_id', $branchId)
            ->when($shiftId, fn($q) => $q->where('shift_id', (int)$shiftId), fn($q) => $q->whereDate('created_at', $date))
            ->sum('amount');
        $netCash = $totalSales - $expenses;

        $rows = '';
        foreach ($paymentBreakdown as $m => $a) {
            $rows .= "<tr><td style='padding:4px;border:1px solid #ccc'>".htmlspecialchars($m)."</td><td style='padding:4px;border:1px solid #ccc'>".number_format((float)$a,2)."</td></tr>";
        }
        if ($rows === '') $rows = "<tr><td colspan='2' style='padding:4px;border:1px solid #ccc;color:#666'>لا يوجد مدفوعات</td></tr>";

        // صفحة حرارية عرض 58مم
        TCPDF::setRTL(true);
        TCPDF::setFontSubsetting(true);
        TCPDF::SetFont('aealarabiya', '', 9);
        TCPDF::AddPage('P', [58, 200]);

        $html = "
            <h3 style='text-align:center;font-family:aealarabiya;margin:0;'>تقرير " . ($shiftId ? "الوردية #$shiftId" : "اليوم") . "</h3>
            <div style='font-family:aealarabiya;text-align:right'>
                <div><b>التاريخ:</b> {$date}</div>
                ".($shiftId ? "<div><b>الوردية:</b> #{$shiftId}</div>" : "")."
            </div>
            <table style='width:100%;border-collapse:collapse;font-family:aealarabiya;margin-top:4px' dir='rtl'>
                <tr><td style='padding:4px'>إجمالي المبيعات</td><td style='padding:4px;text-align:left'>".number_format($totalSales,2)." ج.م</td></tr>
                <tr><td style='padding:4px'>الضريبة</td><td style='padding:4px;text-align:left'>".number_format($totalTax,2)." ج.م</td></tr>
                <tr><td style='padding:4px'>عدد الطلبات</td><td style='padding:4px;text-align:left'>{$orderCount}</td></tr>
                <tr><td style='padding:4px'>المصاريف</td><td style='padding:4px;text-align:left'>".number_format($expenses,2)." ج.م</td></tr>
                <tr><td style='padding:4px'>بعد المصاريف</td><td style='padding:4px;text-align:left'>".number_format($netCash,2)." ج.م</td></tr>
            </table>
            <h4 style='font-family:aealarabiya;margin-top:6px;text-align:right'>طرق الدفع</h4>
            <table style='width:100%;border-collapse:collapse;font-family:aealarabiya' dir='rtl'>{$rows}</table>
        ";
        TCPDF::writeHTML($html, true, false, true, false, '');
        $content = TCPDF::Output('daily_report.pdf', 'S');
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="daily_report.pdf"',
        ]);
    }

    public function products(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $period = $request->query('period', 'daily'); // daily|monthly
        $date = $request->query('date', now()->toDateString());
        $month = $request->query('month', now()->format('Y-m'));

        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.branch_id', $branchId)
            ->where('orders.status', 'completed');

        if ($period === 'daily') {
            $query->whereDate('orders.created_at', $date);
        } else {
            $query->whereYear('orders.created_at', substr($month, 0, 4))
                  ->whereMonth('orders.created_at', substr($month, 5, 2));
        }

        $items = $query
            ->selectRaw('order_items.product_id, COALESCE(products.name, CONCAT("منتج #", order_items.product_id)) as product_name, SUM(order_items.quantity) as total_qty, SUM(order_items.subtotal) as total_amount')
            ->groupBy('order_items.product_id', 'products.name')
            ->orderByDesc('total_qty')
            ->get();

        return response()->json($items);
    }

    public function productsPdf(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $period = $request->query('period', 'daily'); // daily|monthly
        $date = $request->query('date', now()->toDateString());
        $month = $request->query('month', now()->format('Y-m'));

        $items = $this->products($request)->getData(true);
        $rows = '';
        foreach ($items as $it) {
            $rows .= "<tr><td style='padding:4px;border:1px solid #ccc'>".htmlspecialchars($it['product_name'])."</td><td style='padding:4px;border:1px solid #ccc'>".$it['total_qty']."</td><td style='padding:4px;border:1px solid #ccc'>".number_format((float)$it['total_amount'],2)."</td></tr>";
        }
        if ($rows === '') $rows = "<tr><td colspan='3' style='padding:4px;border:1px solid #ccc;color:#666'>لا يوجد مبيعات</td></tr>";

        TCPDF::setRTL(true);
        TCPDF::setFontSubsetting(true);
        TCPDF::SetFont('aealarabiya', '', 9);
        TCPDF::AddPage('P', [58, 200]);

        $title = $period === 'daily' ? "تقرير المنتجات اليومية ({$date})" : "تقرير المنتجات الشهرية ({$month})";
        $html = "
            <h4 style='text-align:center;font-family:aealarabiya;margin:0'>{$title}</h4>
            <table style='width:100%;border-collapse:collapse;font-family:aealarabiya;margin-top:6px' dir='rtl'>
                <thead>
                    <tr>
                        <th style='padding:4px;border:1px solid #ccc'>المنتج</th>
                        <th style='padding:4px;border:1px solid #ccc'>الكمية</th>
                        <th style='padding:4px;border:1px solid #ccc'>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>{$rows}</tbody>
            </table>
        ";
        TCPDF::writeHTML($html, true, false, true, false, '');
        $content = TCPDF::Output('products_report.pdf', 'S');
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="products_report.pdf"',
        ]);
    }

    public function shiftPdf(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $shiftId = (int)$request->query('shift_id');
        if (!$shiftId) {
            return response()->json(['message' => 'shift_id is required'], 422);
        }

        $shift = Shift::with('user')->where('branch_id', $branchId)->findOrFail($shiftId);

        $orders = Order::where('branch_id', $branchId)
            ->where('shift_id', $shiftId)
            ->where('status', 'completed');

        $totalSales = (float)$orders->sum('total_amount');
        $totalTax = (float)$orders->sum('tax_amount');
        $orderCount = $orders->count();

        $paymentBreakdown = $orders->with('payments')
            ->get()
            ->flatMap(fn($o) => $o->payments)
            ->groupBy('method')
            ->map(fn($group) => $group->sum('amount'))
            ->toArray();

        $expenses = (float) Expense::where('branch_id', $branchId)
            ->where('shift_id', $shiftId)
            ->sum('amount');
        $netCash = $totalSales - $expenses;

        // تجميع حسب نوع الطلب
        $typeMap = ['takeaway' => 'تيك أواي', 'table' => 'الصالة', 'delivery' => 'التوصيل'];
        $typeSumsRaw = Order::where('branch_id', $branchId)
            ->where('shift_id', $shiftId)
            ->where('status', 'completed')
            ->selectRaw('type, SUM(total_amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
        $typeRows = '';
        foreach ($typeMap as $k => $label) {
            $val = (float)($typeSumsRaw[$k] ?? 0);
            $typeRows .= "<tr><td style=\"padding:6px;border:1px solid #ccc\">{$label}</td><td style=\"padding:6px;border:1px solid #ccc\">".number_format($val,2)."</td></tr>";
        }

        // تجميع المصاريف حسب التصنيف
        $expenseByCat = Expense::where('branch_id', $branchId)
            ->where('shift_id', $shiftId)
            ->selectRaw('COALESCE(category, "غير مصنف") as cat, SUM(amount) as total')
            ->groupBy('cat')
            ->pluck('total', 'cat')
            ->toArray();
        $expenseRows = '';
        foreach ($expenseByCat as $cat => $amt) {
            $expenseRows .= "<tr><td style=\"padding:6px;border:1px solid #ccc\">".htmlspecialchars($cat)."</td><td style=\"padding:6px;border:1px solid #ccc\">".number_format((float)$amt,2)."</td></tr>";
        }
        if ($expenseRows === '') {
            $expenseRows = '<tr><td colspan="2" style="padding:6px;border:1px solid #ccc;color:#666">لا توجد مصاريف</td></tr>';
        }

        $htmlRows = '';
        foreach ($paymentBreakdown as $method => $amount) {
            $htmlRows .= "<tr><td style=\"padding:6px;border:1px solid #ccc\">".htmlspecialchars($method)."</td><td style=\"padding:6px;border:1px solid #ccc\">".number_format((float)$amount, 2)."</td></tr>";
        }
        if ($htmlRows === '') {
            $htmlRows = '<tr><td colspan="2" style="padding:6px;border:1px solid #ccc;color:#666">لا يوجد مدفوعات</td></tr>';
        }

        $openedAt = optional($shift->opened_at)->format('Y-m-d H:i');
        $closedAt = optional($shift->closed_at)->format('Y-m-d H:i') ?? '—';
        $userName = $shift->user->name ?? 'غير محدد';

        $html = "
            <h2 style='text-align:right;font-family:aealarabiya;'>تقرير الوردية #{$shiftId}</h2>
            <div style='text-align:right;font-family:aealarabiya;'>
                <div><b>المستخدم:</b> {$userName}</div>
                <div><b>وقت الفتح:</b> {$openedAt}</div>
                <div><b>وقت الإغلاق:</b> {$closedAt}</div>
            </div>
            <table style='width:100%;border-collapse:collapse;margin-top:10px;font-family:aealarabiya;' dir='rtl'>
                <tr><th style='text-align:right;padding:6px;border:1px solid #ccc'>إجمالي المبيعات</th><td style='padding:6px;border:1px solid #ccc'>".number_format($totalSales,2)." ج.م</td></tr>
                <tr><th style='text-align:right;padding:6px;border:1px solid #ccc'>الضريبة</th><td style='padding:6px;border:1px solid #ccc'>".number_format($totalTax,2)." ج.م</td></tr>
                <tr><th style='text-align:right;padding:6px;border:1px solid #ccc'>عدد الطلبات</th><td style='padding:6px;border:1px solid #ccc'>{$orderCount}</td></tr>
                <tr><th style='text-align:right;padding:6px;border:1px solid #ccc'>المصاريف</th><td style='padding:6px;border:1px solid #ccc'>".number_format($expenses,2)." ج.م</td></tr>
                <tr><th style='text-align:right;padding:6px;border:1px solid #ccc'>المبيعات بعد المصاريف</th><td style='padding:6px;border:1px solid #ccc'>".number_format($netCash,2)." ج.م</td></tr>
            </table>
            <h3 style='text-align:right;margin-top:15px;font-family:aealarabiya;'>تفصيل طرق الدفع</h3>
            <table style='width:100%;border-collapse:collapse;font-family:aealarabiya;' dir='rtl'>
                <thead>
                    <tr>
                        <th style='text-align:right;padding:6px;border:1px solid #ccc'>الطريقة</th>
                        <th style='text-align:right;padding:6px;border:1px solid #ccc'>المبلغ</th>
                    </tr>
                </thead>
                <tbody>{$htmlRows}</tbody>
            </table>
            <h3 style='text-align:right;margin-top:15px;font-family:aealarabiya;'>تفصيل حسب نوع الطلب</h3>
            <table style='width:100%;border-collapse:collapse;font-family:aealarabiya;' dir='rtl'>
                <thead>
                    <tr>
                        <th style='text-align:right;padding:6px;border:1px solid #ccc'>النوع</th>
                        <th style='text-align:right;padding:6px;border:1px solid #ccc'>المبلغ</th>
                    </tr>
                </thead>
                <tbody>{$typeRows}</tbody>
            </table>
            <h3 style='text-align:right;margin-top:15px;font-family:aealarabiya;'>المصاريف حسب التصنيف</h3>
            <table style='width:100%;border-collapse:collapse;font-family:aealarabiya;' dir='rtl'>
                <thead>
                    <tr>
                        <th style='text-align:right;padding:6px;border:1px solid #ccc'>التصنيف</th>
                        <th style='text-align:right;padding:6px;border:1px solid #ccc'>المبلغ</th>
                    </tr>
                </thead>
                <tbody>{$expenseRows}</tbody>
            </table>
        ";

        TCPDF::SetTitle('Shift Report');
        TCPDF::SetAuthor('POS System');
        TCPDF::setRTL(true);
        TCPDF::setFontSubsetting(true);
        // استخدام خط عربي
        TCPDF::SetFont('aealarabiya', '', 12);
        TCPDF::AddPage();
        TCPDF::writeHTML($html, true, false, true, false, '');
        $content = TCPDF::Output('shift_report.pdf', 'S');
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="shift_report.pdf"',
        ]);
    }
}
