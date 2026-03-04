<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black/60 backdrop-blur-md z-[100] flex items-center justify-center p-4 animate-fade-in"
    >
        <div
            class="bg-gray-800 rounded-2xl w-full max-w-lg border border-gray-700 shadow-2xl overflow-hidden animate-slide-up"
        >
            <div
                class="p-5 border-b border-gray-700 flex justify-between items-center bg-gray-800/50"
            >
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="p-2 bg-blue-600/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
                    {{ product ? "تعديل المنتج" : "إضافة منتج جديد" }}
                </h3>
                <button
                    @click="$emit('close')"
                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-xl transition-all"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-5 text-right" dir="rtl">
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-gray-400 mr-1"
                        >اسم المنتج</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full bg-gray-900 border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 transition-all px-4 py-3 placeholder-gray-600"
                        placeholder="مثال: شاورما دجاج"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-gray-400 mr-1"
                            >الباركود</label
                        >
                        <input
                            v-model="form.barcode"
                            type="text"
                            required
                            class="w-full bg-gray-900 border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 transition-all px-4 py-3 placeholder-gray-600"
                            placeholder="امسح أو اكتب الكود"
                        />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-gray-400 mr-1"
                            >الصنف</label
                        >
                        <select
                            v-model="form.category_id"
                            required
                            class="w-full bg-gray-900 border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 transition-all px-4 py-3"
                        >
                            <option value="" disabled>اختر الصنف</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-gray-400 mr-1"
                            >السعر (ج.م)</label
                        >
                        <input
                            v-model="form.price"
                            type="number"
                            step="0.01"
                            required
                            :disabled="hasVariations"
                            class="w-full bg-gray-900 border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 transition-all px-4 py-3 disabled:opacity-60"
                        />
                        <p v-if="hasVariations" class="text-xs text-amber-400 mt-1">
                            المنتج لديه أحجام؛ يتم حساب السعر من أحجام المنتج.
                        </p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-gray-400 mr-1"
                            >التكلفة (ج.م)</label
                        >
                        <input
                            v-model="form.cost_price"
                            type="number"
                            step="0.01"
                            required
                            :disabled="hasVariations"
                            class="w-full bg-gray-900 border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 transition-all px-4 py-3 disabled:opacity-60"
                        />
                        <p v-if="hasVariations" class="text-xs text-amber-400 mt-1">
                            التكلفة تختلف حسب الحجم؛ سيتم تخزينها لكل حجم على حدة.
                        </p>
                    </div>
                </div>

                <!-- أيقونة المنتج -->
                <div class="space-y-1.5">
                    <label class="text-sm font-bold text-gray-400 mr-1">أيقونة المنتج (Emoji)</label>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-700 rounded-xl border border-gray-600 flex items-center justify-center text-2xl flex-shrink-0">
                            {{ form.icon || '🍽️' }}
                        </div>
                        <input
                            v-model="form.icon"
                            type="text"
                            maxlength="4"
                            class="flex-1 bg-gray-900 border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 transition-all px-4 py-3 placeholder-gray-600 text-2xl text-center"
                            placeholder="🍽️"
                        />
                    </div>
                    <p class="text-xs text-gray-500">اكتب أي Emoji كأيقونة للمنتج (مثال: 🍔 🍕 🥗 🥩)</p>
                </div>

                <!-- أزرار الحفظ -->
                <div class="space-y-2 pt-2" ref="variationsTop">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-gray-400">أحجام المنتج</h4>
                        <button type="button" @click="addVariation" class="text-blue-400 hover:text-blue-300 text-sm font-bold">+ إضافة حجم</button>
                    </div>
                    <div v-if="form.variations.length === 0" class="text-xs text-gray-500">لا توجد أحجام بعد.</div>
                    <div v-for="(v, idx) in form.variations" :key="idx" class="grid grid-cols-4 gap-3 items-end bg-gray-900/40 p-3 rounded-xl border border-gray-700">
                        <div>
                            <label class="text-xs font-bold text-gray-400">الاسم/الحجم</label>
                            <input v-model="v.size_name" type="text" class="w-full bg-gray-900 border-gray-700 rounded-xl text-white px-3 py-2" placeholder="صغير / وسط / كبير" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400">السعر (ج.م)</label>
                            <input v-model.number="v.price" type="number" step="0.01" class="w-full bg-gray-900 border-gray-700 rounded-xl text-white px-3 py-2" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400">التكلفة (ج.م)</label>
                            <input v-model.number="v.cost_price" type="number" step="0.01" class="w-full bg-gray-900 border-gray-700 rounded-xl text-white px-3 py-2" />
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="text-xs font-bold text-gray-400">الباركود</label>
                                <input v-model="v.barcode" type="text" class="w-full bg-gray-900 border-gray-700 rounded-xl text-white px-3 py-2" />
                            </div>
                            <button type="button" @click="removeVariation(idx)" class="self-end px-3 py-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 font-bold">حذف</button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">عند وجود أحجام، يتم اعتماد أقل سعر/تكلفة في القوائم العامة.</p>
                </div>

                <!-- أزرار الحفظ -->
                <div class="pt-4 flex justify-end gap-3 border-t border-gray-700/50">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-6 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-gray-700 font-bold transition-all"
                    >
                        إلغاء
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-900/40 transition-all flex items-center gap-2"
                    >
                        <span>حفظ المنتج</span> ✓
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch, computed, ref, nextTick } from "vue";

const props = defineProps({
    show: Boolean,
    product: Object,
    categories: Array,
    focusVariations: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(["close", "save"]);

const form = reactive({
    id: null,
    name: "",
    barcode: "",
    category_id: "",
    price: "",
    cost_price: "",
    icon: "",
    variations: []
});

const hasVariations = computed(() => Array.isArray(props.product?.variations) && props.product.variations.length > 0);

const variationsTop = ref(null);
watch(() => props.show, async (v) => {
    if (v && props.focusVariations) {
        await nextTick();
        variationsTop.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

const addVariation = () => {
    form.variations.push({ id: null, size_name: "", price: 0, cost_price: 0, barcode: "" });
};
const removeVariation = (idx) => {
    form.variations.splice(idx, 1);
};

watch(
    () => props.product,
    (newVal) => {
        if (newVal) {
            form.id = newVal.id;
            form.name = newVal.name;
            form.barcode = newVal.barcode;
            form.category_id = newVal.category?.id;
            form.price = newVal.price;
            form.cost_price = newVal.cost_price;
            form.icon = newVal.icon || "";
            form.variations = (newVal.variations || []).map(v => ({
                id: v.id ?? null,
                size_name: v.size_name ?? "",
                price: parseFloat(v.price ?? 0),
                cost_price: v.cost_price != null ? parseFloat(v.cost_price) : null,
                barcode: v.barcode ?? ""
            }));
        } else {
            form.id = null;
            form.name = "";
            form.barcode = "";
            form.category_id = "";
            form.price = "";
            form.cost_price = "";
            form.icon = "";
            form.variations = [];
        }
    },
    { immediate: true },
);

const submit = () => {
    emit("save", { ...form });
};
</script>

<style scoped>
@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fade-in-up 0.3s ease-out forwards;
}
</style>
