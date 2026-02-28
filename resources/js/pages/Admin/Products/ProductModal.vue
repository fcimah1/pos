<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    >
        <div
            class="bg-gray-800 rounded-2xl w-full max-w-lg border border-gray-700 shadow-2xl overflow-hidden animate-fade-in-up"
        >
            <div
                class="p-4 border-b border-gray-700 flex justify-between items-center bg-gray-800"
            >
                <h3 class="text-lg font-bold text-white">
                    {{ product ? "Edit Product" : "New Product" }}
                </h3>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-white transition-colors"
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

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-400"
                        >Product Name</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                        placeholder="e.g. Cheese Burger"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-400"
                            >Barcode</label
                        >
                        <input
                            v-model="form.barcode"
                            type="text"
                            required
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                            placeholder="Scan or Type"
                        />
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-400"
                            >Category</label
                        >
                        <select
                            v-model="form.category_id"
                            required
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="" disabled>Select Category</option>
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
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-400"
                            >Price ($)</label
                        >
                        <input
                            v-model="form.price"
                            type="number"
                            step="0.01"
                            required
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                        />
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-400"
                            >Cost ($)</label
                        >
                        <input
                            v-model="form.cost"
                            type="number"
                            step="0.01"
                            required
                            class="w-full bg-gray-700 border-gray-600 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"
                        />
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 font-medium transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg font-bold shadow-lg shadow-blue-900/20 transition-all"
                    >
                        Save Product
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
    product: Object,
    categories: Array,
});

const emit = defineEmits(["close", "save"]);

const form = reactive({
    id: null,
    name: "",
    barcode: "",
    category_id: "",
    price: "",
    cost: "",
});

watch(
    () => props.product,
    (newVal) => {
        if (newVal) {
            form.id = newVal.id;
            form.name = newVal.name;
            form.barcode = newVal.barcode;
            form.category_id = newVal.category?.id;
            form.price = newVal.price;
            form.cost = newVal.cost;
        } else {
            form.id = null;
            form.name = "";
            form.barcode = "";
            form.category_id = "";
            form.price = "";
            form.cost = "";
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
