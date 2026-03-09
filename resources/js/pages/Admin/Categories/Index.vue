<template>
    <div class="space-y-6 animate-fade-in text-right" dir="rtl">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white font-tajawal">إدارة الاقسام</h1>
            <button
                @click="openModal()"
                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                إضافة قسم
            </button>
        </div>

        <!-- البحث -->
        <div class="bg-gray-800/50 backdrop-blur-md p-4 rounded-2xl border border-gray-700/50 shadow-xl">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute right-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="ابحث عن صنف..."
                    class="w-full bg-gray-900/50 text-white pr-10 pl-4 py-2.5 rounded-xl focus:ring-2 focus:ring-blue-500 border-gray-700 transition-all"
                />
            </div>
        </div>

        <!-- الجدول -->
        <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-2xl border border-gray-700 overflow-x-auto">
            <table class="w-full text-right text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">الصنف</th>
                        <th class="px-6 py-4">عدد المنتجات</th>
                        <th class="px-6 py-4 text-left">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <tr
                        v-for="category in filteredCategories"
                        :key="category.id"
                        class="hover:bg-gray-750/50 transition-colors group"
                    >
                        <td class="px-6 py-4 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-xl bg-gray-900 flex items-center justify-center text-xl shadow-inner border border-gray-700">
                                📁
                            </div>
                            <span class="font-bold text-white group-hover:text-blue-400 transition-colors">{{
                                category.name
                            }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-600/10 text-blue-400 px-3 py-1 rounded-full text-xs font-bold border border-blue-600/20">
                                {{ category.products_count || 0 }} منتج
                            </span>
                        </td>
                        <td class="px-6 py-4 text-left space-x-reverse space-x-3">
                            <button
                                @click="openModal(category)"
                                class="text-blue-400 hover:text-blue-300 transition-colors p-2 hover:bg-blue-400/10 rounded-lg"
                            >
                                تعديل
                            </button>
                            <button
                                @click="deleteCategory(category)"
                                class="text-red-400 hover:text-red-300 transition-colors p-2 hover:bg-red-400/10 rounded-lg"
                            >
                                حذف
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredCategories.length === 0 && !isLoading">
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">لا توجد أصناف مطابقة للبحث</td>
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
import { ref, onMounted, computed } from "vue";
import { useApi } from "../../../composables/useApi";
import CategoryModal from "./CategoryModal.vue";

const { apiCall, isLoading, Swal } = useApi();
const categories = ref([]);
const search = ref("");
const showModal = ref(false);
const editingCategory = ref(null);

onMounted(() => {
    fetchCategories();
});

const fetchCategories = async () => {
    try {
        const data = await apiCall("/categories");
        categories.value = data.data || data;
    } catch (err) {
        console.error("Error fetching categories:", err);
    }
};

const openModal = (category = null) => {
    editingCategory.value = category;
    showModal.value = true;
};

const saveCategory = async (categoryData) => {
    try {
        if (categoryData.id) {
            await apiCall(`/categories/${categoryData.id}`, {
                method: "POST", // Laravel usually expects POST for update with _method: 'PUT' if using FormData, but here we use JSON
                body: { ...categoryData, _method: 'PUT' }
            });
        } else {
            await apiCall("/categories", {
                method: "POST",
                body: categoryData
            });
        }
        showModal.value = false;
        fetchCategories();
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: categoryData.id ? 'تم تحديث الصنف بنجاح' : 'تم إضافة الصنف بنجاح',
            timer: 2000,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff'
        });
    } catch (err) {
        // error handled by useApi
    }
};

const deleteCategory = async (category) => {
    const result = await Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `لن تتمكن من استعادة الصنف "${category.name}"!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذفه!',
        cancelButtonText: 'إلغاء',
        background: '#1f2937',
        color: '#fff',
        reverseButtons: true
    });

    if (result.isConfirmed) {
        try {
            await apiCall(`/categories/${category.id}`, { method: "DELETE" });
            fetchCategories();
            Swal.fire({
                title: 'تم الحذف!',
                text: 'تم حذف الصنف بنجاح.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#1f2937',
                color: '#fff'
            });
        } catch (err) {
            // error handled by useApi
        }
    }
};

const filteredCategories = computed(() => {
    return categories.value.filter(c => 
        c.name.toLowerCase().includes(search.value.toLowerCase())
    );
});
</script>


