<template>
    <div
        class="flex flex-row-reverse h-screen overflow-hidden bg-gray-100 text-right"
        dir="rtl"
    >
        <div
            class="w-1/5 bg-gray-900 text-white flex flex-col border-r border-gray-700 shadow-2xl"
        >
            <div
                class="p-4 border-b border-gray-700 font-bold flex justify-between items-center bg-gray-800"
            >
                <span>⏳ معلق</span>
                <span class="bg-red-500 text-[10px] px-2 py-1 rounded-full">{{
                    suspendedOrders.length
                }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-3 bg-gray-900">
                <div
                    v-for="(order, index) in suspendedOrders"
                    :key="index"
                    @click="openOrder(index)"
                    :class="[
                        'p-3 rounded-xl cursor-pointer hover:bg-gray-700 transition border-r-4 border-yellow-500 shadow-lg',
                        editingIndex === index
                            ? 'bg-gray-700 border-blue-500'
                            : 'bg-gray-800',
                    ]"
                >
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold">{{
                            order.type === "dinein" ? "🍽️ صالة" : "🚚 توصيل"
                        }}</span>
                        <span class="text-[10px] text-gray-400">{{
                            order.time
                        }}</span>
                    </div>
                    <div v-if="order.table_number" class="text-xs text-amber-600 font-bold mb-1">
                        🪑 طاولة {{ order.table_number }}
                    </div>
                    <div class="text-sm font-bold text-blue-300 truncate">
                        {{ order.customer ? order.customer.name : (order.type === "dinein" ? "" : "بدون عميل") }}
                    </div>
                    <div class="text-green-400 font-bold mt-1">
                        {{ order.total }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col z-10 overflow-hidden">
            <!-- شريط أزرار نوع الطلب -->
            <div
                class="bg-white px-3 pt-3 pb-2 shadow-sm flex gap-2 items-center border-b"
            >
                <button
                    @click="setOrderType('dinein')"
                    :class="[
                        'px-5 py-2 rounded-lg font-semibold transition shadow-sm',
                        orderType === 'dinein'
                            ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2'
                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-blue-50 hover:border-blue-400',
                    ]"
                >
                    🍽️ صالة
                </button>
                <button
                    @click="setOrderType('takeaway')"
                    :class="[
                        'px-5 py-2 rounded-lg font-semibold transition shadow-sm',
                        orderType === 'takeaway'
                            ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2'
                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-blue-50 hover:border-blue-400',
                    ]"
                >
                    🥡 تيك أواي
                </button>
                <button
                    @click="setOrderType('delivery')"
                    :class="[
                        'px-5 py-2 rounded-lg font-semibold transition shadow-sm',
                        orderType === 'delivery'
                            ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2'
                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-blue-50 hover:border-blue-400',
                    ]"
                >
                    🚚 توصيل
                </button>
            </div>

            <!-- شريط ثانٍ: بحث الصنف + رقم الطاولة (صالة) / بحث العميل (توصيل) -->
            <div
                class="bg-blue-50 px-3 py-2 border-b border-blue-100 flex gap-3 items-center min-h-[52px]"
            >
                <!-- حقل بحث الصنف — دائم الظهور -->
                <input
                    v-model="productSearch"
                    type="text"
                    placeholder="🔍 بحث عن صنف..."
                    class="bg-black/10 text-black border border-blue-200 rounded-lg px-3 py-2 w-44 outline-none focus:border-blue-500 text-sm"
                />

                <!-- فاصل -->
                <div class="h-6 w-px bg-blue-200"></div>

                <!-- حقل رقم الطاولة للصالة -->
                <template v-if="orderType === 'dinein'">
                    <label class="text-sm font-bold text-gray-700 whitespace-nowrap">🪑 رقم الطاولة *</label>
                    <div class="relative">
                        <select
                            v-model="tableNumber"
                            class="w-44 border border-gray-300 px-3 py-2 pr-8 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none appearance-none bg-white cursor-pointer transition-all text-sm"
                        >
                            <option value="" disabled>اختر الطاولة...</option>
                            <option
                                v-for="table in tables.filter(t => t.is_available)"
                                :key="table.id"
                                :value="table.number"
                                :disabled="table.occupied"
                                :class="table.occupied ? 'text-red-500 bg-red-50' : 'text-green-600'"
                                class="py-1"
                            >
                                🪑 طاولة {{ table.number }}
                                <span v-if="table.occupied"> (مشغولة)</span>
                            </option>
                        </select>
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <div v-if="tableNumber" class="text-xs font-medium">
                        <span v-if="tables.find(t => t.number === tableNumber)?.occupied" class="text-red-600">
                            ⚠️ مشغولة
                        </span>
                        <span v-else class="text-green-600">
                            ✅ طاولة {{ tableNumber }}
                        </span>
                    </div>
                </template>

                <!-- حقل بحث العميل للتوصيل -->
                <template v-if="orderType === 'delivery'">
                    <div class="relative flex-1">
                        <input
                            v-model="customerSearch"
                            type="text"
                            placeholder="البحث برقم/اسم/عنوان العميل..."
                            class="w-full bg-black/10 text-black border border-blue-200 px-3 py-2 rounded-lg outline-none focus:border-blue-500"
                        />
                        <!-- نتائج البحث -->
                        <div
                            v-if="searchResults.length > 0"
                            class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 z-50 max-h-48 overflow-y-auto"
                        >
                            <div
                                v-for="customer in searchResults"
                                :key="customer.phone"
                                @click="selectCustomer(customer)"
                                class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                            >
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800">{{ customer.name }}</span>
                                    <span class="text-sm text-gray-500">{{ customer.phone }}</span>
                                </div>
                                <div v-if="customer.specialMark" class="text-xs text-amber-600 mt-1">
                                    ⭐ {{ customer.specialMark }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ customer.addresses.length }} عنوان/عناوين
                                </div>
                            </div>
                        </div>
                    </div>
                    <button
                        @click="showCustomerModal = true"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition whitespace-nowrap"
                    >
                        + عميل جديد
                    </button>
                    <div
                        v-if="currentCustomer"
                        class="text-sm bg-white border border-blue-300 px-3 py-1 rounded-lg font-bold text-blue-800"
                    >
                        👤 {{ currentCustomer.name }}
                        <span v-if="currentCustomer.selectedAddress" class="block text-xs text-gray-500 mt-1">
                            📍 {{ currentCustomer.selectedAddress }}
                        </span>
                    </div>
                </template>
            </div>



            <div class="flex flex-1 overflow-hidden">
                <div class="w-1/5 bg-white border-l border-gray-200 p-3 space-y-2 overflow-y-auto shadow-sm">
                    <button
                        v-for="(items, cat) in categories"
                        :key="cat"
                        @click="activeCategory = cat"
                        :class="[
                            'w-full py-3 px-4 rounded-xl border-2 transition-all duration-200 font-semibold text-center flex items-center justify-center gap-2',
                            activeCategory === cat
                                ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-200'
                                : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700',
                        ]"
                    >
                        <span v-if="cat === 'meals'">🍽️</span>
                        <span v-if="cat === 'drinks'">🥤</span>
                        <span v-if="cat === 'desserts'">🧁</span>
                        {{ catName(cat) }}
                    </button>
                </div>
                <div
                    class="flex-1 p-4 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 overflow-y-auto content-start bg-gradient-to-br from-gray-50 to-gray-100"
                >
                    <button
                        v-for="item in filteredProducts"
                        :key="item.name"
                        @click="addItem(item)"
                        class="group bg-white rounded-2xl p-4 border border-gray-200 shadow-sm hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 active:scale-95 transition-all duration-200 flex flex-col items-center justify-center gap-2 relative overflow-hidden"
                    >
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        <span class="text-4xl mb-1 transform group-hover:scale-110 transition-transform">{{ item.icon }}</span>
                        <span class="font-bold text-gray-800 text-center leading-tight">{{ item.name }}</span>
                        <span class="text-blue-600 font-bold text-lg bg-blue-50 px-3 py-1 rounded-full">{{ item.price }} ج</span>
                    </button>
                </div>
            </div>
        </div>

        <div
            class="w-1/4 bg-white border-l flex flex-col shadow-xl z-20 overflow-hidden"
        >
            <div
                class="p-4 border-b font-bold text-lg flex justify-between items-center bg-gray-50"
            >
                <span>🧾 الفاتورة</span>
                <span
                    v-if="editingIndex !== null"
                    class="text-[10px] bg-yellow-400 text-black px-2 py-1 rounded-full"
                    >تعديل طلب معلق</span
                >
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <table class="w-full text-sm text-right">
                    <thead>
                        <tr class="border-b text-gray-500">
                            <th class="pb-2">الصنف</th>
                            <th class="pb-2 text-center">الكمية</th>
                            <th class="pb-2 text-center">السعر</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in invoice"
                            :key="index"
                            class="border-b transition hover:bg-gray-50"
                        >
                            <td class="py-3 font-medium text-gray-800">{{ item.name }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        @click="decreaseQty(index)"
                                        class="w-6 h-6 rounded bg-red-100 text-red-600 hover:bg-red-200 font-bold text-sm flex items-center justify-center"
                                    >−</button>
                                    <span class="font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded min-w-[30px] text-center">{{ item.qty }}</span>
                                    <button
                                        @click="increaseQty(index)"
                                        class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 hover:bg-emerald-200 font-bold text-sm flex items-center justify-center"
                                    >+</button>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded-lg">{{ item.qty * item.price }}</span>
                            </td>
                            <td class="text-center">
                                <button
                                    @click="removeItem(index)"
                                    class="text-red-400 font-bold hover:text-red-600"
                                >
                                    ✕
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50 border-t space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">المجموع الفرعي</span>
                    <span class="font-bold text-gray-800">{{ subtotal.toFixed(2) }} ج</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">الضريبة ({{ taxRate }}%)</span>
                    <span class="font-bold text-gray-800">{{ tax.toFixed(2) }} ج</span>
                </div>
                <div
                    class="flex justify-between text-xl font-bold text-blue-700 border-t-2 border-blue-100 pt-3 bg-blue-50 -mx-4 px-4 py-2 rounded-lg"
                >
                    <span>الإجمالي</span>
                    <span class="text-2xl">{{ total.toFixed(2) }} ج</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        @click="suspendOrder"
                        class="bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-amber-200 flex items-center justify-center gap-2"
                    >
                        <span class="text-lg">⏸</span>
                        <span>تعليق</span>
                    </button>
                    <button
                        @click="clearInvoice"
                        class="bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-red-200 flex items-center justify-center gap-2"
                    >
                        <span class="text-lg">🗑</span>
                        <span>إلغاء</span>
                    </button>
                </div>

                <button
                    @click="payOrder"
                    class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white py-4 rounded-xl font-bold text-lg transition shadow-xl shadow-emerald-200 flex items-center justify-center gap-3"
                >
                    <span class="text-2xl">💳</span>
                    <span>دفع</span>
                    <span class="bg-white/20 px-3 py-1 rounded-lg">{{ total.toFixed(2) }} ج</span>
                </button>
            </div>
        </div>

        <!-- popup تسجيل عميل جديد -->
        <div
            v-if="showCustomerModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
            @click.self="showCustomerModal = false"
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4">
                    <h3 class="text-white font-bold text-lg">📝 تسجيل عميل جديد</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">الاسم *</label>
                        <input
                            v-model="newCustomer.name"
                            type="text"
                            placeholder="اسم العميل"
                            class="w-full text-black border border-gray-300 px-3 py-2 rounded-lg focus:border-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">رقم الهاتف *</label>
                        <input
                            v-model="newCustomer.phone"
                            type="text"
                            placeholder="01xxxxxxxxx"
                            class="w-full text-black border border-gray-300 px-3 py-2 rounded-lg focus:border-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">رقم هاتف آخر (اختياري)</label>
                        <input
                            v-model="newCustomer.phone2"
                            type="text"
                            placeholder="01xxxxxxxxx"
                            class="w-full text-black border border-gray-300 px-3 py-2 rounded-lg focus:border-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">العنوان 1 *</label>
                        <input
                            v-model="newCustomer.address1"
                            type="text"
                            placeholder="العنوان الرئيسي"
                            class="w-full text-black border border-gray-300 px-3 py-2 rounded-lg focus:border-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">العنوان 2 (اختياري)</label>
                        <input
                            v-model="newCustomer.address2"
                            type="text"
                            placeholder="عنوان ثانوي"
                            class="w-full text-black border border-gray-300 px-3 py-2 rounded-lg focus:border-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">العنوان 3 (اختياري)</label>
                        <input
                            v-model="newCustomer.address3"
                            type="text"
                            placeholder="عنوان ثالث"
                            class="w-full text-black border border-gray-300 px-3 py-2 rounded-lg focus:border-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">علامة مميزة</label>
                        <input
                            v-model="newCustomer.specialMark"
                            type="text"
                            placeholder="مثال: عميل VIP، باب أحمر"
                            class="w-full text-black border border-amber-300 px-3 py-2 rounded-lg focus:border-amber-500 outline-none bg-amber-50"
                        />
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button
                            @click="showCustomerModal = false"
                            class="flex-1 bg-red-600 text-white py-3 rounded-xl font-bold border border-gray-300 hover:bg-red-400 transition"
                        >
                            إلغاء
                        </button>
                        <button
                            @click="saveNewCustomer"
                            class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition"
                        >
                            حفظ العميل
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- popup اختيار العنوان -->
        <div
            v-if="showAddressSelector"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
            @click.self="showAddressSelector = false"
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 p-4">
                    <h3 class="text-white font-bold text-lg">📍 اختيار عنوان التوصيل</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            v-for="(addr, idx) in selectedCustomerAddresses"
                            :key="idx"
                            @click="confirmAddressSelection(addr)"
                            class="p-4 border-2 rounded-xl cursor-pointer transition hover:border-emerald-500 hover:bg-emerald-50"
                            :class="{ 'border-emerald-500 bg-emerald-50': addr === selectedCustomerAddresses[0] }"
                        >
                            <div class="flex items-start gap-3">
                                <span class="text-2xl">{{ idx === 0 ? '🏠' : idx === 1 ? '🏢' : '📍' }}</span>
                                <div>
                                    <span class="font-bold text-gray-800 block">{{ addr }}</span>
                                    <span v-if="idx === 0" class="text-xs text-emerald-600 font-bold">العنوان الافتراضي</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button
                        @click="showAddressSelector = false"
                        class="w-full mt-6 py-3 rounded-xl font-bold border border-gray-300 hover:bg-gray-50 transition"
                    >
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";

const orderType = ref("dinein");
const tableNumber = ref("");
const tables = ref([]);
const activeCategory = ref("meals");
const API_BASE = "/api";

// المتغيرات الرئيسية
const invoice = ref([]);
const suspendedOrders = ref([]);
const editingIndex = ref(null);
const currentCustomer = ref(null);
const taxRate = ref(10);
const isLoading = ref(false);
const customerSearch = ref("");
const productSearch = ref("");
const searchResults = ref([]);
const selectedCustomerAddresses = ref([]);
const showCustomerModal = ref(false);
const showAddressSelector = ref(false);
const currentEditingCustomer = ref(null);

// بيانات العميل الجديد
const newCustomer = ref({
    name: "",
    phone: "",
    phone2: "",
    address1: "",
    address2: "",
    address3: "",
    specialMark: ""
});

// helper function للـ API calls
const apiCall = async (endpoint, options = {}) => {
    const url = `${API_BASE}${endpoint}`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const authToken = localStorage.getItem('token');

    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
            ...(authToken && { 'Authorization': `Bearer ${authToken}` }),
            ...options.headers
        },
        ...options
    };

    if (options.body && typeof options.body === 'object') {
        defaultOptions.body = JSON.stringify(options.body);
    }

    const response = await fetch(url, defaultOptions);
    
    if (!response.ok) {
        if (response.status === 401) {
            // توكن غير صالح، إعادة توجيه لصفحة تسجيل الدخول
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
            throw new Error('Unauthorized');
        }
        const errorData = await response.json().catch(() => ({ message: 'Unknown error' }));
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
    }
    
    return response.json();
};

// تحميل الطلبات المعلقة من API
const loadTables = async () => {
    try {
        const data = await apiCall('/tables');
        tables.value = data;
    } catch (error) {
        console.error("Error loading tables:", error);
    }
};

const loadSuspendedOrders = async () => {
    try {
        const data = await apiCall('/orders/suspended');
        suspendedOrders.value = data.map(order => ({
            id: order.id,
            type: order.type === 'table' ? 'dinein' : order.type,
            table_number: order.table_number,
            customer: order.customer ? {
                id: order.customer.id,
                name: order.customer.name,
                phone: order.customer.phone,
                specialMark: order.customer.special_mark
            } : null,
            items: order.items.map(item => ({
                id: item.product_id || item.product?.id,
                name: item.product?.name || item.name,
                price: parseFloat(item.unit_price),
                qty: item.quantity,
                icon: "🍽️"
            })),
            total: order.total_amount + " ج",
            time: new Date(order.created_at).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' }),
            status: 'suspended'
        }));
    } catch (error) {
        console.error("Error loading suspended orders:", error);
    }
};

// تحميل المنتجات من API
const loadProducts = async () => {
    try {
        const response = await apiCall('/products');
        const products = response.data || response;
        
        // تجميع المنتجات حسب الفئة
        const grouped = {};
        products.forEach(p => {
            const cat = p.category?.name || '其他';
            if (!grouped[cat]) grouped[cat] = [];
            grouped[cat].push({
                id: p.id,
                name: p.name,
                price: parseFloat(p.price),
                icon: "🍽️"
            });
        });
        
        categories.value = grouped;
    } catch (error) {
        console.error("Error loading products:", error);
    }
};

onMounted(() => {
    loadSuspendedOrders();
    loadProducts();
    loadTables();
}); 

// البحث عن العملاء من API
const searchCustomers = async () => {
    if (!customerSearch.value.trim()) {
        searchResults.value = [];
        return;
    }

    try {
        const data = await apiCall(`/customers/search?q=${encodeURIComponent(customerSearch.value)}`);
        searchResults.value = data;
    } catch (error) {
        console.error("Error searching customers:", error);
    }
};

// watch للبحث
watch(customerSearch, () => {
    searchCustomers();
});

// اختيار عميل من البحث
const selectCustomer = (customer) => {
    selectedCustomerAddresses.value = customer.addresses
        .filter(a => a.address_line_1)
        .map(a => a.address_line_1 + (a.address_line_2 ? " - " + a.address_line_2 : ""));
    showAddressSelector.value = true;
    searchResults.value = [];
    customerSearch.value = "";
};

// تأكيد اختيار العنوان
const confirmAddressSelection = (address) => {
    const foundCustomer = searchResults.value.find(c =>
        c.addresses.some(a => (a.address_line_1 + (a.address_line_2 ? " - " + a.address_line_2 : "")) === address)
    ) || currentEditingCustomer.value;

    if (foundCustomer) {
        currentCustomer.value = {
            id: foundCustomer.id,
            name: foundCustomer.name,
            phone: foundCustomer.phone,
            specialMark: foundCustomer.special_mark,
            selectedAddress: address,
            addressId: foundCustomer.addresses.find(a =>
                (a.address_line_1 + (a.address_line_2 ? " - " + a.address_line_2 : "")) === address
            )?.id
        };
    }
    showAddressSelector.value = false;
};

// حفظ عميل جديد في API
const saveNewCustomer = async () => {
    if (!newCustomer.value.name || !newCustomer.value.phone || !newCustomer.value.address1) {
        alert("الرجاء إدخال الاسم ورقم الهاتف والعنوان 1");
        return;
    }

    isLoading.value = true;

    try {
        const addresses = [];
        if (newCustomer.value.address1) {
            addresses.push({
                address_line_1: newCustomer.value.address1,
                address_line_2: newCustomer.value.address2 || null,
                type: "home"
            });
        }
        if (newCustomer.value.address2 && !addresses.find(a => a.address_line_1 === newCustomer.value.address2)) {
            addresses.push({
                address_line_1: newCustomer.value.address2,
                type: "work"
            });
        }
        if (newCustomer.value.address3) {
            addresses.push({
                address_line_1: newCustomer.value.address3,
                type: "other"
            });
        }

        const savedCustomer = await apiCall('/customers', {
            method: 'POST',
            body: {
                name: newCustomer.value.name,
                phone: newCustomer.value.phone,
                phone2: newCustomer.value.phone2,
                special_mark: newCustomer.value.specialMark,
                addresses: addresses
            }
        });
        currentEditingCustomer.value = savedCustomer;

        // تحميل العميل مباشرة للفاتورة
        currentCustomer.value = {
            id: savedCustomer.id,
            name: savedCustomer.name,
            phone: savedCustomer.phone,
            specialMark: savedCustomer.special_mark,
            selectedAddress: savedCustomer.addresses[0]?.address_line_1 || "",
            addressId: savedCustomer.addresses[0]?.id
        };

        // إعادة تعيين النموذج
        newCustomer.value = {
            name: "",
            phone: "",
            phone2: "",
            address1: "",
            address2: "",
            address3: "",
            specialMark: ""
        };

        showCustomerModal.value = false;
    } catch (error) {
        console.error("Error saving customer:", error);
        alert("حدث خطأ: " + (error.message || "فشل في حفظ العميل"));
    } finally {
        isLoading.value = false;
    }
};

const categories = ref({});

const filteredProducts = computed(() => {
    const q = productSearch.value.trim().toLowerCase();
    if (!q) {
        // بدون بحث: عرض منتجات الفئة النشطة
        return categories.value[activeCategory.value] || [];
    }
    // عند البحث: البحث في كافة المنتجات من جميع الفئات
    return Object.values(categories.value)
        .flat()
        .filter(item => item.name.toLowerCase().includes(q));
});

const catName = (cat) => cat;

const setOrderType = (type) => {
    orderType.value = type;
};

const addItem = (item) => {
    const existing = invoice.value.find((i) => i.name === item.name);
    if (existing) {
        existing.qty++;
    } else {
        invoice.value.push({ ...item, qty: 1 });
    }
};

const removeItem = (index) => {
    invoice.value.splice(index, 1);
};

const increaseQty = (index) => {
    invoice.value[index].qty++;
};

const decreaseQty = (index) => {
    if (invoice.value[index].qty > 1) {
        invoice.value[index].qty--;
    } else {
        removeItem(index);
    }
};

const clearInvoice = () => {
    if (confirm("هل أنت متأكد من إلغاء الطلب؟")) {
        invoice.value = [];
    }
};

const openOrder = (index) => {
    editingIndex.value = index;
    const order = suspendedOrders.value[index];
    if (order && order.items) {
        invoice.value = JSON.parse(JSON.stringify(order.items));
        orderType.value = order.type;
        currentCustomer.value = order.customer;
        tableNumber.value = order.table_number || "";
    }
};

const suspendOrder = async () => {
    if (invoice.value.length === 0) {
        alert("لا يوجد أصناف في الفاتورة");
        return;
    }
    
    // التحقق من رقم الطاولة للصالة
    if (orderType.value === 'dinein') {
        if (!tableNumber.value.trim()) {
            alert("الرجاء إدخال رقم الطاولة");
            return;
        }
        
        // التحقق من أن الطاولة غير مشغولة
        const selectedTable = tables.value.find(t => t.number === tableNumber.value);
        if (selectedTable?.occupied) {
            alert(`طاولة ${tableNumber.value} مشغولة حالياً. الرجاء اختيار طاولة أخرى.`);
            return;
        }
    }

    isLoading.value = true;

    try {
        const orderData = {
            type: orderType.value === 'dinein' ? 'table' : orderType.value,
            table_number: orderType.value === 'dinein' ? tableNumber.value : null,
            customer_id: currentCustomer.value?.id || null,
            delivery_address_id: currentCustomer.value?.addressId || null,
            items: invoice.value.map(item => ({
                    product_id: item.id,
                    unit_price: item.price,
                    quantity: item.qty
                })),
            status: 'suspended',
            payment_status: 'unpaid'
        };
        
        console.log('Order Data:', orderData);
        console.log('Current Customer:', currentCustomer.value);

        if (editingIndex.value !== null) {
            // تحديث الطلب المعلق بالكامل
            const orderId = suspendedOrders.value[editingIndex.value].id;
            const updateData = {
                type: orderType.value === 'dinein' ? 'table' : orderType.value,
                table_number: orderType.value === 'dinein' ? tableNumber.value : null,
                customer_id: currentCustomer.value?.id || null,
                delivery_address_id: currentCustomer.value?.addressId || null,
                items: invoice.value.map(item => ({
                    product_id: item.id,
                    unit_price: item.price,
                    quantity: item.qty
                })),
                status: 'suspended',
                payment_status: 'unpaid'
            };
            
            await apiCall(`/orders/${orderId}`, {
                method: 'POST',
                body: updateData
            });
        } else {
            // إنشاء طلب جديد معلق
            await apiCall('/orders', {
                method: 'POST',
                body: orderData
            });
        }

        // إعادة تحميل الطلبات المعلقة
        await loadSuspendedOrders();
        await loadTables();

        invoice.value = [];
        currentCustomer.value = null;
        editingIndex.value = null;
        tableNumber.value = "";
    } catch (error) {
        console.error("Error suspending order:", error);
        alert("حدث خطأ: " + (error.message || "فشل في تعليق الطلب"));
    } finally {
        isLoading.value = false;
    }
};

const payOrder = async () => {
    if (invoice.value.length === 0) {
        alert("لا يوجد أصناف في الفاتورة");
        return;
    }

    isLoading.value = true;

    try {
        if (editingIndex.value !== null) {
            // تحديث حالة الطلب المعلق إلى مدفوع
            const orderId = suspendedOrders.value[editingIndex.value].id;
            await apiCall(`/orders/${orderId}/status`, {
                method: 'PATCH',
                body: { status: 'paid' }
            });

            // إضافة الدفع
            await apiCall('/payments', {
                method: 'POST',
                body: {
                    order_id: orderId,
                    amount: total.value,
                    method: 'cash',
                    status: 'completed'
                }
            });
        } else {
            // إنشاء طلب جديد مدفوع مباشرة
            const orderData = {
                type: orderType.value === 'dinein' ? 'table' : orderType.value,
                customer_id: currentCustomer.value?.id || null,
                delivery_address_id: currentCustomer.value?.addressId || null,
                items: invoice.value.map(item => ({
                    product_name: item.name,
                    unit_price: item.price,
                    quantity: item.qty
                })),
                status: 'paid',
                payment_status: 'paid'
            };

            const savedOrder = await apiCall('/orders', {
                method: 'POST',
                body: orderData
            });

            // إضافة الدفع
            await apiCall('/payments', {
                method: 'POST',
                body: {
                    order_id: savedOrder.id,
                    amount: total.value,
                    method: 'cash',
                    status: 'completed'
                }
            });
        }

        // إعادة تحميل الطلبات المعلقة
        await loadSuspendedOrders();
        await loadTables();

        alert("تم الدفع بنجاح! الإجمالي: " + total.value.toFixed(2) + " ج");
        invoice.value = [];
        currentCustomer.value = null;
        editingIndex.value = null;
        tableNumber.value = "";
    } catch (error) {
        console.error("Error paying order:", error);
        alert("حدث خطأ: " + (error.message || "فشل في الدفع"));
    } finally {
        isLoading.value = false;
    }
};

const subtotal = computed(() => {
    return invoice.value.reduce((sum, item) => sum + item.price * item.qty, 0);
});

const tax = computed(() => {
    return subtotal.value * (taxRate.value / 100);
});

const total = computed(() => {
    return subtotal.value + tax.value;
});
</script>
