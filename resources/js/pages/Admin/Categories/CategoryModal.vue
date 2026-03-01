<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4"
    >
        <div class="bg-gray-800 rounded-3xl w-full max-w-md border border-gray-700 shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="p-5 border-b border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="text-3xl">{{ form.icon || '📁' }}</span>
                    {{ category ? "تعديل الصنف" : "إضافة صنف جديد" }}
                </h3>
                <button @click="$emit('close')" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-xl transition-all">✕</button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-5 text-right" dir="rtl">

                <!-- اسم الصنف -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-400">اسم الصنف *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full bg-gray-900 border border-gray-700 rounded-2xl text-white focus:ring-2 focus:ring-blue-500 px-5 py-3 placeholder-gray-600 text-lg"
                        placeholder="مثال: وجبات رئيسية..."
                    />
                </div>

                <!-- أيقونة الصنف -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-400">أيقونة الصنف (Emoji)</label>
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-gray-700 rounded-2xl border border-gray-600 flex items-center justify-center text-3xl flex-shrink-0">
                            {{ form.icon || '📁' }}
                        </div>
                        <input
                            v-model="form.icon"
                            type="text"
                            maxlength="4"
                            class="flex-1 bg-gray-900 border border-gray-700 rounded-2xl text-white focus:ring-2 focus:ring-blue-500 px-4 py-3 placeholder-gray-600 text-2xl text-center"
                            placeholder="📁"
                        />
                    </div>
                    <p class="text-xs text-gray-500">اكتب أي Emoji كأيقونة للصنف (مثال: 🍔 🍕 🥗)</p>
                </div>

                <!-- Buttons -->
                <div class="pt-4 flex justify-end gap-3 border-t border-gray-700/50">
                    <button type="button" @click="$emit('close')" class="px-6 py-3 rounded-2xl text-gray-400 hover:text-white hover:bg-gray-700 font-bold transition-all">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-10 py-3 rounded-2xl font-bold shadow-lg transition-all">
                        حفظ الصنف ✓
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch } from "vue";

const props = defineProps({
    show: Boolean,
    category: Object,
});

const emit = defineEmits(["close", "save"]);

const form = reactive({
    id: null,
    name: "",
    icon: "",
});

watch(
    () => props.category,
    (newVal) => {
        if (newVal) {
            form.id = newVal.id;
            form.name = newVal.name;
            form.icon = newVal.icon || "";
        } else {
            form.id = null;
            form.name = "";
            form.icon = "";
        }
    },
    { immediate: true },
);

const submit = () => {
    emit("save", { ...form });
};
</script>
