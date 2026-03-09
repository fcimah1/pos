<template>
    <div class="w-1/4 bg-white border-l border-gray-200 flex flex-col shadow-[0_-10px_40px_rgba(0,0,0,0.1)] z-20 overflow-hidden font-tajawal">
        <!-- هيدر الفاتورة الفاخر -->
        <div class="p-5 bg-gradient-to-r from-gray-900 to-gray-800 text-white shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🧾</span>
                    <div>
                        <h2 class="font-black text-lg leading-none">الفاتورة</h2>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-mono">Detailed Invoice</p>
                    </div>
                </div>
                <div v-if="isEditing" class="animate-pulse bg-amber-500 text-black text-[9px] font-black px-3 py-1 rounded-full shadow-lg shadow-amber-500/20">
                    تعديل طلب معلق
                </div>
            </div>
        </div>

        <!-- قائمة الاقسام -->
        <div class="flex-1 min-h-0 overflow-y-auto p-4 space-y-2 bg-gray-50/30">
            <div v-if="invoice.length === 0" class="h-full flex flex-col items-center justify-center opacity-20 grayscale py-20 text-center">
                <span class="text-6xl mb-4">🛒</span>
                <p class="font-black text-gray-900">لا توجد أقسام حالياً</p>
            </div>
            
            <div v-for="(item, index) in invoice" :key="index" class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-300 transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-black text-gray-900 text-sm leading-tight flex-1">{{ item.name }}</span>
                    <button @click="$emit('remove-item', index)" class="text-gray-300 hover:text-red-500 transition-colors p-1">✕</button>
                </div>
                
                <div class="flex justify-between items-center">
                    <div class="flex items-center bg-gray-100 rounded-xl p-1 gap-3">
                        <button @click="$emit('decrease-qty', index)" class="w-7 h-7 rounded-lg bg-white shadow-sm text-red-500 hover:bg-red-50 font-black flex items-center justify-center transition-all">−</button>
                        <span class="font-black text-gray-900 text-xs min-w-[20px] text-center">{{ item.qty }}</span>
                        <button @click="$emit('increase-qty', index)" class="w-7 h-7 rounded-lg bg-white shadow-sm text-emerald-500 hover:bg-emerald-50 font-black flex items-center justify-center transition-all">+</button>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] text-gray-400 line-through" v-if="item.qty > 1">{{ (item.price).toFixed(2) }}</span>
                        <span class="text-blue-700 font-black text-sm">{{ (item.qty * item.price).toFixed(2) }} <small class="text-[8px] font-normal">ج</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ملخص الحساب الفاخر -->
        <div class="p-5 bg-white border-t-2 border-dashed border-gray-100 space-y-4">
            <div class="space-y-2 px-2">
                <div class="flex justify-between text-xs text-gray-500 font-bold">
                    <span>المجموع الفرعي</span>
                    <span>{{ subtotal.toFixed(2) }} ج</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500 font-bold">
                    <span>الضريبة ({{ taxRate }}%)</span>
                    <span>{{ tax.toFixed(2) }} ج</span>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-4 rounded-2xl text-white shadow-xl shadow-blue-200 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex justify-between items-end relative z-10">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase text-blue-100 tracking-widest mb-1">Total Amount</span>
                        <span class="text-3xl font-black leading-none">{{ total.toFixed(2) }} <small class="text-xs font-normal">ج.م</small></span>
                    </div>
                    <div class="text-white/50 text-xs italic">شامل الضريبة</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button @click="$emit('suspend')" :disabled="!canSuspend" class="bg-amber-100 hover:bg-amber-500 hover:text-white text-amber-700 py-3 rounded-2xl font-black text-xs transition-all shadow-sm flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-100 disabled:hover:text-amber-700">
                    <span class="text-lg group-hover:rotate-12 transition-transform">⏸</span><span>تعليق الطلب</span>
                </button>
                <button @click="$emit('clear')" class="bg-red-50 hover:bg-red-500 hover:text-white text-red-600 py-3 rounded-2xl font-black text-xs transition-all shadow-sm flex items-center justify-center gap-2 group">
                    <span class="text-lg group-hover:scale-125 transition-transform">🗑️</span><span>إلغاء الكل</span>
                </button>
            </div>

            <button v-if="isEditing" @click="$emit('pay')" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-2xl font-black text-sm transition-all shadow-sm flex items-center justify-center gap-2 group">
                <span class="text-lg">✅</span><span>إنهاء + دفع نقدي</span>
            </button>

            <button @click="$emit('pay')" class="w-full bg-gray-900 border-2 border-gray-900 hover:bg-white hover:text-gray-900 text-white py-5 rounded-2xl font-black text-lg transition-all shadow-2xl shadow-gray-300 flex items-center justify-center gap-3 group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-transparent transform -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                <span class="text-2xl group-hover:animate-bounce">💳</span>
                <span>إتمام الدفع الآن</span>
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    invoice: Array,
    isEditing: Boolean,
    subtotal: Number,
    tax: Number,
    taxRate: Number,
    total: Number,
    canSuspend: {
        type: Boolean,
        default: true
    }
});

defineEmits(['increase-qty', 'decrease-qty', 'remove-item', 'suspend', 'clear', 'pay']);
</script>
