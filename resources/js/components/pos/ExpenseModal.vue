<template>
    <div v-if="show" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm p-4 text-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 bounce-in">
            <div class="bg-green-600 p-4 text-white font-bold flex justify-between items-center">
                <span>➕ إضافة مصروف جديد</span>
                <button @click="handleClose" class="hover:bg-green-700 rounded-full w-8 h-8 flex items-center justify-center transition">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-1">المبلغ *</label>
                    <input v-model="form.amount" type="number" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 outline-none text-gray-900" />
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">التصنيف</label>
                    <input v-model="form.category" type="text" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 outline-none text-gray-900" />
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">ملاحظات</label>
                    <textarea v-model="form.notes" rows="2" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 outline-none text-gray-900"></textarea>
                </div>
            </div>
            <div class="bg-gray-50 p-4 flex gap-3">
                <button @click="handleSave" class="flex-1 bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition">✅ حفظ المصروف</button>
                <button @click="handleClose" class="px-6 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition">إلغاء</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue';
const props = defineProps({ show: Boolean, initial: Object });
const emit = defineEmits(['close','save']);
const form = reactive({ amount: '', category: '', notes: '' });

const resetForm = () => {
  form.amount = '';
  form.category = '';
  form.notes = '';
};

const handleSave = () => {
  emit('save', { amount: form.amount, category: form.category, notes: form.notes });
  resetForm();
};

const handleClose = () => {
  emit('close');
  resetForm();
};

watch(() => props.show, (v) => {
  if (!v) resetForm();
});

watch(() => props.initial, (v) => {
  if (props.show && v) {
    form.amount = v.amount ?? '';
    form.category = v.category ?? '';
    form.notes = v.notes ?? '';
  }
}, { immediate: true });
</script>
