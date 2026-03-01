<template>
    <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-[100]" @click.self="$emit('close')" dir="rtl">
        <div class="bg-gray-900 rounded-[2.5rem] shadow-2xl w-full max-w-2xl mx-4 overflow-hidden flex flex-col max-h-[85vh] border border-gray-800 animate-slide-up">
            <!-- الهيدر -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white flex justify-between items-center shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-black text-xl font-tajawal">سجل طلبات العميل</h3>
                </div>
                <button @click="$emit('close')" class="hover:bg-white/20 p-2 rounded-2xl transition-all active:scale-95 group">
                    <svg class="w-6 h-6 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 bg-gray-900 custom-scrollbar">
                <div v-if="loading" class="flex flex-col justify-center items-center py-20 gap-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600/20 border-t-blue-600"></div>
                    <p class="text-gray-500 font-bold animate-pulse">جاري تحميل السجل...</p>
                </div>
                
                <div v-else-if="orders.length === 0" class="text-center py-20">
                    <div class="bg-gray-800 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 opacity-20">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="font-black text-gray-500 text-lg">لا توجد طلبات سابقة لهذا العميل</p>
                </div>
                
                <div v-else class="space-y-4">
                    <div v-for="order in orders" :key="order.id" class="bg-gray-800/40 rounded-3xl p-5 border border-gray-800 hover:border-blue-500/30 transition-all hover:bg-gray-800/60 group shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-3">
                                    <span class="bg-blue-600/10 text-blue-400 text-xs font-black px-3 py-1 rounded-full border border-blue-600/20">رقم #{{ order.id }}</span>
                                    <span class="text-xs text-gray-500 font-bold">{{ formatDate(order.created_at) }}</span>
                                </div>
                                <div class="font-black text-white text-lg flex items-center gap-2">
                                    <span v-if="order.type === 'table'">🍽️ صالة</span>
                                    <span v-else-if="order.type === 'delivery'">🚚 توصيل</span>
                                    <span v-else>🥡 تيك أواي</span>
                                </div>
                            </div>
                            <div class="text-left">
                                <span :class="getStatusClass(order.status)" class="text-[10px] font-black px-3 py-1 rounded-full block mb-2 border">
                                    {{ getStatusLabel(order.status) }}
                                </span>
                                <div class="text-2xl font-black text-blue-400">{{ parseFloat(order.total_amount).toLocaleString() }} <span class="text-xs font-normal">ج.م</span></div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-900/50 rounded-2xl p-4 border border-gray-800/50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2">
                                <div v-for="item in order.items" :key="item.id" class="text-sm text-gray-400 flex justify-between items-center group/item">
                                    <span class="truncate font-bold group-hover/item:text-white transition-colors">{{ item.product?.name || 'منتج غير معروف' }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="w-px h-3 bg-gray-700"></span>
                                        <span class="font-black text-blue-500">x{{ item.quantity }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 bg-gray-900 border-t border-gray-800">
                <button @click="$emit('close')" class="w-full bg-gray-800 hover:bg-gray-700 text-white py-4 rounded-2xl font-black transition-all active:scale-[0.98] shadow-lg">
                    إغلاق سجل الطلبات
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: Boolean,
    orders: Array,
    loading: Boolean
});

defineEmits(['close']);

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString('ar-EG', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': '⏳ معلق',
        'paid': '✅ مدفوع',
        'completed': '🏁 مكتمل',
        'cancelled': '❌ ملغي',
        'suspended': '⏸ معلق'
    };
    return labels[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-amber-100 text-amber-700',
        'suspended': 'bg-amber-100 text-amber-700',
        'paid': 'bg-emerald-100 text-emerald-700',
        'completed': 'bg-blue-100 text-blue-700',
        'cancelled': 'bg-red-100 text-red-700'
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};
</script>
