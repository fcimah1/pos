<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Products</h1>
            <button
                @click="openModal()"
                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Add Product
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="bg-gray-800 p-4 rounded-xl flex gap-4">
            <div class="flex-1 relative">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search products..."
                    class="w-full bg-gray-700 text-white pl-10 pr-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 border-none"
                />
            </div>
            <select
                v-model="filterCategory"
                class="bg-gray-700 text-white px-4 py-2 rounded-lg border-none focus:ring-2 focus:ring-blue-500"
            >
                <option value="">All Categories</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                </option>
            </select>
        </div>

        <!-- Table -->
        <div
            class="bg-gray-800 rounded-xl overflow-hidden shadow-lg border border-gray-700"
        >
            <table class="w-full text-left text-gray-300">
                <thead
                    class="bg-gray-700 text-gray-400 uppercase text-xs font-bold"
                >
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Cost</th>
                        <th class="px-6 py-3">Stock</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <tr
                        v-for="product in products"
                        :key="product.id"
                        class="hover:bg-gray-750 transition-colors"
                    >
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-lg bg-gray-600 flex items-center justify-center text-xl"
                            >
                                📦
                            </div>
                            <div>
                                <p class="font-bold text-white">
                                    {{ product.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ product.barcode }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-gray-700 text-gray-300 px-2 py-1 rounded text-xs font-bold"
                                >{{
                                    product.category?.name || "Uncategorized"
                                }}</span
                            >
                        </td>
                        <td class="px-6 py-4 font-bold text-green-400">
                            {{ product.price }} $
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ product.cost }} $
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="
                                    product.stock > 10
                                        ? 'text-green-500'
                                        : 'text-red-500 font-bold'
                                "
                            >
                                {{ product.stock || "∞" }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button
                                @click="openModal(product)"
                                class="text-blue-400 hover:text-blue-300 transition-colors"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteProduct(product)"
                                class="text-red-400 hover:text-red-300 transition-colors"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ProductModal
            :show="showModal"
            :product="editingProduct"
            :categories="categories"
            @close="showModal = false"
            @save="saveProduct"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ProductModal from "./ProductModal.vue";

const products = ref([]);
const categories = ref([]);
const search = ref("");
const filterCategory = ref("");
const showModal = ref(false);
const editingProduct = ref(null);

onMounted(() => {
    fetchProducts();
    fetchCategories();
});

const fetchProducts = async () => {
    // Mock data for now
    products.value = [
        {
            id: 1,
            name: "Cheeseburger",
            barcode: "123456",
            price: 15.0,
            cost: 8.0,
            category: { id: 1, name: "Meals" },
            stock: 50,
        },
        {
            id: 2,
            name: "Cola Zero",
            barcode: "789012",
            price: 2.5,
            cost: 1.0,
            category: { id: 3, name: "Drinks" },
            stock: 100,
        },
    ];
};

const fetchCategories = async () => {
    categories.value = [
        { id: 1, name: "Meals" },
        { id: 3, name: "Drinks" },
    ];
};

const openModal = (product = null) => {
    editingProduct.value = product;
    showModal.value = true;
};

const saveProduct = (productData) => {
    console.log("Saving", productData);
    // TODO: API Call
    showModal.value = false;
    fetchProducts(); // Refresh
};

const deleteProduct = (product) => {
    if (confirm("Are you sure?")) {
        console.log("Deleting", product.id);
        // TODO: API Call
    }
};
</script>
