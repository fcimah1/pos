<template>
    <div class="flex flex-col h-screen overflow-hidden bg-gray-900 text-right font-tajawal" dir="rtl">
        <!-- الهيدر الموحد -->
        <PosHeader 
            :current-path="route.path"
            @go-to="goTo" 
            @logout="logout" 
            @open-driver-settlement="showDriverSettlement = true" 
            @open-expenses="openExpenses"
            @open-add-expense="() => { selectedExpenseId = null; initialExpense = null; showExpenseModal = true }"
            @open-daily-report="openDailyReport"
            @current-shift-report="currentShiftReport"
            @export-shift-pdf="exportShiftPdf"
            @export-daily-pdf="exportDailyPdf"
            @export-products-pdf="exportProductsPdf"
            @switch-shift="switchShift"
        />

        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <div class="max-w-7xl mx-auto">
                <router-view></router-view>
            </div>
        </div>

        <!-- مودال تسوية الطيارين (يجب أن يكون متاحاً من الهيدر في كل الصفحات) -->
        <DriverSettlementModal 
            :show="showDriverSettlement"
            :drivers="drivers"
            v-model:selected-driver-id="selectedDriverId"
            :orders="driverSettlementOrders"
            :loading="isLoadingSettlement"
            @close="showDriverSettlement = false"
            @settle="settleOrders"
        />

        <ExpenseListModal
            :show="showExpenseList"
            :expenses="expenses"
            :loading="isLoadingExpense"
            @close="showExpenseList = false"
            @add="() => { selectedExpenseId = null; initialExpense = null; showExpenseModal = true }"
            @edit="startEditExpense"
        />

        <ExpenseModal
            :show="showExpenseModal"
            :initial="initialExpense"
            @close="showExpenseModal = false"
            @save="saveExpense"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useApi } from "../composables/useApi";
import { usePermissionService } from "../composables/usePermissionService";
import PosHeader from "./pos/PosHeader.vue";
import DriverSettlementModal from "./pos/DriverSettlementModal.vue";
import ExpenseListModal from "./pos/ExpenseListModal.vue";
import ExpenseModal from "./pos/ExpenseModal.vue";

const router = useRouter();
const route = useRoute();
const { apiCall, Swal } = useApi();

const showDriverSettlement = ref(false);
const drivers = ref([]);
const selectedDriverId = ref(null);
const driverSettlementOrders = ref([]);
const isLoadingSettlement = ref(false);

// Expense state
const showExpenseList = ref(false);
const showExpenseModal = ref(false);
const expenses = ref([]);
const isLoadingExpense = ref(false);
const currentShiftId = ref(null);
const selectedExpenseId = ref(null);
const initialExpense = ref(null);

const goTo = (path) => router.push(path);
const logout = () => {
    localStorage.removeItem("token");
    router.push("/login");
};

const fetchDrivers = async () => {
    try {
        const resp = await apiCall("/delivery-persons");
        drivers.value = resp.data || resp;
    } catch (err) {
        console.error("Error fetching drivers:", err);
    }
};

const settleOrders = async (id, ids) => {
    isLoadingSettlement.value = true;
    try {
        await apiCall("/orders/settle", { method: "POST", body: { driver_id: id, order_ids: ids } });
        alert("تمت التسوية بنجاح");
        showDriverSettlement.value = false;
    } catch (err) {
        alert(err.message);
    } finally {
        isLoadingSettlement.value = false;
    }
};

onMounted(() => {
    fetchDrivers();
});

const openExpenses = async () => {
    await loadExpenses();
    showExpenseList.value = true;
};

const loadExpenses = async () => {
    isLoadingExpense.value = true;
    try {
        const shiftResp = await apiCall("/shifts/current");
        currentShiftId.value = shiftResp.id || shiftResp?.data?.id || null;
        const resp = await apiCall(`/expenses${currentShiftId.value ? `?shift_id=${currentShiftId.value}` : ''}`);
        const list = Array.isArray(resp) ? resp : (resp.data ?? []);
        expenses.value = list;
    } catch (err) {
        console.error("Error loading expenses:", err);
    } finally {
        isLoadingExpense.value = false;
    }
};

const startEditExpense = (exp) => {
    selectedExpenseId.value = exp.id;
    initialExpense.value = exp;
    showExpenseModal.value = true;
};

const saveExpense = async (payload) => {
    isLoadingExpense.value = true;
    try {
        if (selectedExpenseId.value) {
            await apiCall(`/expenses/${selectedExpenseId.value}`, { method: 'PATCH', body: payload });
        } else {
            await apiCall('/expenses', { method: 'POST', body: payload });
        }
        showExpenseModal.value = false;
        await loadExpenses();
    } catch (err) {
        // handled in useApi
    } finally {
        isLoadingExpense.value = false;
    }
};

const openDailyReport = async () => {
    try {
        const { value: date } = await Swal.fire({
            title: 'اختر التاريخ',
            input: 'date',
            inputValue: new Date().toISOString().slice(0,10),
            confirmButtonText: 'عرض',
            cancelButtonText: 'إلغاء',
            showCancelButton: true
        });
        if (!date) return;
        const shifts = await apiCall(`/shifts/by-date?date=${date}`);
        const shiftList = shifts.data || shifts || [];
        let selectedShiftId = null;
        if (Array.isArray(shiftList) && shiftList.length > 0) {
            const inputOptions = {};
            shiftList.forEach(s => {
                const open = new Date(s.opened_at).toLocaleTimeString('ar-EG', {hour:'2-digit', minute:'2-digit'});
                const closed = s.closed_at ? new Date(s.closed_at).toLocaleTimeString('ar-EG', {hour:'2-digit', minute:'2-digit'}) : '—';
                inputOptions[s.id] = `من ${open} إلى ${closed} (${s.status})`;
            });
            const { value: shiftId } = await Swal.fire({
                title: 'اختر الوردية',
                input: 'select',
                inputOptions,
                inputPlaceholder: 'اختر الوردية',
                showCancelButton: true,
                confirmButtonText: 'عرض'
            });
            if (shiftId === undefined) return;
            selectedShiftId = shiftId || null;
        }
        const url = selectedShiftId ? `/reports/daily?date=${date}&shift_id=${selectedShiftId}` : `/reports/daily?date=${date}`;
            const report = await apiCall(url);
            const data = report.data || report;
            const breakdown = data.payment_breakdown || {};
            const breakdownHtml = Object.entries(breakdown)
                .map(([k,v]) => `<div><b>${k}:</b> ${parseFloat(v).toLocaleString()} ج.م</div>`)
                .join('');
            await Swal.fire({
            title: selectedShiftId ? 'مبيعات الوردية' : 'مبيعات اليوم',
            html: `
                <div dir="rtl" style="text-align:right">
                    <div><b>التاريخ:</b> ${data.date}</div>
                    ${selectedShiftId ? `<div><b>الوردية:</b> #${selectedShiftId}</div>` : ''}
                    <div><b>إجمالي المبيعات:</b> ${parseFloat(data.total_sales).toLocaleString()} ج.م</div>
                    <div><b>الضريبة:</b> ${parseFloat(data.total_tax).toLocaleString()} ج.م</div>
                    <div><b>عدد الطلبات:</b> ${data.orders_count}</div>
                    <div><b>المصاريف:</b> ${parseFloat(data.expenses || 0).toLocaleString()} ج.م</div>
                    <div><b>المبيعات بعد المصاريف:</b> ${parseFloat(data.sales_after_expenses ?? data.net_cash).toLocaleString()} ج.م</div>
                        <hr style="margin:8px 0;border:none;border-top:1px dashed #ddd" />
                        <div><b>تفصيل طرق الدفع:</b></div>
                        ${breakdownHtml || '<div style="color:#888">لا يوجد مدفوعات</div>'}
                </div>
            `,
            confirmButtonText: 'حسناً'
        });
    } catch (e) {
        Swal.fire({icon:'error', title:'خطأ', text:e.message});
    }
};

const switchShift = async () => {
    try {
        const { value: closingCashStr } = await Swal.fire({
            title: 'إغلاق الوردية الحالية',
            input: 'number',
            inputLabel: 'المبلغ الموجود فعلياً بالصندوق',
            inputAttributes: { min: 0, step: 0.01 },
            confirmButtonText: 'إغلاق',
            cancelButtonText: 'إلغاء',
            showCancelButton: true
        });
        if (closingCashStr === undefined) return;
        const closingCash = parseFloat(closingCashStr || '0');
        await apiCall('/shifts/close', { method: 'POST', body: { closing_cash: closingCash } });

        const { value: openingCashStr } = await Swal.fire({
            title: 'فتح وردية جديدة',
            input: 'number',
            inputLabel: 'رصيد الافتتاح',
            inputValue: closingCash.toFixed(2),
            inputAttributes: { min: 0, step: 0.01 },
            confirmButtonText: 'فتح',
            cancelButtonText: 'إلغاء',
            showCancelButton: true
        });
        if (openingCashStr === undefined) return;
        const openingCash = parseFloat(openingCashStr || '0');
        await apiCall('/shifts/open', { method: 'POST', body: { opening_cash: openingCash } });

        Swal.fire({icon:'success', title:'تم تبديل الوردية', timer:1500, showConfirmButton:false});
    } catch (e) {
        Swal.fire({icon:'error', title:'خطأ', text:e.message});
    }
};

const currentShiftReport = async () => {
    try {
        const shiftResp = await apiCall('/shifts/current');
        const current = shiftResp.id || shiftResp?.data?.id;
        const date = new Date().toISOString().slice(0,10);
        const url = current ? `/reports/daily?date=${date}&shift_id=${current}` : `/reports/daily?date=${date}`;
        const report = await apiCall(url);
        const data = report.data || report;
        const breakdown = data.payment_breakdown || {};
        const breakdownHtml = Object.entries(breakdown)
            .map(([k,v]) => `<div><b>${k}:</b> ${parseFloat(v).toLocaleString()} ج.م</div>`)
            .join('');
        await Swal.fire({
            title: current ? 'مبيعات الوردية الحالية' : 'مبيعات اليوم',
            html: `
                <div dir="rtl" style="text-align:right">
                    <div><b>التاريخ:</b> ${data.date}</div>
                    ${current ? `<div><b>الوردية:</b> #${current}</div>` : ''}
                    <div><b>إجمالي المبيعات:</b> ${parseFloat(data.total_sales).toLocaleString()} ج.م</div>
                    <div><b>الضريبة:</b> ${parseFloat(data.total_tax).toLocaleString()} ج.م</div>
                    <div><b>عدد الطلبات:</b> ${data.orders_count}</div>
                    <div><b>المصاريف:</b> ${parseFloat(data.expenses || 0).toLocaleString()} ج.م</div>
                    <div><b>المبيعات بعد المصاريف:</b> ${parseFloat(data.sales_after_expenses ?? data.net_cash).toLocaleString()} ج.م</div>
                    <hr style="margin:8px 0;border:none;border-top:1px dashed #ddd" />
                    <div><b>تفصيل طرق الدفع:</b></div>
                    ${breakdownHtml || '<div style="color:#888">لا يوجد مدفوعات</div>'}
                </div>
            `,
            confirmButtonText: 'حسناً'
        });
    } catch (e) {
        Swal.fire({icon:'error', title:'خطأ', text:e.message});
    }
};

const exportShiftPdf = async () => {
    try {
        const shiftResp = await apiCall('/shifts/current');
        const current = shiftResp.id || shiftResp?.data?.id;
        if (!current) {
            return Swal.fire({icon:'info', title:'لا توجد وردية مفتوحة'});
        }
        const url = `/api/reports/shift/pdf?shift_id=${current}`;
        window.open(url, '_blank');
    } catch (e) {
        Swal.fire({icon:'error', title:'خطأ', text:e.message});
    }
};

const exportDailyPdf = async () => {
    try {
        const { value: date } = await Swal.fire({
            title: 'اختر التاريخ',
            input: 'date',
            inputValue: new Date().toISOString().slice(0,10),
            showCancelButton: true
        });
        if (!date) return;
        window.open(`/api/reports/daily/pdf?date=${date}`, '_blank');
    } catch (e) {
        Swal.fire({icon:'error', title:'خطأ', text:e.message});
    }
};

const exportProductsPdf = async () => {
    try {
        const { value: choice } = await Swal.fire({
            title: 'نوع التقرير',
            input: 'select',
            inputOptions: { daily: 'يومي', monthly: 'شهري' },
            inputPlaceholder: 'اختر النوع',
            showCancelButton: true,
            confirmButtonText: 'التالي'
        });
        if (!choice) return;
        if (choice === 'daily') {
            const { value: date } = await Swal.fire({
                title: 'اختر اليوم',
                input: 'date',
                inputValue: new Date().toISOString().slice(0,10),
                showCancelButton: true
            });
            if (!date) return;
            window.open(`/api/reports/products/pdf?period=daily&date=${date}`, '_blank');
        } else {
            const { value: month } = await Swal.fire({
                title: 'اختر الشهر',
                html: '<input id="monthInput" type="month" class="swal2-input" style="width:auto">',
                focusConfirm: false,
                preConfirm: () => document.getElementById('monthInput').value,
                showCancelButton: true
            });
            if (!month) return;
            window.open(`/api/reports/products/pdf?period=monthly&month=${month}`, '_blank');
        }
    } catch (e) {
        Swal.fire({icon:'error', title:'خطأ', text:e.message});
    }
};
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>
