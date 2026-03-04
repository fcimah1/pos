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
            @open-expenses="openExpenses"
            @open-add-expense="showExpenseModal = true"
            @open-daily-report="openDailyReport"
            @current-shift-report="currentShiftReport"
            @export-shift-pdf="exportShiftPdf"
            @export-daily-pdf="exportDailyPdf"
            @export-products-pdf="exportProductsPdf"
            @switch-shift="switchShift"
        />

        <div class="flex flex-row-reverse flex-1 overflow-visible min-h-0">
            <!-- الشريط الجانبي للطلبات المعلقة -->
            <div
                class="w-1/5 bg-gray-900 text-white flex flex-col border-r border-gray-700 shadow-2xl"
            >
                <div
                    class="p-3 border-b border-gray-700 font-bold flex justify-between items-center bg-gray-800 gap-2"
                >
                    <div class="flex items-center gap-2">
                        <span>⏳ معلق</span>
                        <span class="text-[10px] text-gray-400">({{ suspendedOrders.length }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            class="px-2 py-1 rounded-lg text-[11px] font-black"
                            :class="suspendedFilter === 'all' ? 'bg-gray-700 text-white' : 'bg-gray-900 text-gray-300 border border-gray-700'"
                            @click="suspendedFilter = 'all'"
                        >
                            الكل
                        </button>
                        <button
                            class="px-2 py-1 rounded-lg text-[11px] font-black"
                            :class="suspendedFilter === 'preparing' ? 'bg-indigo-600 text-white' : 'bg-gray-900 text-gray-300 border border-gray-700'"
                            @click="suspendedFilter = 'preparing'"
                        >
                            تحت التجهيز
                        </button>
                        <button
                            class="px-2 py-1 rounded-lg text-[11px] font-black"
                            :class="suspendedFilter === 'delivering' ? 'bg-purple-600 text-white' : 'bg-gray-900 text-gray-300 border border-gray-700'"
                            @click="suspendedFilter = 'delivering'"
                        >
                            يتم توصيله
                        </button>
                        <span class="bg-red-500 text-[10px] px-2 py-1 rounded-full">{{ filteredSuspendedOrders.length }}</span>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-3 space-y-3 bg-gray-900">
                    <div
                        v-for="(order, index) in filteredSuspendedOrders"
                        :key="index"
                        @click="openOrder(order.id)"
                        :class="[
                            'p-3 rounded-xl cursor-pointer transition shadow-lg backdrop-blur-[1px]',
                            editingIndex === index
                                ? 'bg-gray-700 border-blue-500'
                                : 'bg-gray-800',
                            order.type === 'delivery' && order.status === 'preparing'
                                ? 'border-r-4 border-indigo-500 ring-2 ring-indigo-500/20 hover:ring-indigo-500/40 hover:bg-indigo-900/30'
                                : (order.type === 'delivery' && order.status === 'delivering'
                                    ? 'border-r-4 border-purple-500 ring-2 ring-purple-500/20 hover:ring-purple-500/40 hover:bg-purple-900/30'
                                    : 'border-r-4 border-yellow-500 hover:bg-gray-700')
                        ]"
                    >
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold">{{
                                order.type === "dinein" ? "🍽️ صالة" : "🚚 توصيل"
                            }}</span>
                            <span
                                class="text-sm font-black tracking-wide"
                                :class="(order.type === 'delivery' && order.status === 'delivering') 
                                    ? 'text-purple-300' 
                                    : (order.ageMinutes >= 30 
                                        ? 'text-red-600' 
                                        : (order.ageMinutes >= 20 
                                            ? 'text-amber-500' 
                                            : 'text-gray-400'))"
                            >
                                #{{ order.order_number }} | {{ order.time }}
                            </span>
                        </div>
                        <div
                            v-if="order.type === 'delivery' && ['suspended','preparing','delivering'].includes(order.status)"
                            class="mb-2"
                        >
                            <span
                                :class="order.status === 'delivering'
                                    ? 'bg-purple-600 text-white shadow-purple-500/30'
                                    : (order.status === 'preparing'
                                        ? 'bg-indigo-600 text-white shadow-indigo-500/30'
                                        : 'bg-amber-600 text-black shadow-amber-500/30')"
                                class="text-[11px] font-extrabold px-2.5 py-1 rounded-full inline-flex items-center gap-1 shadow-lg"
                            >
                                <span
                                    class="w-2 h-2 rounded-full animate-ping bg-white"
                                ></span>
                                <span>
                                    {{
                                        order.status === 'delivering'
                                            ? ('🛵 يتم توصيله' + (order.driverName ? ' • ' + order.driverName : ''))
                                            : '🧑‍🍳 تحت التجهيز'
                                    }}
                                </span>
                            </span>
                            <div v-if="order.status !== 'delivering'" class="mt-1 h-1.5 bg-gray-700/50 rounded-full overflow-hidden">
                                <div
                                    :class="order.status === 'preparing'
                                        ? 'bg-gradient-to-r from-indigo-400 via-indigo-500 to-indigo-400'
                                        : 'bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400'"
                                    class="h-full w-full animate-pulse"
                                ></div>
                            </div>
                            <div
                                v-if="['suspended','preparing'].includes(order.status)"
                                class="mt-2 flex items-center gap-2"
                                @click.stop
                            >
                                <select
                                    v-model="selectedDriverFor[order.id]"
                                    class="bg-gray-900/60 border border-gray-700 text-xs text-gray-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                                >
                                    <option :value="null">اختر المندوب...</option>
                                    <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
                                </select>
                                <button
                                    class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-600 hover:bg-indigo-500 active:scale-95 transition text-white disabled:opacity-50"
                                    :disabled="!selectedDriverFor[order.id]"
                                    @click.stop.prevent="assignDriver(index)"
                                >
                                    اطبع
                                </button>
                            </div>
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
                        class="bg-white px-4 py-3 border-b border-gray-200 flex flex-wrap gap-4 items-center shadow-sm relative z-20"
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
                                    <button
                                        ref="tableBtnRef"
                                        @click="toggleTablePicker"
                                        class="bg-white border-2 border-blue-200 px-4 py-2 rounded-xl text-sm font-black outline-none focus:border-blue-500 shadow-sm appearance-none cursor-pointer pr-4 pl-10 min-w-[160px] text-gray-800 flex items-center gap-2"
                                    >
                                        <span class="text-blue-600">🪑</span>
                                        <span>{{ tableNumber ? ('طاولة ' + tableNumber) : 'اختر الطاولة...' }}</span>
                                        <span class="ml-auto text-blue-500">▼</span>
                                    </button>

                                    <div v-if="showTablePicker" class="fixed inset-0 z-[1500]" @click="showTablePicker = false"></div>

                                    <div
                                        v-if="showTablePicker"
                                        class="fixed z-[2000] bg-white border-2 border-blue-100 rounded-[1.5rem] shadow-2xl w-[420px] max-h-[300px] overflow-y-auto p-4"
                                        :style="{ top: tablePickerPos.top + 'px', left: tablePickerPos.left + 'px' }"
                                    >
                                        <div class="grid grid-cols-3 gap-3">
                                            <button
                                                v-for="table in availableTables"
                                                :key="table.id"
                                                @click="tableNumber = table.number; showTablePicker = false"
                                                class="p-4 rounded-xl border-2"
                                                :class="tableNumber === table.number ? 'border-blue-500 bg-blue-50 text-blue-700 font-black' : 'border-gray-100 hover:border-blue-300 hover:bg-blue-50 text-gray-800 font-bold'"
                                            >
                                                🪑 طاولة {{ table.number }}
                                            </button>
                                        </div>
                                        <div class="mt-4 flex justify-between">
                                            <button
                                                @click="tableNumber = ''; showTablePicker = false"
                                                class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200"
                                            >مسح الاختيار</button>
                                            <button
                                                @click="showTablePicker = false"
                                                class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700"
                                            >تم</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- خيارات التوصيل -->
                        <template v-if="orderType === 'delivery'">
                            <div
                                class="flex flex-wrap items-center gap-4 flex-[2]"
                            >
                     

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
                                        class="w-full bg-emerald-50/30 border-2 border-emerald-100 px-10 py-2.5 rounded-xl outline-none focus:border-emerald-500 focus:bg-white transition-all text-sm font-bold shadow-inner text-gray-800 placeholder-emerald-400"
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
                                        v-if="!currentCustomer"
                                        @click="showCustomerModal = true"
                                        class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-5 py-2.5 rounded-2xl font-black text-xs shadow-lg shadow-emerald-200 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 whitespace-nowrap"
                                    >
                                        <span>➕</span>
                                        <span> عميل جديد</span>
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
                                            <span v-if="currentCustomer.selectedAddress" class="text-[10px] text-gray-500 font-bold mt-1">
                                                📍 {{ currentCustomer.selectedAddress }}
                                            </span>
                                            <span v-if="currentCustomer.deliveryCharge" class="text-[10px] text-amber-600 font-black mt-0.5">
                                                رسوم التوصيل: {{ parseFloat(currentCustomer.deliveryCharge).toLocaleString() }} ج.م
                                            </span>
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
                :can-suspend="orderType !== 'takeaway'"
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

        <ExpenseListModal
            :show="showExpenseList"
            :expenses="expenses"
            :loading="isLoading"
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
                                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none text-gray-900"
                            />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-1"
                                >الموبايل *</label
                            >
                            <input
                                v-model="newCustomer.phone"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none text-gray-900"
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
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none text-gray-900"
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
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none text-gray-900 placeholder-gray-400"
                        />
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-700">
                            <input type="radio" name="defaultAddress" value="1" v-model="newCustomer.defaultIndex" />
                            اجعل هذا افتراضياً
                        </label>
                        <input
                            v-model="newCustomer.address2"
                            type="text"
                            placeholder="عنوان فرعي 1"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none text-gray-900 placeholder-gray-400"
                        />
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-700">
                            <input type="radio" name="defaultAddress" value="2" v-model="newCustomer.defaultIndex" />
                            اجعل هذا افتراضياً
                        </label>
                        <input
                            v-model="newCustomer.address3"
                            type="text"
                            placeholder="عنوان فرعي 2"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none text-gray-900 placeholder-gray-400"
                        />
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-700">
                            <input type="radio" name="defaultAddress" value="3" v-model="newCustomer.defaultIndex" />
                            اجعل هذا افتراضياً
                        </label>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1"
                            >علامة مميزة</label
                        >
                        <textarea
                            v-model="newCustomer.specialMark"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none text-gray-900 placeholder-gray-400"
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
                class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-100 bounce-in"
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
                <div class="p-6 space-y-3 max-h-[70vh] overflow-y-auto">
                    <p class="text-sm text-gray-600 mb-4">
                        لقد تم العثور على أكثر من عنوان للعميل، يرجى اختيار وجهة
                        التوصيل:
                    </p>
                    <div
                        v-for="(addr, idx) in selectedCustomerAddresses"
                        :key="idx"
                        class="w-full p-3 border-2 border-gray-100 rounded-xl text-right hover:border-amber-500 hover:bg-amber-50 transition font-bold text-sm text-gray-800 break-words flex items-center gap-3"
                    >
                        <div class="flex-1 text-right">
                            <template v-if="editingAddress && editingAddress.id === addr.id">
                                <input v-model="editingAddress.line1" class="w-full border-2 border-gray-100 rounded-xl p-2 text-sm outline-none focus:border-amber-500 mb-2" />
                                <input v-model="editingAddress.line2" class="w-full border-2 border-gray-100 rounded-xl p-2 text-sm outline-none focus:border-amber-500" placeholder="سطر ثاني (اختياري)" />
                            </template>
                            <template v-else>
                                🏠 {{ addr.line1 }}<span v-if="addr.line2"> - {{ addr.line2 }}</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-gray-500 font-bold">رسوم التوصيل</span>
                            <input
                                v-model.number="addr.deliveryCharge"
                                type="number"
                                min="10"
                                step="1"
                                class="w-20 border-2 border-gray-100 rounded-xl p-1 text-xs outline-none focus:border-amber-500 text-right"
                            />
                            <span class="text-[10px] text-gray-500">ج.م</span>
                        </div>
                        <span
                            v-if="addr.is_default"
                            class="px-2 py-1 rounded-lg bg-amber-100 text-amber-700 text-[10px] font-black"
                        >افتراضي</span>
                        <button
                            v-if="!addr.is_default"
                            @click="setDefaultAddress(addr)"
                            class="px-3 py-1 rounded-lg bg-blue-100 text-blue-700 text-[10px] font-black hover:bg-blue-200"
                        >اجعل افتراضياً</button>
                        <button
                            v-if="!(editingAddress && editingAddress.id === addr.id)"
                            @click="startEditAddress(addr)"
                            class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-black hover:bg-gray-200"
                        >تعديل</button>
                        <button
                            v-if="editingAddress && editingAddress.id === addr.id"
                            @click="saveEditAddress"
                            class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-[10px] font-black hover:bg-emerald-200"
                        >حفظ</button>
                        <button
                            v-if="editingAddress && editingAddress.id === addr.id"
                            @click="editingAddress = null"
                            class="px-3 py-1 rounded-lg bg-red-100 text-red-700 text-[10px] font-black hover:bg-red-200"
                        >إلغاء</button>
                        <button
                            @click="confirmAddressSelection(addr)"
                            class="px-3 py-1 rounded-lg bg-amber-600 text-white text-[10px] font-black hover:bg-amber-700"
                        >اختر</button>
                    </div>
                   
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
import ExpenseListModal from "../components/pos/ExpenseListModal.vue";
import ExpenseModal from "../components/pos/ExpenseModal.vue";

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
const taxRate = ref(5);

const suspendedFilter = ref('all');
const filteredSuspendedOrders = computed(() => {
    if (suspendedFilter.value === 'preparing') {
        return suspendedOrders.value.filter(o => o.type === 'delivery' && o.status === 'preparing');
    }
    if (suspendedFilter.value === 'delivering') {
        return suspendedOrders.value.filter(o => o.type === 'delivery' && o.status === 'delivering');
    }
    return suspendedOrders.value;
});

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
const showExpenseList = ref(false);
const showExpenseModal = ref(false);
const customerOrders = ref([]);
const selectedCustomerAddresses = ref([]);
const newAddressText = ref('');
const newAddressType = ref('home');
const newAddressIsDefault = ref(false);
const editingAddress = ref(null);
const expenses = ref([]);
const currentShiftId = ref(null);
const selectedExpenseId = ref(null);
const initialExpense = ref(null);

// الطيارون والحسابات
const drivers = ref([]);
const selectedDriverId = ref(null);
const driverSettlementOrders = ref([]);

// تحميل طلبات السائق غير المُسوّاة
const loadDriverSettlementOrders = async (driverId) => {
    if (!driverId) {
        driverSettlementOrders.value = [];
        return;
    }
    isLoading.value = true;
    try {
        const resp = await apiCall(`/orders?delivery_person_id=${driverId}&is_driver_settled=0&status_in=delivering,completed`);
        const list = resp.data?.data ?? resp.data ?? resp;
        // قد يعود Paginator، لذا نطبع حسب الشكل
        const normalized = Array.isArray(list) ? list : (list?.data ?? []);
        driverSettlementOrders.value = normalized;
    } catch (err) {
        console.error('Error loading driver orders:', err);
        driverSettlementOrders.value = [];
    } finally {
        isLoading.value = false;
    }
};

// إسناد مندوب للتوصيل من قائمة المعلّق
const selectedDriverFor = ref({});
const assignDriver = async (index) => {
    const ord = suspendedOrders.value[index];
    const driverId = selectedDriverFor.value[ord.id];
    if (!driverId) {
        return Swal.fire({
            icon: "info",
            title: "تنبيه",
            text: "اختر مندوب التوصيل أولاً",
            confirmButtonText: "حسناً",
        });
    }
    isLoading.value = true;
    try {
        const items = (ord.items || []).map((i) => ({
            product_id: i.id,
            product_name: i.name,
            unit_price: i.price,
            quantity: i.qty,
        }));
        await apiCall(`/orders/${ord.id}`, {
            method: "POST",
            body: {
                type: "delivery",
                customer_id: ord.customer?.id ?? null,
                delivery_address_id: ord.customer?.addressId ?? null,
                delivery_person_id: driverId,
                delivery_charge: 0,
                items,
                status: "delivering",
                payment_status: "unpaid",
            },
        });
        await loadSuspendedOrders();
        Swal.fire({
            icon: "success",
            title: "تم طباعة الفاتوره",
            text: "تم تحويل الحالة إلى التوصيل",
            timer: 1200,
            showConfirmButton: false,
        });
        selectedDriverFor.value[ord.id] = null;
    } catch (err) {
        Swal.fire({ icon: "error", title: "خطأ", text: err.message });
    } finally {
        isLoading.value = false;
    }
};

// اختيار الطاولة الشبكي
const showTablePicker = ref(false);
const availableTables = computed(() => (tables.value || []).filter(t => t.is_available));
const tableBtnRef = ref(null);
const tablePickerPos = ref({ top: 0, left: 0 });

const toggleTablePicker = () => {
    if (!showTablePicker.value) {
        const el = tableBtnRef.value;
        if (el) {
            const rect = el.getBoundingClientRect();
            const width = 420;
            tablePickerPos.value = {
                top: rect.bottom + window.scrollY + 8,
                left: rect.right + window.scrollX - width,
            };
        }
        showTablePicker.value = true;
    } else {
        showTablePicker.value = false;
    }
};

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
            order_number: (order.shift_order_number ?? order.order_number),
            type: order.type === "table" ? "dinein" : order.type,
            table_number: order.type === "table" ? order.table_number : null,
            created_at: order.created_at,
            ageMinutes: Math.floor((Date.now() - new Date(order.created_at).getTime()) / 60000),
            customer: order.customer
                ? {
                      id: order.customer.id,
                      name: order.customer.name,
                      phone: order.customer.phone,
                      specialMark: order.customer.special_mark,
                      addressId: order.delivery_address_id,
                  }
                : null,
            driverName: order.delivery_person?.name || order.deliveryPerson?.name || null,
            hasDriver: !!(order.delivery_person_id || order.deliveryPerson?.id || order.delivery_person?.id),
            items: order.items.map((item) => ({
                id: item.product_id,
                name: item.product_name || item.product?.name || 'منتج',
                price: parseFloat(item.unit_price),
                qty: item.quantity,
            })),
            total: order.total_amount + " ج",
            time: new Date(order.created_at).toLocaleTimeString("ar-EG", {
                hour: "2-digit",
                minute: "2-digit",
            }),
            status: order.type === "delivery" ? ( (order.delivery_person_id || order.deliveryPerson?.id || order.delivery_person?.id) ? "delivering" : "preparing") : "suspended",
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

watch(selectedDriverId, (id) => {
    loadDriverSettlementOrders(id);
});

setInterval(() => {
    suspendedOrders.value = suspendedOrders.value.map((o) => ({
        ...o,
        ageMinutes: Math.floor((Date.now() - new Date(o.created_at).getTime()) / 60000),
    }));
}, 60000);

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
        const normalized = Array.isArray(data) ? data : (data.data ?? []);
        searchResults.value = normalized;
    } catch (error) {
        console.error("Error searching customers:", error);
    }
};

watch(customerSearch, searchCustomers);

const selectCustomer = (customer) => {
    selectedCustomerAddresses.value = customer.addresses
        .filter((a) => a.address_line_1)
        .map((a) => ({
            id: a.id,
            line1: a.address_line_1,
            line2: a.address_line_2 || '',
            is_default: !!a.is_default,
            type: a.type || 'home',
            deliveryCharge: 0,
        }));

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

const confirmAddressSelection = async (address) => {
    const customer = currentEditingCustomer.value;
    if (customer) {
        currentCustomer.value = {
            id: customer.id,
            name: customer.name,
            phone: customer.phone,
            specialMark: customer.special_mark,
            selectedAddress: address.line1 + (address.line2 ? ' - ' + address.line2 : ''),
            addressId: address.id,
            deliveryCharge: parseFloat(address.deliveryCharge || 0),
        };
        try {
            await apiCall(`/customer-addresses/${address.id}`, { method: 'PATCH', body: { is_default: true } });
            selectedCustomerAddresses.value = selectedCustomerAddresses.value.map(a => ({ ...a, is_default: a.id === address.id }));
        } catch (e) {}
    }
    showAddressSelector.value = false;
};

const setDefaultAddress = async (addr) => {
    try {
        await apiCall(`/customer-addresses/${addr.id}`, { method: 'PATCH', body: { is_default: true } });
        selectedCustomerAddresses.value = selectedCustomerAddresses.value.map(a => ({ ...a, is_default: a.id === addr.id }));
    } catch (e) {}
};

const addNewAddress = async () => {
    const customer = currentEditingCustomer.value;
    if (!customer || !newAddressText.value.trim()) return;
    try {
        const saved = await apiCall('/customer-addresses', {
            method: 'POST',
            body: {
                customer_id: customer.id,
                address_line_1: newAddressText.value.trim(),
                type: newAddressType.value,
                is_default: newAddressIsDefault.value,
            },
        });
        const addr = saved.data || saved;
        selectedCustomerAddresses.value.push({
            id: addr.id,
            line1: addr.address_line_1,
            line2: addr.address_line_2 || '',
            is_default: !!addr.is_default,
            type: addr.type || 'home',
        });
        if (newAddressIsDefault.value) {
            selectedCustomerAddresses.value = selectedCustomerAddresses.value.map(a => ({ ...a, is_default: a.id === addr.id }));
        }
        newAddressText.value = '';
        newAddressType.value = 'home';
        newAddressIsDefault.value = false;
    } catch (e) {}
};

const startEditAddress = (addr) => {
    editingAddress.value = { ...addr };
};

const saveEditAddress = async () => {
    if (!editingAddress.value) return;
    try {
        const body = {
            address_line_1: editingAddress.value.line1,
            address_line_2: editingAddress.value.line2,
            type: editingAddress.value.type,
        };
        const saved = await apiCall(`/customer-addresses/${editingAddress.value.id}`, {
            method: 'PATCH',
            body,
        });
        const addr = saved.data || saved;
        selectedCustomerAddresses.value = selectedCustomerAddresses.value.map(a =>
            a.id === addr.id
                ? {
                      id: addr.id,
                      line1: addr.address_line_1,
                      line2: addr.address_line_2 || '',
                      is_default: !!addr.is_default,
                      type: addr.type || 'home',
                  }
                : a
        );
        editingAddress.value = null;
    } catch (e) {}
};

const newCustomer = ref({
    name: "",
    phone: "",
    phone2: "",
    address1: "",
    address2: "",
    address3: "",
    specialMark: "",
    defaultIndex: "1",
});
const resetNewCustomer = () => {
    newCustomer.value = {
        name: "",
        phone: "",
        phone2: "",
        address1: "",
        address2: "",
        address3: "",
        specialMark: "",
        defaultIndex: "1",
    };
};
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
        const addresses = [];
        if (newCustomer.value.address1) {
            addresses.push({
                address_line_1: newCustomer.value.address1,
                type: "home",
                is_default: newCustomer.value.defaultIndex === "1",
            });
        }
        if (newCustomer.value.address2) {
            addresses.push({
                address_line_1: newCustomer.value.address2,
                type: "work",
                is_default: newCustomer.value.defaultIndex === "2",
            });
        }
        if (newCustomer.value.address3) {
            addresses.push({
                address_line_1: newCustomer.value.address3,
                type: "other",
                is_default: newCustomer.value.defaultIndex === "3",
            });
        }

        const response = await apiCall("/customers", {
            method: "POST",
            body: {
                name: newCustomer.value.name,
                phone: newCustomer.value.phone,
                phone2: newCustomer.value.phone2,
                special_mark: newCustomer.value.specialMark,
                addresses: addresses,
            },
        });

        // التعامل مع شكل الرد الموحد {status, message, data}
        const saved = response.data || response;

        currentCustomer.value = {
            id: saved.id,
            name: saved.name,
            phone: saved.phone,
            specialMark: saved.special_mark,
            selectedAddress: saved.addresses?.[0]?.address_line_1 || null,
            addressId: saved.addresses?.[0]?.id || null,
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

const openOrder = (orderId) => {
    const idx = suspendedOrders.value.findIndex(o => o.id === orderId);
    if (idx === -1) return;
    editingIndex.value = idx;
    const order = suspendedOrders.value[idx];
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
    if (orderType.value === "delivery" && !currentCustomer.value?.id) {
        return Swal.fire({
            icon: "warning",
            title: "تنبيه",
            text: "يرجى اختيار عميل",
            confirmButtonText: "حسناً",
            confirmButtonColor: "#f59e0b",
        });
    }

    isLoading.value = true;
    try {
        const isDelivery = orderType.value === "delivery";
        const hasDriver = !!selectedDriverId.value;
        const computedStatus = isDelivery ? (hasDriver ? "delivering" : "preparing") : "suspended";

        const orderData = {
            type: orderType.value === "dinein" ? "table" : orderType.value,
            table_number:
                orderType.value === "dinein" ? tableNumber.value : null,
            customer_id: currentCustomer.value?.id,
            delivery_address_id: currentCustomer.value?.addressId,
            delivery_person_id: isDelivery && hasDriver ? selectedDriverId.value : null,
            delivery_charge: parseFloat(currentCustomer.value?.deliveryCharge || 0),
            items: invoice.value.map((i) => ({
                product_id: i.id,
                product_name: i.name,
                unit_price: i.price,
                quantity: i.qty,
            })),
            status: computedStatus,
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
        selectedDriverId.value = null;
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
    if (orderType.value === "delivery" && !currentCustomer.value?.addressId) {
        showAddressSelector.value = true;
        return;
    }
    isLoading.value = true;
    try {
        let orderId;
        if (editingIndex.value !== null) {
            orderId = suspendedOrders.value[editingIndex.value].id;
            await apiCall(`/orders/${orderId}/status`, {
                method: "PATCH",
                body: { status: "completed" },
            });
        } else {
            const orderData = {
                type: orderType.value === "dinein" ? "table" : orderType.value,
                customer_id: currentCustomer.value?.id,
                delivery_address_id: currentCustomer.value?.addressId,
                delivery_charge: parseFloat(currentCustomer.value?.deliveryCharge || 0),
                items: invoice.value.map((i) => ({
                    product_id: i.id,
                    product_name: i.name,
                    unit_price: i.price,
                    quantity: i.qty,
                })),
                status: "completed",
                payment_status: "paid",
            };
            const saved = await apiCall("/orders", {
                method: "POST",
                body: orderData,
            });
            orderId = saved?.id ?? saved?.data?.id;
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
        const normalized = Array.isArray(resp)
            ? resp
            : (resp.data?.data ?? resp.data ?? []);
        customerOrders.value = normalized;
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
        // حدّث بيانات المودال إن كانت مفتوحة
        await loadDriverSettlementOrders(id);
        // حدّث المعلّقات
        await loadSuspendedOrders();
        // اترك المودال مفتوحاً ليعرض الوضع الحالي (قد يظهر صافي بعد التحديث)
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
const total = computed(() => subtotal.value + tax.value + parseFloat(currentCustomer.value?.deliveryCharge || 0));

const openExpenses = async () => {
    await loadExpenses();
    showExpenseList.value = true;
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
        const res1 = await Swal.fire({
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
            confirmButtonText: 'حسناً',
            showDenyButton: !!selectedShiftId,
            denyButtonText: 'طباعة'
        });
        if (res1.isDenied && selectedShiftId) {
            window.open(`/api/reports/shift/pdf?shift_id=${selectedShiftId}`, '_blank');
        }
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
        const res2 = await Swal.fire({
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
            confirmButtonText: 'حسناً',
            showDenyButton: !!current,
            denyButtonText: 'طباعة'
        });
        if (res2.isDenied && current) {
            window.open(`/api/reports/shift/pdf?shift_id=${current}`, '_blank');
        }
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

const exportShiftPdf = async () => {
    try {
        const shiftResp = await apiCall('/shifts/current');
        const current = shiftResp.id || shiftResp?.data?.id;
        if (!current) {
            return Swal.fire({icon:'info', title:'لا توجد وردية مفتوحة'});
        }
        window.open(`/api/reports/shift/pdf?shift_id=${current}`, '_blank');
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

const loadExpenses = async () => {
    isLoading.value = true;
    try {
        const shiftResp = await apiCall("/shifts/current");
        currentShiftId.value = shiftResp.id || shiftResp?.data?.id || null;
        const resp = await apiCall(`/expenses${currentShiftId.value ? `?shift_id=${currentShiftId.value}` : ''}`);
        const list = Array.isArray(resp) ? resp : (resp.data ?? []);
        expenses.value = list;
    } catch (err) {
        console.error("Error loading expenses:", err);
    } finally {
        isLoading.value = false;
    }
};

const saveExpense = async (data) => {
    if (!data.amount || parseFloat(data.amount) <= 0) {
        return Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'أدخل مبلغاً صحيحاً' });
    }
    isLoading.value = true;
    try {
        if (selectedExpenseId.value) {
            await apiCall(`/expenses/${selectedExpenseId.value}`, { method: 'PATCH', body: { amount: parseFloat(data.amount), category: data.category, notes: data.notes } });
        } else {
            await apiCall('/expenses', { method: 'POST', body: { amount: parseFloat(data.amount), category: data.category, notes: data.notes, ...(currentShiftId.value ? { shift_id: currentShiftId.value } : {}) } });
        }
        showExpenseModal.value = false;
        selectedExpenseId.value = null;
        initialExpense.value = null;
        await loadExpenses();
        Swal.fire({ icon: 'success', title: 'تم حفظ المصروف', timer: 1200, showConfirmButton: false });
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'فشل الحفظ', text: err.message });
    } finally {
        isLoading.value = false;
    }
};

const startEditExpense = (e) => {
    selectedExpenseId.value = e.id;
    initialExpense.value = { amount: e.amount, category: e.category, notes: e.notes };
    showExpenseModal.value = true;
};

const logout = () => {
    localStorage.removeItem("token");
    router.push("/login");
};
const goTo = (p) => router.push(p);

watch(() => showCustomerModal.value, (v) => {
    if (!v) resetNewCustomer();
});
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
