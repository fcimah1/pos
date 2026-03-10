<template>
    <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" dir="rtl">
        <div class="bg-gray-800 w-full max-w-2xl rounded-3xl shadow-2xl border border-gray-700 overflow-hidden transform transition-all scale-100 font-tajawal">
            <!-- Header -->
            <div class="bg-gray-700/50 p-6 border-b border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-black text-white">
                    {{ branch ? 'تعديل فرع' : 'إضافة فرع جديد' }}
                </h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">اسم الفرع</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="مثال: فرع مدينة نصر"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-right"
                        />
                    </div>

                    <!-- Code -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">كود الفرع</label>
                        <input
                            v-model="form.code"
                            type="text"
                            required
                            placeholder="مثال: BR-001"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-right uppercase"
                        />
                    </div>

                    <!-- Phone 1 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">رقم الهاتف 1</label>
                        <input
                            v-model="form.phone"
                            type="text"
                            placeholder="01xxxxxxxxx"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-left font-mono"
                            dir="ltr"
                        />
                    </div>

                    <!-- Phone 2 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">رقم الهاتف 2</label>
                        <input
                            v-model="form.phone_2"
                            type="text"
                            placeholder="01xxxxxxxxx"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-left font-mono"
                            dir="ltr"
                        />
                    </div>

                    <!-- Phone 3 -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">رقم الهاتف 3</label>
                        <input
                            v-model="form.phone_3"
                            type="text"
                            placeholder="01xxxxxxxxx"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-left font-mono"
                            dir="ltr"
                        />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">البريد الإلكتروني</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="branch@example.com"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-left font-mono"
                            dir="ltr"
                        />
                    </div>

                    <!-- Tax Rate -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">نسبة الضريبة (%)</label>
                        <input
                            v-model="form.tax_rate"
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-right"
                        />
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-400">الحالة</label>
                        <select
                            v-model="form.is_active"
                            class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-right"
                        >
                            <option :value="true">نشط</option>
                            <option :value="false">غير نشط</option>
                        </select>
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-400">العنوان بالتفصيل</label>
                    <textarea
                        v-model="form.address"
                        required
                        rows="2"
                        class="w-full bg-gray-900 border-gray-700 text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-right"
                    ></textarea>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4 border-t border-gray-700 mt-6 font-tajawal">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-900/20 disabled:opacity-50"
                    >
                        {{ loading ? 'جاري الحفظ...' : 'حفظ البيانات' }}
                    </button>
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 rounded-xl transition-all"
                    >
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    branch: Object,
    loading: Boolean
});

const emit = defineEmits(['close', 'save']);

const form = ref({
    name: '',
    code: '',
    address: '',
    phone: '',
    phone_2: '',
    phone_3: '',
    email: '',
    tax_rate: 0,
    is_active: true
});

const resetForm = () => {
    form.value = {
        name: '',
        code: '',
        address: '',
        phone: '',
        phone_2: '',
        phone_3: '',
        email: '',
        tax_rate: 0,
        is_active: true
    };
};

watch(() => props.branch, (newVal) => {
    if (newVal) {
        form.value = { ...newVal };
    } else {
        resetForm();
    }
}, { immediate: true });

const handleSubmit = () => {
    emit('save', { ...form.value });
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
