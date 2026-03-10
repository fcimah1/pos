<template>
    <div class="space-y-6 text-right font-tajawal" dir="rtl">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-black text-white">إعدادات النظام والشركة</h1>
            <div class="flex gap-3">
                <button
                    v-if="activeTab === 'branches'"
                    @click="openBranchModal()"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة فرع جديد
                </button>
            </div>
        </div>

        <!-- التبويبات -->
        <div class="flex border-b border-gray-700 bg-gray-800/30 rounded-t-2xl overflow-hidden">
            <button 
                @click="activeTab = 'company'" 
                :class="activeTab === 'company' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-300'"
                class="flex-1 py-4 font-black transition-all flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                بيانات الشركة
            </button>
            <button 
                @click="activeTab = 'branches'" 
                :class="activeTab === 'branches' ? 'bg-indigo-600/20 text-indigo-400 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-300'"
                class="flex-1 py-4 font-black transition-all flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                إدارة الفروع
            </button>
        </div>

        <!-- محتوى تبويب الشركة -->
        <div v-if="activeTab === 'company'" class="bg-gray-800/50 backdrop-blur-md rounded-2xl border border-gray-700 p-8 shadow-xl">
            <form @submit.prevent="saveCompanySettings" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Right Side: Info -->
                    <div class="space-y-6">
                        <!-- Logo Upload Section -->
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-400 font-black">شعار الشركة (اللوجو)</label>
                            <div class="flex items-center gap-6 bg-gray-900/40 p-5 rounded-2xl border border-dashed border-gray-700 group hover:border-blue-500/50 transition-colors">
                                <div class="relative">
                                    <img 
                                        v-if="companyForm.company_logo" 
                                        :src="companyForm.company_logo" 
                                        class="w-24 h-24 rounded-full object-cover border-4 border-gray-800 shadow-2xl ring-2 ring-blue-500/20"
                                    />
                                    <div v-else class="w-24 h-24 rounded-full bg-gray-800 flex items-center justify-center border-4 border-gray-700 text-gray-600">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <button 
                                        v-if="companyForm.company_logo"
                                        type="button"
                                        @click="companyForm.company_logo = ''"
                                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-400 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                <div class="flex-1 space-y-3">
                                    <p class="text-sm text-gray-400 font-bold">بإمكانك إضافة شعار للمؤسسة</p>
                                    <p class="text-xs text-gray-500">يفضل صورة مربعة (PNG/JPG) بحد أقصى 2 ميجابايت</p>
                                    <button 
                                        type="button"
                                        @click="fileInput.click()"
                                        class="bg-gray-700 hover:bg-blue-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition-all flex items-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                        اختر صورة الشعار
                                    </button>
                                    <input 
                                        type="file" 
                                        ref="fileInput"
                                        class="hidden" 
                                        accept="image/*"
                                        @change="handleLogoUpload"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400">اسم الشركة / المنشأة</label>
                            <input
                                v-model="companyForm.company_name"
                                type="text"
                                class="w-full bg-gray-900 border-gray-700 text-white rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400">العنوان الرئيسي</label>
                            <textarea
                                v-model="companyForm.company_address"
                                rows="3"
                                class="w-full bg-gray-900 border-gray-700 text-white rounded-xl p-3 focus:ring-2 focus:ring-blue-500 transition-all"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Left Side: Phones -->
                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400">رقم الهاتف 1 (الرئيسي)</label>
                            <input v-model="companyForm.company_phone_1" type="text" class="w-full bg-gray-900 border-gray-700 text-white rounded-xl p-3 text-left font-mono" dir="ltr" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400">رقم الهاتف 2</label>
                            <input v-model="companyForm.company_phone_2" type="text" class="w-full bg-gray-900 border-gray-700 text-white rounded-xl p-3 text-left font-mono" dir="ltr" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-400">رقم الهاتف 3</label>
                            <input v-model="companyForm.company_phone_3" type="text" class="w-full bg-gray-900 border-gray-700 text-white rounded-xl p-3 text-left font-mono" dir="ltr" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-start">
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-12 py-3 rounded-xl font-black transition-all shadow-xl shadow-blue-900/30 disabled:opacity-50"
                    >
                        {{ isLoading ? 'جاري الحفظ...' : 'حفظ الإعدادات' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- محتوى تبويب الفروع -->
        <div v-else class="bg-gray-800 rounded-2xl overflow-hidden shadow-2xl border border-gray-700 overflow-x-auto">
            <table class="w-full text-right text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">الفرع</th>
                        <th class="px-6 py-4">الكود</th>
                        <th class="px-6 py-4">أرقام الهاتف</th>
                        <th class="px-6 py-4">الضريبة</th>
                        <th class="px-6 py-4">الحالة</th>
                        <th class="px-6 py-4 text-left">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <tr v-for="branch in branches" :key="branch.id" class="hover:bg-gray-750/50 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="font-bold text-white group-hover:text-blue-400 transition-colors">{{ branch.name }}</p>
                            <p class="text-xs text-gray-500">{{ branch.address }}</p>
                        </td>
                        <td class="px-6 py-4 font-mono text-gray-400">{{ branch.code }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col text-xs space-y-1">
                                <span v-if="branch.phone" class="font-mono">{{ branch.phone }}</span>
                                <span v-if="branch.phone_2" class="font-mono text-gray-500">{{ branch.phone_2 }}</span>
                                <span v-if="branch.phone_3" class="font-mono text-gray-500">{{ branch.phone_3 }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-amber-400 font-bold">{{ branch.tax_rate }}%</td>
                        <td class="px-6 py-4">
                            <span :class="branch.is_active ? 'bg-green-600/10 text-green-400 border-green-600/20' : 'bg-red-600/10 text-red-400 border-red-600/20'" class="px-3 py-1 rounded-full text-xs font-bold border">
                                {{ branch.is_active ? 'نشط' : 'متوقف' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-left space-x-reverse space-x-3">
                            <button @click="openBranchModal(branch)" class="text-blue-400 hover:text-blue-300 transition-colors p-2 hover:bg-blue-400/10 rounded-lg">تعديل</button>
                            <button @click="deleteBranch(branch)" class="text-red-400 hover:text-red-300 transition-colors p-2 hover:bg-red-400/10 rounded-lg">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="branches.length === 0 && !isLoading">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">لا توجد فروع مضافة حالياً</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- مودال الفرع -->
        <BranchModal
            :show="showBranchModal"
            :branch="editingBranch"
            :loading="isLoading"
            @close="showBranchModal = false"
            @save="saveBranch"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';
import BranchModal from './BranchModal.vue';

console.log('Settings Index component loading...');
const { apiCall, isLoading, Swal } = useApi();
const activeTab = ref('company');
const fileInput = ref(null);

const companyForm = ref({
    company_name: '',
    company_address: '',
    company_phone_1: '',
    company_phone_2: '',
    company_phone_3: '',
    company_logo: ''
});

const branches = ref([]);
const showBranchModal = ref(false);
const editingBranch = ref(null);

const handleLogoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'حجم الملف كبير',
                text: 'يجب أن لا يتجاوز حجم الصورة 2 ميجابايت',
                background: '#1f2937',
                color: '#fff'
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            companyForm.value.company_logo = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};


onMounted(() => {
    fetchCompanySettings();
    fetchBranches();
});

const fetchCompanySettings = async () => {
    try {
        const resp = await apiCall('/company/settings');
        if (!resp) return;
        
        const data = resp.data || resp;
        
        // Map raw settings to form
        Object.keys(companyForm.value).forEach(key => {
            if (data && data[key] !== undefined) {
                companyForm.value[key] = data[key];
            }
        });
    } catch (err) {
        console.error('Error fetching settings:', err);
    }
};

const saveCompanySettings = async () => {
    try {
        await apiCall('/company/settings', {
            method: 'POST',
            body: companyForm.value
        });
        
        Swal.fire({
            icon: 'success',
            title: 'تم الحفظ',
            text: 'تم تحديث بيانات الشركة بنجاح',
            timer: 2000,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff'
        });
    } catch (err) {
        // Handled by useApi
    }
};

const fetchBranches = async () => {
    try {
        const data = await apiCall('/branches');
        if (!data) return;
        branches.value = Array.isArray(data) ? data : (data.data || []);
    } catch (err) {
        console.error('Error fetching branches:', err);
    }
};

const openBranchModal = (branch = null) => {
    editingBranch.value = branch;
    showBranchModal.value = true;
};

const saveBranch = async (branchData) => {
    try {
        if (branchData.id) {
            await apiCall(`/branches/${branchData.id}`, {
                method: 'POST',
                body: { ...branchData, _method: 'PUT' }
            });
        } else {
            await apiCall('/branches', {
                method: 'POST',
                body: branchData
            });
        }
        
        showBranchModal.value = false;
        fetchBranches();
        
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: branchData.id ? 'تم تحديث بيانات الفرع' : 'تم إضافة الفرع بنجاح',
            timer: 2000,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff'
        });
    } catch (err) {
        // Handled by useApi
    }
};

const deleteBranch = async (branch) => {
    const result = await Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `لن تتمكن من استعادة الفرع "${branch.name}"!`,
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
            await apiCall(`/branches/${branch.id}`, { method: 'DELETE' });
            fetchBranches();
            Swal.fire({
                title: 'تم الحذف!',
                text: 'تم حذف الفرع بنجاح.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#1f2937',
                color: '#fff'
            });
        } catch (err) {
            // Handled by useApi
        }
    }
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
