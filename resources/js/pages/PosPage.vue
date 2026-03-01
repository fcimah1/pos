<template>
    <div
        class="flex flex-col h-screen overflow-hidden bg-gray-100 text-right"
        dir="rtl"
    >
        <PosHeader
            :current-path="route.path"
            @go-to="goTo"
            @logout="logout"
            @open-driver-settlement="showDriverSettlement = true"
        />

        <div class="flex flex-row-reverse flex-1 overflow-hidden">
            <!-- الشريط الجانبي للطلبات المعلقة -->
            <div
                class="w-1/5 bg-gray-900 text-white flex flex-col border-r border-gray-700 shadow-2xl"
            >
                <div
                    class="p-4 border-b border-gray-700 font-bold flex justify-between items-center bg-gray-800"
                >
                    <span>⏳ معلق</span>
                    <span
                        class="bg-red-500 text-[10px] px-2 py-1 rounded-full"
                        >{{ suspendedOrders.length }}</span
                    >
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
                        <div
                            v-if="order.table_number"
                            class="text-xs text-amber-600 font-bold mb-1"
                        >
                            🪑 طاولة {{ order.table_number }}
                        </div>
                        <div class="text-sm font-bold text-blue-300 truncate">
                            {{
                                order.customer?.name ||
                                (order.type === "dinein" ? "" : "بدون عميل")
                            }}
                        </div>
                        <div class="text-green-400 font-bold mt-1">
                            {{ order.total }}
                        </div>
                    </div>
                </div>
            </div>

            <ProductGrid
                v-model:order-type="orderType"
                v-model:active-category="activeCategory"
                :categories="categories"
                :products="filteredProducts"
                @add-item="addItem"
            >
                <template #top-bar>
                    <div
                        class="bg-white px-4 py-3 border-b border-gray-200 flex flex-wrap gap-4 items-center shadow-sm"
                    >
                        <!-- البحث عن منتج -->
                        <div class="relative group flex-1 min-w-[200px]">
                            <span
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                >🔍</span
                            >
                            <input
                                v-model="productSearch"
                                type="text"
                                placeholder="ابحث عن صنف أو باركود..."
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl pr-10 pl-4 py-2.5 outline-none focus:border-blue-500 focus:bg-white transition-all text-sm font-bold shadow-inner font-tajawal text-gray-800 placeholder-gray-400"
                            />
                        </div>

                        <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

                        <!-- خيارات الصالة -->
                        <template v-if="orderType === 'dinein'">
                            <div
                                class="flex items-center gap-3 bg-blue-50/50 px-4 py-1.5 rounded-2xl border border-blue-100 shadow-sm"
                            >
                                <label
                                    class="text-sm font-black text-blue-800 whitespace-nowrap"
                                    >🪑 رقم الطاولة:</label
                                >
                                <div class="relative">
                                    <select
                                        v-model="tableNumber"
                                        class="bg-white border-2 border-blue-200 px-4 py-2 rounded-xl text-sm font-black outline-none focus:border-blue-500 shadow-sm appearance-none cursor-pointer pr-4 pl-10 min-w-[120px] text-gray-800"
                                    >
                                        <option value="" disabled class="text-gray-400">
                                            اختر الطاولة...
                                        </option>
                                        <option
                                            v-for="table in tables.filter(
                                                (t) => t.is_available,
                                            )"
                                            :key="table.id"
                                            :value="table.number"
                                            class="text-gray-800"
                                        >
                                            طاولة {{ table.number }}
                                        </option>
                                    </select>
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 pointer-events-none"
                                        >▼</span
                                    >
                                </div>
                            </div>
                        </template>

                        <!-- خيارات التوصيل -->
                        <template v-if="orderType === 'delivery'">
                            <div
                                class="flex flex-wrap items-center gap-4 flex-[2]"
                            >
                                <!-- اختيار الطيار -->
                                <div
                                    class="flex items-center gap-2 bg-indigo-50 px-3 py-2 rounded-2xl border border-indigo-100 shadow-sm transition-all hover:shadow-md"
                                >
                                    <span
                                        class="text-sm font-black text-indigo-700 whitespace-nowrap"
                                        >🛵 الطيار:</span
                                    >
                                    <div class="relative group">
                                        <select
                                            v-model="selectedDriverId"
                                            class="bg-white border-2 border-indigo-200 px-3 py-1.5 rounded-xl text-xs font-black outline-none focus:border-indigo-500 shadow-sm cursor-pointer appearance-none pl-8 pr-2 min-w-[100px] text-indigo-900"
                                        >
                                            <option :value="null">
                                                لم يحدد
                                            </option>
                                            <option
                                                v-for="driver in drivers"
                                                :key="driver.id"
                                                :value="driver.id"
                                            >
                                                {{ driver.name }}
                                            </option>
                                        </select>
                                        <span
                                            class="absolute left-2 top-1/2 -translate-y-1/2 text-indigo-400 text-[10px] pointer-events-none"
                                            >▼</span
                                        >
                                    </div>
                                </div>

                                <!-- البحث عن عميل -->
                                <div
                                    class="relative flex-1 group min-w-[250px]"
                                >
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition-colors"
                                        >👤</span
                                    >
                                    <input
                                        v-model="customerSearch"
                                        type="text"
                                        placeholder="البحث برقم/اسم العميل..."
                                        class="w-full bg-emerald-50/30 border-2 border-emerald-100 px-10 py-2.5 rounded-xl outline-none focus:border-emerald-500 focus:bg-white transition-all text-sm font-bold shadow-inner"
                                    />
                                    <div
                                        v-if="searchResults.length > 0"
                                        class="absolute top-full right-0 left-0 bg-white border border-gray-200 mt-2 rounded-[1.5rem] shadow-2xl z-[100] overflow-hidden animate-slide-up border-t-4 border-t-emerald-500"
                                    >
                                        <div
                                            v-for="customer in searchResults"
                                            :key="customer.phone"
                                            @click="selectCustomer(customer)"
                                            class="p-4 hover:bg-emerald-50 cursor-pointer border-b last:border-0 transition-all text-sm group/item"
                                        >
                                            <div
                                                class="font-black text-gray-800 group-hover/item:text-emerald-700"
                                            >
                                                {{ customer.name }}
                                            </div>
                                            <div
                                                class="text-xs text-gray-500 group-hover/item:text-emerald-600 font-mono"
                                            >
                                                {{ customer.phone }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- أزرار التحكم بالعميل -->
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="showCustomerModal = true"
                                        class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-5 py-2.5 rounded-2xl font-black text-xs shadow-lg shadow-emerald-200 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 whitespace-nowrap"
                                    >
                                        <span>➕</span>
                                        <span>جديد</span>
                                    </button>

                                    <div
                                        v-if="currentCustomer"
                                        class="flex items-center gap-3 bg-white border-2 border-blue-500 px-4 py-2 rounded-2xl shadow-xl animate-bounce-in min-w-[150px]"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[10px] text-gray-400 font-black leading-none uppercase tracking-tighter mb-1"
                                                >العميل الحالي</span
                                            >
                                            <span
                                                class="font-black text-blue-900 text-sm whitespace-nowrap"
                                                >{{
                                                    currentCustomer.name
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="flex gap-2 mr-auto border-r pr-3 border-gray-100"
                                        >
                                            <button
                                                @click="
                                                    fetchCustomerOrders(
                                                        currentCustomer.id,
                                                    )
                                                "
                                                class="bg-amber-100 hover:bg-amber-200 p-1.5 rounded-xl transition-all"
                                                title="سجل الطلبات"
                                            >
                                                📜
                                            </button>
                                            <button
                                                @click="currentCustomer = null"
                                                class="bg-red-100 hover:bg-red-200 p-1.5 rounded-xl transition-all text-red-600"
                                                title="إزالة العميل"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </ProductGrid>

            <InvoicePanel
                :invoice="invoice"
                :is-editing="editingIndex !== null"
                :subtotal="subtotal"
                :tax="tax"
                :tax-rate="taxRate"
                :total="total"
                @increase-qty="increaseQty"
                @decrease-qty="decreaseQty"
                @remove-item="removeItem"
                @suspend="suspendOrder"
                @clear="clearInvoice"
                @pay="payOrder"
            />
        </div>

        <!-- مودالات -->
        <OrderHistoryModal
            :show="showOrderHistory"
            :orders="customerOrders"
            :loading="isLoading"
            @close="showOrderHistory = false"
        />

        <DriverSettlementModal
            :show="showDriverSettlement"
            :drivers="drivers"
            v-model:selected-driver-id="selectedDriverId"
            :orders="driverSettlementOrders"
            :loading="isLoading"
            @close="showDriverSettlement = false"
            @settle="settleOrders"
        />

        <!-- مودال إضافة عميل جديد -->
        <div
            v-if="showCustomerModal"
            class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm p-4 text-sm"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 bounce-in"
            >
                <div
                    class="bg-blue-600 p-4 text-white font-bold flex justify-between items-center"
                >
                    <span>👤 إضافة عميل جديد</span>
                    <button
                        @click="showCustomerModal = false"
                        class="hover:bg-blue-700 rounded-full w-8 h-8 flex items-center justify-center transition"
                    >
                        ✕
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-1"
                                >الاسم *</label
                            >
                            <input
                                v-model="newCustomer.name"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-1"
                                >الموبايل *</label
                            >
                            <input
                                v-model="newCustomer.phone"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1"
                            >الموبايل 2 (اختياري)</label
                        >
                        <input
                            v-model="newCustomer.phone2"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-bold"
                            >العناوين</label
                        >
                        <input
                            v-model="newCustomer.address1"
                            type="text"
                            placeholder="العنوان الرئيسي (منطقة - شارع - عمارة - شقة) *"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                        <input
                            v-model="newCustomer.address2"
                            type="text"
                            placeholder="عنوان فرعي 1"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                        <input
                            v-model="newCustomer.address3"
                            type="text"
                            placeholder="عنوان فرعي 2"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1"
                            >علامة مميزة</label
                        >
                        <textarea
                            v-model="newCustomer.specialMark"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        ></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 flex gap-3">
                    <button
                        @click="saveNewCustomer"
                        class="flex-1 bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition"
                    >
                        ✅ حفظ العميل
                    </button>
                    <button
                        @click="showCustomerModal = false"
                        class="px-6 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition"
                    >
                        إلغاء
                    </button>
                </div>
            </div>
        </div>

        <!-- مودال اختيار العنوان -->
        <div
            v-if="showAddressSelector"
            class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm p-4"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 bounce-in"
            >
                <div
                    class="bg-amber-500 p-4 text-white font-bold flex justify-between items-center text-sm"
                >
                    <span>📍 اختيار عنوان التوصيل</span>
                    <button
                        @click="showAddressSelector = false"
                        class="hover:bg-amber-600 rounded-full w-8 h-8 flex items-center justify-center transition"
                    >
                        ✕
                    </button>
                </div>
                <div class="p-6 space-y-3">
                    <p class="text-sm text-gray-600 mb-4">
                        لقد تم العثور على أكثر من عنوان للعميل، يرجى اختيار وجهة
                        التوصيل:
                    </p>
                    <button
                        v-for="(addr, idx) in selectedCustomerAddresses"
                        :key="idx"
                        @click="confirmAddressSelection(addr)"
                        class="w-full p-4 border-2 border-gray-100 rounded-xl text-right hover:border-amber-500 hover:bg-amber-50 transition font-bold text-sm"
                    >
                        🏠 {{ addr }}
                    </button>
                </div>
                <div class="bg-gray-50 p-4">
                    <button
                        @click="showAddressSelector = false"
                        class="w-full py-2 text-gray-500 font-bold hover:text-gray-700 transition text-sm"
                    >
                        إغلاق
                    </button>
                </div>
            </div>
        </div>

        <!-- مودال اختيار الحجم/النوع (Variations) -->
        <div
            v-if="selectedProductForVariation"
            class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm p-4"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 bounce-in"
            >
                <div
                    class="bg-blue-600 p-4 text-white font-bold flex justify-between items-center text-sm"
                >
                    <span
                        >📐 اختر الحجم:
                        {{ selectedProductForVariation.name }}</span
                    >
                    <button
                        @click="selectedProductForVariation = null"
                        class="hover:bg-blue-700 rounded-full w-8 h-8 flex items-center justify-center transition"
                    >
                        ✕
                    </button>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <button
                        v-for="variation in selectedProductForVariation.variations"
                        :key="variation.id"
                        @click="confirmVariation(variation)"
                        class="p-4 border-2 border-gray-100 rounded-xl text-center hover:border-blue-500 hover:bg-blue-50 transition group"
                    >
                        <div
                            class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition"
                        >
                            {{ variation.size_name }}
                        </div>
                        <div class="text-lg font-black text-green-600">
                            {{ variation.price }} ج
                        </div>
                    </button>
                </div>
                <div class="bg-gray-50 p-4">
                    <button
                        @click="selectedProductForVariation = null"
                        class="w-full py-2 text-gray-500 font-bold hover:text-gray-700 transition text-sm"
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
import { useRouter, useRoute } from "vue-router";
import Swal from "sweetalert2";

import PosHeader from "../components/pos/PosHeader.vue";
import ProductGrid from "../components/pos/ProductGrid.vue";
import InvoicePanel from "../components/pos/InvoicePanel.vue";
import OrderHistoryModal from "../components/pos/OrderHistoryModal.vue";
import DriverSettlementModal from "../components/pos/DriverSettlementModal.vue";

const router = useRouter();
const route = useRoute();

const API_BASE = "/api";
const isLoading = ref(false);

// الحالات الرئيسية
const orderType = ref("dinein");
const tableNumber = ref("");
const tables = ref([]);
const invoice = ref([]);
const suspendedOrders = ref([]);
const editingIndex = ref(null);
const currentCustomer = ref(null);
const taxRate = ref(10);

// البحث
const customerSearch = ref("");
const productSearch = ref("");
const searchResults = ref([]);
const activeCategory = ref("");
const categories = ref({});

// المودالات
const showCustomerModal = ref(false);
const showAddressSelector = ref(false);
const showOrderHistory = ref(false);
const showDriverSettlement = ref(false);
const customerOrders = ref([]);
const selectedCustomerAddresses = ref([]);

// الطيارون والحسابات
const drivers = ref([]);
const selectedDriverId = ref(null);
const driverSettlementOrders = ref([]);

// دوال الـ API
const apiCall = async (endpoint, options = {}) => {
    const url = `${API_BASE}${endpoint}`;
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;
    const authToken = localStorage.getItem("token");

    const defaultOptions = {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            ...(csrfToken && { "X-CSRF-TOKEN": csrfToken }),
            ...(authToken && { Authorization: `Bearer ${authToken}` }),
            ...options.headers,
        },
        ...options,
    };

    if (options.body && typeof options.body === "object") {
        defaultOptions.body = JSON.stringify(options.body);
    }

    const response = await fetch(url, defaultOptions);

    if (!response.ok) {
        if (response.status === 401) {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            window.location.href = "/login";
            throw new Error("Unauthorized");
        }
        const errorData = await response
            .json()
            .catch(() => ({ message: "Unknown error" }));
        throw new Error(
            errorData.message || `HTTP error! status: ${response.status}`,
        );
    }

    return response.json();
};

const loadTables = async () => {
    try {
        const resp = await apiCall("/tables");
        // دعم شكل الرد الجديد {status, message, data} والشكل القديم
        tables.value = resp.data ?? resp;
    } catch (error) {
        console.error("Error loading tables:", error);
    }
};

const loadSuspendedOrders = async () => {
    try {
        const resp = await apiCall("/orders/suspended");
        // دعم شكل الرد الجديد {status, message, data} والشكل القديم
        const orders = Array.isArray(resp) ? resp : (resp.data ?? []);
        suspendedOrders.value = orders.map((order) => ({
            id: order.id,
            type: order.type === "table" ? "dinein" : order.type,
            table_number: order.table_number,
            customer: order.customer
                ? {
                      id: order.customer.id,
                      name: order.customer.name,
                      phone: order.customer.phone,
                      specialMark: order.customer.special_mark,
                      addressId: order.delivery_address_id,
                  }
                : null,
            items: order.items.map((item) => ({
                id: item.product_id,
                name: item.product_name,
                price: parseFloat(item.unit_price),
                qty: item.quantity,
            })),
            total: order.total_amount + " ج",
            time: new Date(order.created_at).toLocaleTimeString("ar-EG", {
                hour: "2-digit",
                minute: "2-digit",
            }),
            status: "suspended",
        }));
    } catch (error) {
        console.error("Error loading suspended orders:", error);
    }
};

const loadProducts = async () => {
    try {
        const response = await apiCall("/products");
        const products = response.data || response;
        const grouped = {};
        products.forEach((p) => {
            const cat = p.category?.name || "أخرى";
            if (!grouped[cat]) grouped[cat] = [];

            let defaultPrice = 0;
            if (p.variations && p.variations.length > 0) {
                defaultPrice = Math.min(
                    ...p.variations.map((v) => parseFloat(v.price)),
                );
            } else {
                defaultPrice = parseFloat(p.price || 0);
            }

            grouped[cat].push({
                id: p.id,
                name: p.name,
                price: defaultPrice,
                icon: p.icon || "🍛",
                variations: p.variations || [],
            });
        });
        categories.value = grouped;
        if (!activeCategory.value && Object.keys(grouped).length > 0) {
            activeCategory.value = Object.keys(grouped)[0];
        }
    } catch (error) {
        console.error("Error loading products:", error);
    }
};

const fetchDrivers = async () => {
    try {
        const resp = await apiCall("/delivery-persons");
        drivers.value = Array.isArray(resp) ? resp : (resp.data ?? []);
    } catch (err) {
        console.error("Error fetching drivers:", err);
    }
};

onMounted(() => {
    loadSuspendedOrders();
    loadProducts();
    loadTables();
    fetchDrivers();
});

// البحث عن العملاء
const searchCustomers = async () => {
    if (!customerSearch.value.trim()) {
        searchResults.value = [];
        return;
    }
    try {
        const data = await apiCall(
            `/customers/search?q=${encodeURIComponent(customerSearch.value)}`,
        );
        searchResults.value = data;
    } catch (error) {
        console.error("Error searching customers:", error);
    }
};

watch(customerSearch, searchCustomers);

const selectCustomer = (customer) => {
    selectedCustomerAddresses.value = customer.addresses
        .filter((a) => a.address_line_1)
        .map(
            (a) =>
                a.address_line_1 +
                (a.address_line_2 ? " - " + a.address_line_2 : ""),
        );

    currentEditingCustomer.value = customer;
    if (selectedCustomerAddresses.value.length > 1) {
        showAddressSelector.value = true;
    } else if (selectedCustomerAddresses.value.length === 1) {
        confirmAddressSelection(selectedCustomerAddresses.value[0]);
    } else {
        currentCustomer.value = {
            id: customer.id,
            name: customer.name,
            phone: customer.phone,
            specialMark: customer.special_mark,
            selectedAddress: null,
            addressId: null,
        };
    }
    customerSearch.value = "";
    searchResults.value = [];
};

const currentEditingCustomer = ref(null);

const confirmAddressSelection = (address) => {
    const customer = currentEditingCustomer.value;
    if (customer) {
        currentCustomer.value = {
            id: customer.id,
            name: customer.name,
            phone: customer.phone,
            specialMark: customer.special_mark,
            selectedAddress: address,
            addressId: customer.addresses.find(
                (a) =>
                    a.address_line_1 +
                        (a.address_line_2 ? " - " + a.address_line_2 : "") ===
                    address,
            )?.id,
        };
    }
    showAddressSelector.value = false;
};

const newCustomer = ref({
    name: "",
    phone: "",
    phone2: "",
    address1: "",
    address2: "",
    address3: "",
    specialMark: "",
});
const saveNewCustomer = async () => {
    if (
        !newCustomer.value.name ||
        !newCustomer.value.phone ||
        !newCustomer.value.address1
    ) {
        Swal.fire({
            icon: "warning",
            title: "بيانات ناقصة",
            text: "الرجاء إدخال الاسم ورقم الهاتف والعنوان الرئيسي",
            confirmButtonText: "حسناً",
            confirmButtonColor: "#3b82f6",
        });
        return;
    }
    isLoading.value = true;
    try {
        const addresses = [
            { address_line_1: newCustomer.value.address1, type: "home" },
        ];
        if (newCustomer.value.address2)
            addresses.push({
                address_line_1: newCustomer.value.address2,
                type: "work",
            });
        if (newCustomer.value.address3)
            addresses.push({
                address_line_1: newCustomer.value.address3,
                type: "other",
            });

        const saved = await apiCall("/customers", {
            method: "POST",
            body: {
                name: newCustomer.value.name,
                phone: newCustomer.value.phone,
                phone2: newCustomer.value.phone2,
                special_mark: newCustomer.value.specialMark,
                addresses: addresses,
            },
        });
        currentCustomer.value = {
            id: saved.id,
            name: saved.name,
            phone: saved.phone,
            specialMark: saved.special_mark,
            selectedAddress: saved.addresses[0]?.address_line_1,
            addressId: saved.addresses[0]?.id,
        };
        showCustomerModal.value = false;
        newCustomer.value = {
            name: "",
            phone: "",
            phone2: "",
            address1: "",
            address2: "",
            address3: "",
            specialMark: "",
        };
    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "فشل الحفظ",
            text: err.message,
            confirmButtonText: "حسناً",
        });
    } finally {
        isLoading.value = false;
    }
};

// إدارة الفاتورة
const filteredProducts = computed(() => {
    const q = productSearch.value.trim().toLowerCase();
    if (!q) return categories.value[activeCategory.value] || [];
    return Object.values(categories.value)
        .flat()
        .filter((p) => p.name.toLowerCase().includes(q));
});

const selectedProductForVariation = ref(null);
const addItem = (item) => {
    if (item.variations && item.variations.length > 1) {
        selectedProductForVariation.value = item;
    } else {
        const v = item.variations?.[0];
        confirmVariation(v || { price: item.price, size_name: "عادي" }, item);
    }
};

const confirmVariation = (variation, product = null) => {
    const item = product || selectedProductForVariation.value;
    const nameToInvoice =
        item.name +
        (variation.size_name !== "عادي" ? ` (${variation.size_name})` : "");
    const existing = invoice.value.find((i) => i.name === nameToInvoice);
    if (existing) {
        existing.qty++;
    } else {
        invoice.value.push({
            id: item.id,
            name: nameToInvoice,
            price: parseFloat(variation.price),
            qty: 1,
        });
    }
    selectedProductForVariation.value = null;
};

const removeItem = (index) => invoice.value.splice(index, 1);
const increaseQty = (index) => invoice.value[index].qty++;
const decreaseQty = (index) => {
    if (invoice.value[index].qty > 1) invoice.value[index].qty--;
    else removeItem(index);
};
const clearInvoice = () => {
    Swal.fire({
        title: "هل أنت متأكد؟",
        text: "سيتم حذف جميع الأصناف من الفاتورة!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "نعم، تفريغ الكل",
        cancelButtonText: "إلغاء",
    }).then((result) => {
        if (result.isConfirmed) {
            invoice.value = [];
        }
    });
};

const openOrder = (index) => {
    editingIndex.value = index;
    const order = suspendedOrders.value[index];
    invoice.value = JSON.parse(JSON.stringify(order.items));
    orderType.value = order.type;
    currentCustomer.value = order.customer;
    tableNumber.value = order.table_number || "";
};

const suspendOrder = async () => {
    if (invoice.value.length === 0) {
        return Swal.fire({
            icon: "info",
            title: "تنبيه",
            text: "الفاتورة فارغة",
            confirmButtonText: "حسناً",
        });
    }
    if (orderType.value === "dinein" && !tableNumber.value) {
        return Swal.fire({
            icon: "warning",
            title: "تنبيه",
            text: "الرجاء اختيار الطاولة أولاً",
            confirmButtonText: "حسناً",
            confirmButtonColor: "#f59e0b",
        });
    }

    isLoading.value = true;
    try {
        const orderData = {
            type: orderType.value === "dinein" ? "table" : orderType.value,
            table_number:
                orderType.value === "dinein" ? tableNumber.value : null,
            customer_id: currentCustomer.value?.id,
            delivery_address_id: currentCustomer.value?.addressId,
            items: invoice.value.map((i) => ({
                product_id: i.id,
                unit_price: i.price,
                quantity: i.qty,
            })),
            status: "suspended",
            payment_status: "unpaid",
        };

        if (editingIndex.value !== null) {
            await apiCall(
                `/orders/${suspendedOrders.value[editingIndex.value].id}`,
                { method: "POST", body: orderData },
            );
        } else {
            await apiCall("/orders", { method: "POST", body: orderData });
        }
        await loadSuspendedOrders();
        await loadTables();
        invoice.value = [];
        currentCustomer.value = null;
        editingIndex.value = null;
        tableNumber.value = "";
    } catch (err) {
        alert(err.message);
    } finally {
        isLoading.value = false;
    }
};

const payOrder = async () => {
    if (invoice.value.length === 0) {
        return Swal.fire({
            icon: "info",
            title: "تنبيه",
            text: "الفاتورة فارغة",
            confirmButtonText: "حسناً",
        });
    }
    isLoading.value = true;
    try {
        let orderId;
        if (editingIndex.value !== null) {
            orderId = suspendedOrders.value[editingIndex.value].id;
            await apiCall(`/orders/${orderId}/status`, {
                method: "PATCH",
                body: { status: "paid" },
            });
        } else {
            const orderData = {
                type: orderType.value === "dinein" ? "table" : orderType.value,
                customer_id: currentCustomer.value?.id,
                delivery_address_id: currentCustomer.value?.addressId,
                items: invoice.value.map((i) => ({
                    product_id: i.id,
                    unit_price: i.price,
                    quantity: i.qty,
                })),
                status: "paid",
                payment_status: "paid",
            };
            const saved = await apiCall("/orders", {
                method: "POST",
                body: orderData,
            });
            orderId = saved.id;
        }

        await apiCall("/payments", {
            method: "POST",
            body: {
                order_id: orderId,
                amount: total.value,
                method: "cash",
                status: "completed",
            },
        });

        await loadSuspendedOrders();
        await loadTables();

        Swal.fire({
            icon: "success",
            title: "تم الدفع بنجاح",
            text: "تم إتمام العملية بنجاح",
            confirmButtonText: "ممتاز",
            confirmButtonColor: "#10b981",
        });

        invoice.value = [];
        currentCustomer.value = null;
        editingIndex.value = null;
        tableNumber.value = "";
    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "خطأ في العملية",
            text: err.message,
        });
    } finally {
        isLoading.value = false;
    }
};

const fetchCustomerOrders = async (id) => {
    isLoading.value = true;
    try {
        const resp = await apiCall(`/orders?customer_id=${id}`);
        customerOrders.value = resp.data || resp;
        showOrderHistory.value = true;
    } catch (err) {
        console.error(err);
    } finally {
        isLoading.value = false;
    }
};

const settleOrders = async (id, ids) => {
    isLoading.value = true;
    try {
        await apiCall("/orders/settle", {
            method: "POST",
            body: { driver_id: id, order_ids: ids },
        });
        Swal.fire({
            icon: "success",
            title: "تمت التسوية بنجاح",
            timer: 1500,
            showConfirmButton: false,
        });
        showDriverSettlement.value = false;
    } catch (err) {
        Swal.fire({ icon: "error", title: "خطأ", text: err.message });
    } finally {
        isLoading.value = false;
    }
};

const subtotal = computed(() =>
    invoice.value.reduce((s, i) => s + i.price * i.qty, 0),
);
const tax = computed(() => subtotal.value * (taxRate.value / 100));
const total = computed(() => subtotal.value + tax.value);

const logout = () => {
    localStorage.removeItem("token");
    router.push("/login");
};
const goTo = (p) => router.push(p);
</script>

<style scoped>
.bounce-in {
    animation: bounceIn 0.3s ease-out;
}
@keyframes bounceIn {
    0% {
        transform: scale(0.9);
        opacity: 0;
    }
    70% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
