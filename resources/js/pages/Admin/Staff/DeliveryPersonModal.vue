<template>
    <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-[100]" @click.self="$emit('close')" dir="rtl">
        <div class="bg-gray-900 rounded-[2.5rem] shadow-2xl w-full max-w-lg mx-4 overflow-hidden flex flex-col border border-gray-800 animate-slide-up">
            <!-- الهيدر -->
            <div class="bg-gradient-to-r from-orange-600 to-red-600 p-6 text-white flex justify-between items-center shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="font-black text-xl font-tajawal">{{ deliveryPerson ? 'تعديل بيانات الطيار' : 'إضافة طيار جديد' }}</h3>
                </div>
                <button @click="$emit('close')" class="hover:bg-white/20 p-2 rounded-2xl transition-all active:scale-95 group">
                    <svg class="w-6 h-6 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="handleSubmit" class="p-8 space-y-6 bg-gray-900">
                <!-- الاسم -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-gray-400 mr-2 uppercase tracking-widest leading-none text-right block">الاسم الكامل</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        required
                        placeholder="أدخل اسم الطيار..."
                        class="w-full bg-gray-800 border-2 border-gray-700 rounded-2xl px-5 py-3.5 text-white font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 outline-none transition-all shadow-inner text-right"
                    />
                </div>

                <!-- رقم الهاتف -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-gray-400 mr-2 uppercase tracking-widest leading-none text-right block">رقم الهاتف</label>
                    <input 
                        v-model="form.phone" 
                        type="tel" 
                        required
                        placeholder="01xxxxxxxxx"
                        class="w-full bg-gray-800 border-2 border-gray-700 rounded-2xl px-5 py-3.5 text-white font-bold focus:border-orange-500 outline-none transition-all shadow-inner text-left"
                        dir="ltr"
                    />
                </div>

                <!-- خيار نشط -->
                <div class="flex items-center gap-3 bg-gray-800 border-2 border-gray-700 rounded-2xl px-5 py-3.5 transition-all">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                    <span class="text-sm font-black" :class="form.is_active ? 'text-orange-400' : 'text-gray-400'">
                        {{ form.is_active ? 'الطيار نشط' : 'الطيار غير نشط' }}
                    </span>
                </div>

                <div class="flex gap-4 pt-4">
                    <button 
                        type="button" 
                        @click="$emit('close')"
                        class="flex-1 bg-gray-800 hover:bg-gray-700 text-white font-black py-4 rounded-2xl transition-all active:scale-95"
                    >
                        إلغاء
                    </button>
                    <button 
                        type="submit"
                        :disabled="loading"
                        class="flex-1 bg-orange-600 hover:bg-orange-500 disabled:opacity-50 text-white font-black py-4 rounded-2xl shadow-xl shadow-orange-900/40 transition-all active:scale-95 flex items-center justify-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin rounded-full h-5 w-5 border-2 border-white/30 border-t-white"></span>
                        <span>{{ deliveryPerson ? 'حفظ التغييرات' : 'إضافة الطيار' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    show: Boolean,
    deliveryPerson: Object,
    loading: Boolean
});

const emit = defineEmits(['close', 'save']);

const form = ref({
    name: "",
    phone: "",
    is_active: true
});

const resetForm = () => {
    form.value = { 
        name: "", 
        phone: "",
        is_active: true
    };
};

watch(() => props.show, (isShown) => {
    if (isShown && !props.deliveryPerson) {
        resetForm();
    }
});

watch(() => props.deliveryPerson, (newVal) => {
    if (newVal) {
        form.value = { 
            ...newVal,
            is_active: newVal.is_active !== undefined ? !!newVal.is_active : true
        };
    } else {
        resetForm();
    }
}, { immediate: true });

const handleSubmit = () => {
    emit('save', { ...form.value });
};
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideUp {
    from { transform: translateY(30px) scale(0.95); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
</style>
