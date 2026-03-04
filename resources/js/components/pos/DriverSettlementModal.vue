<template>
    <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-[110]" @click.self="$emit('close')" dir="rtl">
        <div class="bg-gray-900 rounded-[2.5rem] shadow-2xl w-full max-w-2xl mx-4 overflow-hidden flex flex-col max-h-[85vh] border border-gray-800 animate-slide-up">
            <!-- الهيدر -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white flex justify-between items-center shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-black text-xl font-tajawal">مراجعة حسابات الطيارين</h3>
                </div>
                <button @click="$emit('close')" class="hover:bg-white/20 p-2 rounded-2xl transition-all active:scale-95 group">
                    <svg class="w-6 h-6 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- اختيار الطيار -->
            <div class="p-6 bg-gray-800/40 border-b border-gray-800">
                <div class="flex flex-col gap-3">
                    <label class="font-black text-gray-400 text-sm uppercase tracking-widest mr-2">اختيار الطيار المسؤول:</label>
                    <div class="relative group">
                        <select 
                            :value="selectedDriverId" 
                            @change="$emit('update:selectedDriverId', $event.target.value)"
                            class="w-full bg-gray-900 border border-gray-700 rounded-2xl px-5 py-3.5 font-black text-blue-400 outline-none focus:ring-2 focus:ring-blue-500/50 shadow-inner appearance-none transition-all"
                        >
                            <option :value="null">اختر الطيار من القائمة...</option>
                            <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- المحتوى -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-900 custom-scrollbar">
                <div v-if="!selectedDriverId" class="text-center py-20">
                    <div class="bg-indigo-600/10 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 opacity-40 animate-bounce">
                        <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="font-black text-gray-500 text-lg">يرجى اختيار طيار لعرض المعلقات المالية</p>
                </div>
                
                <div v-else-if="loading" class="flex flex-col justify-center items-center py-20 gap-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-600/20 border-t-indigo-600"></div>
                    <p class="text-gray-500 font-bold animate-pulse">جاري التحقق من الحسابات...</p>
                </div>

                <div v-else-if="orders.length === 0" class="text-center py-20">
                    <div class="bg-emerald-600/10 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-emerald-500 font-black text-xl">الحساب صافي! كل الطلبات تم تسويتها</p>
                </div>
                
                <div v-else class="space-y-4">
                    <!-- شريط تحديد الكل -->
                    <div class="flex items-center justify-between bg-gray-800/40 rounded-2xl p-3 border border-gray-800">
                        <label class="flex items-center gap-2 text-gray-300 text-sm font-bold">
                            <input type="checkbox" class="accent-indigo-600 w-4 h-4" :checked="isAllSelected" @change="toggleAll" />
                            تحديد الكل
                        </label>
                        <div class="text-xs text-gray-400 font-black">المحدد: {{ selectedIds.length }} من {{ orders.length }}</div>
                    </div>

                    <div v-for="ord in orders" :key="ord.id" class="bg-gray-800/40 rounded-3xl p-5 border border-gray-800 hover:border-indigo-500/30 transition-all hover:bg-gray-800/60 group shadow-sm">
                        <div class="flex justify-between items-center">
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" class="accent-indigo-600 w-4 h-4" :value="ord.id" v-model="selectedIds" />
                                    <span class="bg-indigo-600/10 text-indigo-400 text-xs font-black px-3 py-1 rounded-full border border-indigo-600/20">طلب #{{ ord.id }}</span>
                                    <span class="text-xs text-gray-500 font-bold">{{ ord.created_at }}</span>
                                </div>
                                <div class="font-black text-white text-lg flex items-center gap-2">
                                    <span class="text-gray-500 text-base font-bold">العميل:</span>
                                    <span>{{ ord.customer?.name || 'زائر خارجي' }}</span>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="text-2xl font-black text-indigo-400">{{ parseFloat(ord.total_amount).toLocaleString() }} <span class="text-xs font-normal">ج.م</span></div>
                                <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest mt-1">يجب التحصيل كاش</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- الفوتر والتسوية -->
            <div v-if="selectedDriverId && orders.length > 0" class="p-8 bg-gray-900 border-t border-gray-800 shadow-2xl">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex flex-col text-right">
                        <span class="text-xs text-gray-500 font-black uppercase tracking-widest mb-1">إجمالي العجز المطلوب توريده</span>
                        <div class="text-4xl font-black text-indigo-400">{{ selectedTotal.toLocaleString() }} <span class="text-sm font-normal">ج.م</span></div>
                    </div>
                    <button 
                        @click="$emit('settle', selectedDriverId, selectedIds)"
                        :disabled="selectedIds.length === 0"
                        class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-900/50 disabled:cursor-not-allowed text-white px-10 py-5 rounded-[2rem] font-black text-xl shadow-2xl shadow-emerald-900/30 transition-all active:scale-95 flex items-center justify-center gap-3 group"
                    >
                        <span>تسوية الحساب الآن</span>
                        <svg class="w-7 h-7 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
defineProps({
    show: Boolean,
    drivers: Array,
    selectedDriverId: [Number, String],
    orders: Array,
    loading: Boolean
});

defineEmits(['close', 'update:selectedDriverId', 'settle']);

const selectedIds = ref([]);
const isAllSelected = computed(() => selectedIds.value.length > 0 && selectedIds.value.length === (Array.isArray(__props.orders) ? __props.orders.length : 0));
const selectedTotal = computed(() => {
    const map = new Set(selectedIds.value);
    return (Array.isArray(__props.orders) ? __props.orders : []).filter(o => map.has(o.id)).reduce((s, o) => s + parseFloat(o.total_amount || 0), 0);
});

const toggleAll = (e) => {
    if (e.target.checked) {
        selectedIds.value = (Array.isArray(__props.orders) ? __props.orders : []).map(o => o.id);
    } else {
        selectedIds.value = [];
    }
};

watch(() => __props.selectedDriverId, () => {
    selectedIds.value = [];
});

watch(() => __props.orders, () => {
    selectedIds.value = [];
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 10px;
}
.animate-slide-up {
    animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideUp {
    from { transform: translateY(30px) scale(0.95); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
</style>
