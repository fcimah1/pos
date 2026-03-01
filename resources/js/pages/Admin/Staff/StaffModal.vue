<template>
    <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-[100]" @click.self="$emit('close')" dir="rtl">
        <div class="bg-gray-900 rounded-[2.5rem] shadow-2xl w-full max-w-lg mx-4 overflow-hidden flex flex-col border border-gray-800 animate-slide-up">
            <!-- الهيدر -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white flex justify-between items-center shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h3 class="font-black text-xl font-tajawal">{{ staff ? 'تعديل بيانات موظف' : 'إضافة موظف جديد' }}</h3>
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
                    <label class="text-sm font-black text-gray-500 mr-2 uppercase tracking-widest leading-none">الاسم الكامل</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        required
                        placeholder="أدخل اسم الموظف..."
                        class="w-full bg-gray-800 border-2 border-gray-700 rounded-2xl px-5 py-3.5 text-white font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all shadow-inner"
                    />
                </div>

                <!-- البريد الإلكتروني -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-gray-500 mr-2 uppercase tracking-widest leading-none">البريد الإلكتروني</label>
                    <input 
                        v-model="form.email" 
                        type="email" 
                        required
                        placeholder="example@company.com"
                        class="w-full bg-gray-800 border-2 border-gray-700 rounded-2xl px-5 py-3.5 text-white font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all shadow-inner text-left"
                        dir="ltr"
                    />
                </div>

                <!-- الدور الوظيفي (سيتم ربطه بجدول designations لاحقاً) -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-gray-500 mr-2 uppercase tracking-widest leading-none">الدور الوظيفي</label>
                    <div class="relative group">
                        <select 
                            v-model="form.designation_id" 
                            class="w-full bg-gray-800 border-2 border-gray-700 rounded-2xl px-5 py-3.5 text-white font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all appearance-none cursor-pointer"
                        >
                            <option :value="null">اختر المسمى الوظيفي...</option>
                            <option :value="1">مدير نظام</option>
                            <option :value="2">كاشير</option>
                            <option :value="3">طيار (توصيل)</option>
                        </select>
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div v-if="!staff" class="space-y-2">
                    <label class="text-sm font-black text-gray-500 mr-2 uppercase tracking-widest leading-none">كلمة المرور المؤقتة</label>
                    <input 
                        v-model="form.password" 
                        type="password" 
                        required
                        class="w-full bg-gray-800 border-2 border-gray-700 rounded-2xl px-5 py-3.5 text-white font-bold focus:border-blue-500 outline-none transition-all shadow-inner"
                    />
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
                        class="flex-1 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-900/40 transition-all active:scale-95 flex items-center justify-center gap-2"
                    >
                        <span v-if="loading" class="animate-spin rounded-full h-5 w-5 border-2 border-white/30 border-t-white"></span>
                        <span>{{ staff ? 'حفظ التغييرات' : 'إضافة الموظف' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";

const props = defineProps({
    show: Boolean,
    staff: Object,
    loading: Boolean
});

const emit = defineEmits(['close', 'save']);

const form = ref({
    name: "",
    email: "",
    designation_id: null,
    password: "password123"
});

watch(() => props.staff, (newVal) => {
    if (newVal) {
        form.value = { ...newVal };
    } else {
        form.value = { name: "", email: "", designation_id: null, password: "password123" };
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
