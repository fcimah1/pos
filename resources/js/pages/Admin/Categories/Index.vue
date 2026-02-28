<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Categories</h1>
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
                Add Category
            </button>
        </div>

        <!-- Search -->
        <div class="bg-gray-800 p-4 rounded-xl">
            <div class="relative">
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
                    placeholder="Search categories..."
                    class="w-full bg-gray-700 text-white pl-10 pr-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 border-none"
                />
            </div>
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
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Products Count</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <tr
                        v-for="category in categories"
                        :key="category.id"
                        class="hover:bg-gray-750 transition-colors"
                    >
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-lg bg-gray-600 flex items-center justify-center text-xl"
                            >
                                📁
                            </div>
                            <span class="font-bold text-white">{{
                                category.name
                            }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-400">
                            {{ category.products_count || 0 }} items
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button
                                @click="openModal(category)"
                                class="text-blue-400 hover:text-blue-300 transition-colors"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteCategory(category)"
                                class="text-red-400 hover:text-red-300 transition-colors"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <CategoryModal
            :show="showModal"
            :category="editingCategory"
            @close="showModal = false"
            @save="saveCategory"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import CategoryModal from "./CategoryModal.vue";

const categories = ref([]);
const search = ref("");
const showModal = ref(false);
const editingCategory = ref(null);

onMounted(() => {
    fetchCategories();
});

const fetchCategories = async () => {
    // Mock data for now
    categories.value = [
        { id: 1, name: "Meals", products_count: 15 },
        { id: 2, name: "Pizza", products_count: 8 },
        { id: 3, name: "Drinks", products_count: 12 },
    ];
};

const openModal = (category = null) => {
    editingCategory.value = category;
    showModal.value = true;
};

const saveCategory = (categoryData) => {
    console.log("Saving", categoryData);
    // TODO: API Call
    showModal.value = false;
    fetchCategories(); // Refresh
};

const deleteCategory = (category) => {
    if (confirm("Are you sure?")) {
        console.log("Deleting", category.id);
        // TODO: API Call
    }
};
</script>
