<template>
    <div class="space-y-6 animate-fade-in">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">إدارة المنتجات</h1>
            <button
                @click="openModal()"
                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                إضافة منتج
            </button>
        </div>

        <!-- البحث والفلترة -->
        <div class="bg-gray-800/50 backdrop-blur-md p-4 rounded-2xl flex gap-4 border border-gray-700/50 shadow-xl">
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute right-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="ابحث عن منتج بالاسم أو الباركود..."
                    class="w-full bg-gray-900/50 text-white pr-10 pl-4 py-2.5 rounded-xl focus:ring-2 focus:ring-blue-500 border-gray-700 transition-all text-right"
                />
            </div>
            <select
                v-model="filterCategory"
                class="bg-gray-900/50 text-white px-4 py-2.5 rounded-xl border-gray-700 focus:ring-2 focus:ring-blue-500 transition-all text-right"
            >
                <option value="">كل الأصناف</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                </option>
            </select>
        </div>

        <!-- الجدول -->
        <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-2xl border border-gray-700 overflow-x-auto">
            <table class="w-full text-right text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">المنتج</th>
                        <th class="px-6 py-4">الصنف</th>
                        <th class="px-6 py-4">السعر</th>
                        <th class="px-6 py-4">التكلفة</th>
                        <th class="px-6 py-4">المخزون</th>
                        <th class="px-6 py-4 text-left">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <tr
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="hover:bg-gray-750/50 transition-colors group"
                    >
                        <td class="px-6 py-4 flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-gray-900 flex items-center justify-center text-2xl shadow-inner border border-gray-700">
                                {{ product.icon || '📦' }}
                            </div>
                            <div>
                                <p class="font-bold text-white group-hover:text-blue-400 transition-colors">
                                    {{ product.name }}
                                </p>
                                <p class="text-xs text-gray-500 font-mono">
                                    {{ product.barcode }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-600/10 text-blue-400 px-3 py-1 rounded-full text-xs font-bold border border-blue-600/20">
                                {{ product.category?.name || "بدون صنف" }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-green-400 text-lg">
                            {{ getEffectivePrice(product) }} <span class="text-[10px] text-gray-500">ج.م</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 italic">
                            {{ product.cost_price }} <span class="text-[10px]">ج.م</span>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="
                                    product.stock_quantity > 10
                                        ? 'text-green-500'
                                        : 'text-red-500 font-bold bg-red-500/10 px-2 py-0.5 rounded'
                                "
                            >
                                {{ product.stock_quantity ?? '∞' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-left space-x-reverse space-x-3">
                            <button
                                @click="openModal(product)"
                                class="text-blue-400 hover:text-blue-300 transition-colors p-2 hover:bg-blue-400/10 rounded-lg"
                            >
                                تعديل
                            </button>
                            <button
                                @click="deleteProduct(product)"
                                class="text-red-400 hover:text-red-300 transition-colors p-2 hover:bg-red-400/10 rounded-lg"
                            >
                                حذف
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredProducts.length === 0 && !isLoading">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">لا توجد منتجات مطابقة للبحث</td>
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
import { ref, onMounted, computed } from "vue";
import { useApi } from "../../../composables/useApi";
import ProductModal from "./ProductModal.vue";

const { apiCall, isLoading, Swal } = useApi();
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
    try {
        const data = await apiCall("/products");
        products.value = data.data || data;
    } catch (err) {
        console.error("Error fetching products:", err);
    }
};

const fetchCategories = async () => {
    try {
        const data = await apiCall("/categories");
        categories.value = data.data || data;
    } catch (err) {
        console.error("Error fetching categories:", err);
    }
};

const openModal = (product = null) => {
    editingProduct.value = product;
    showModal.value = true;
};

const saveProduct = async (productData) => {
    try {
        if (productData.id) {
            await apiCall(`/products/${productData.id}`, {
                method: "POST", // Laravel usually expects POST for update with _method: 'PUT'
                body: { ...productData, _method: 'PUT' }
            });
        } else {
            await apiCall("/products", {
                method: "POST",
                body: productData
            });
        }
        showModal.value = false;
        fetchProducts();
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: productData.id ? 'تم تحديث المنتج بنجاح' : 'تم إضافة المنتج بنجاح',
            timer: 2000,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff'
        });
    } catch (err) {
        // error handled by useApi
    }
};

const deleteProduct = async (product) => {
    const result = await Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `لن تتمكن من استعادة المنتج "${product.name}"!`,
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
            await apiCall(`/products/${product.id}`, { method: "DELETE" });
            fetchProducts();
            Swal.fire({
                title: 'تم الحذف!',
                text: 'تم حذف المنتج بنجاح.',
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

// حساب السعر الفعلي: من التنويعات إن وُجدت، وإلا من حقل price مباشرة
const getEffectivePrice = (product) => {
    if (product.variations && product.variations.length > 0) {
        const min = Math.min(...product.variations.map(v => parseFloat(v.price || 0)));
        return min > 0 ? min.toFixed(2) : (parseFloat(product.price) || 0).toFixed(2);
    }
    return (parseFloat(product.price) || 0).toFixed(2);
};

const filteredProducts = computed(() => {
    return products.value.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(search.value.toLowerCase()) || 
                             (p.barcode && p.barcode.includes(search.value));
        const matchesCategory = filterCategory.value === "" || p.category_id == filterCategory.value;
        return matchesSearch && matchesCategory;
    });
});
</script>

