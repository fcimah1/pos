<template>
    <div v-if="show" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm p-4 text-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden border border-gray-100 bounce-in">
            <div class="bg-amber-600 p-4 text-white font-bold flex justify-between items-center">
                <span>📒 قائمة المصاريف (الوردية الحالية)</span>
                <button @click="$emit('close')" class="hover:bg-amber-700 rounded-full w-8 h-8 flex items-center justify-center transition">✕</button>
            </div>
            <div class="p-6">
                <div v-if="loading" class="text-center text-gray-500 py-10">جاري التحميل...</div>
                <div v-else>
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm text-gray-500 font-bold">الإجمالي</div>
                        <div class="text-2xl font-black text-amber-600">{{ total.toLocaleString() }} <span class="text-sm">ج.م</span></div>
                    </div>
                    <div class="space-y-2 max-h-[50vh] overflow-y-auto">
                        <div v-for="(e, idx) in expenses" :key="idx" class="p-3 border-2 border-gray-100 rounded-xl flex items-center gap-3">
                            <div class="w-16 text-center font-black text-green-700">{{ parseFloat(e.amount).toLocaleString() }}</div>
                            <div class="flex-1">
                                <div class="font-black text-gray-800">{{ e.category || '—' }}</div>
                                <div class="text-xs text-gray-500">{{ e.notes }}</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-xs text-gray-400 font-mono">{{ new Date(e.created_at).toLocaleTimeString('ar-EG', { hour:'2-digit', minute:'2-digit' }) }}</div>
                                <button @click="$emit('edit', e)" class="px-3 py-1 text-xs font-bold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200">تعديل</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-4 flex gap-3 justify-between">
                <button @click="$emit('add')" class="bg-green-600 text-white font-bold py-2 px-6 rounded-xl hover:bg-green-700 transition">➕ إضافة مصروف</button>
                <button @click="$emit('close')" class="px-6 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition">إغلاق</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
    show: Boolean,
    expenses: Array,
    loading: Boolean
});
const emits = defineEmits(['close','add','edit']);
const total = computed(() => (props.expenses || []).reduce((s,e) => s + parseFloat(e.amount || 0), 0));
</script>
