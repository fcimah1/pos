<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in text-right" dir="rtl">
        <!-- بطاقة إحصائية: إجمالي المبيعات -->
        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg group hover:border-blue-500/50 transition-all shadow-blue-900/10"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-600/10 text-blue-500 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm font-bold font-tajawal">إجمالي المبيعات</h3>
            <p class="text-2xl font-black text-white mt-1">{{ stats.total_sales.toLocaleString() }} <span class="text-xs text-gray-500 font-normal">ج.م</span></p>
        </div>

        <!-- بطاقة إحصائية: إجمالي الطلبات -->
        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg group hover:border-purple-500/50 transition-all shadow-purple-900/10"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-600/10 text-purple-500 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                        />
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm font-bold font-tajawal">إجمالي الطلبات اليوم</h3>
            <p class="text-2xl font-black text-white mt-1">{{ stats.orders_count }} <span class="text-xs text-gray-500 font-normal">طلب</span></p>
        </div>

        <!-- بطاقة إحصائية: المنتجات -->
        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg group hover:border-yellow-500/50 transition-all shadow-yellow-900/10"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-600/10 text-yellow-500 rounded-xl group-hover:bg-yellow-600 group-hover:text-white transition-all">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                        />
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm font-bold font-tajawal">عدد الأصناف</h3>
            <p class="text-2xl font-black text-white mt-1">{{ stats.products_count }} <span class="text-xs text-gray-500 font-normal">صنف</span></p>
        </div>

        <!-- بطاقة إحصائية: العملاء -->
        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg group hover:border-green-500/50 transition-all shadow-green-900/10"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-600/10 text-green-500 rounded-xl group-hover:bg-green-600 group-hover:text-white transition-all">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm font-bold font-tajawal">إجمالي العملاء</h3>
            <p class="text-2xl font-black text-white mt-1">{{ stats.customers_count || 0 }} <span class="text-xs text-gray-500 font-normal">عمیل</span></p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useApi } from "../../composables/useApi";

const { apiCall } = useApi();
const stats = ref({
    total_sales: 0,
    orders_count: 0,
    products_count: 0,
    customers_count: 0
});

onMounted(() => {
    fetchStats();
});

const fetchStats = async () => {
    try {
        // Fetch daily report for current branch
        const report = await apiCall("/reports/daily");
        stats.value.total_sales = report.total_sales;
        stats.value.orders_count = report.orders_count;

        // Fetch counts for products and customers
        const products = await apiCall("/products");
        stats.value.products_count = (products.data || products).length;

        const customers = await apiCall("/customers");
        stats.value.customers_count = (customers.data || customers).length;
    } catch (err) {
        console.error("Error fetching dashboard stats:", err);
    }
};
</script>

