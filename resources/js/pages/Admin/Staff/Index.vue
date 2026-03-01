<template>
    <div class="space-y-6 animate-fade-in text-right" dir="rtl">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">إدارة الموظفين</h1>
            <button
                @click="openModal()"
                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                إضافة موظف
            </button>
        </div>

        <!-- البحث -->
        <div class="bg-gray-800/50 backdrop-blur-md p-4 rounded-2xl border border-gray-700/50 shadow-xl">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="ابحث عن موظف بالاسم أو البريد..."
                    class="w-full bg-gray-900/50 text-white pl-10 pr-4 py-2.5 rounded-xl focus:ring-2 focus:ring-blue-500 border-gray-700 transition-all"
                />
            </div>
        </div>

        <!-- الجدول -->
        <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-2xl border border-gray-700 overflow-x-auto">
            <table class="w-full text-right text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">الموظف</th>
                        <th class="px-6 py-4">الدور الوظيفي</th>
                        <th class="px-6 py-4">البريد الإلكتروني</th>
                        <th class="px-6 py-4 text-left">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <tr v-for="staff in filteredStaff" :key="staff.id" class="hover:bg-gray-750/50 transition-colors group">
                        <td class="px-6 py-4 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">
                                {{ staff.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold text-white group-hover:text-blue-400 transition-colors">{{ staff.name }}</p>
                                <p class="text-xs text-gray-500">ID: #{{ staff.id }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-600/10 text-blue-400 px-3 py-1 rounded-full text-xs font-bold border border-blue-600/20">
                                {{ staff.designation?.designation_name || 'موظف' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400">{{ staff.email }}</td>
                        <td class="px-6 py-4 text-left space-x-reverse space-x-3">
                            <button @click="openModal(staff)" class="text-blue-400 hover:text-blue-300 transition-colors p-2 hover:bg-blue-400/10 rounded-lg">تعديل</button>
                            <button @click="deleteStaff(staff)" class="text-red-400 hover:text-red-300 transition-colors p-2 hover:bg-red-400/10 rounded-lg">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="filteredStaff.length === 0 && !isLoading">
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">لا يوجد موظفين حالياً</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- المودال -->
        <StaffModal
            :show="showModal"
            :staff="editingStaff"
            :loading="isLoading"
            @close="showModal = false"
            @save="saveStaff"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useApi } from "../../../composables/useApi";
import StaffModal from "./StaffModal.vue";

const { apiCall, isLoading, Swal } = useApi();
const staffList = ref([]);
const search = ref("");
const showModal = ref(false);
const editingStaff = ref(null);

onMounted(() => {
    fetchStaff();
});

const fetchStaff = async () => {
    try {
        const data = await apiCall("/employees");
        staffList.value = data.data || data;
    } catch (err) {
        console.error("Error fetching staff:", err);
    }
};

const openModal = (staff = null) => {
    editingStaff.value = staff;
    showModal.value = true;
};

const saveStaff = async (staffData) => {
    try {
        if (staffData.id) {
            await apiCall(`/employees/${staffData.id}`, {
                method: "POST", // Laravel update trick
                body: { ...staffData, _method: 'PUT' }
            });
        } else {
            await apiCall("/employees", {
                method: "POST",
                body: staffData
            });
        }
        
        showModal.value = false;
        fetchStaff();
        
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: staffData.id ? 'تم تحديث بيانات الموظف' : 'تم إضافة الموظف بنجاح',
            timer: 2000,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff'
        });
    } catch (err) {
        // Handled by useApi
    }
};

const deleteStaff = async (staff) => {
    const result = await Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `لن تتمكن من استعادة الموظف "${staff.name}"!`,
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
            await apiCall(`/employees/${staff.id}`, { method: "DELETE" });
            fetchStaff();
            Swal.fire({
                title: 'تم الحذف!',
                text: 'تم حذف الموظف بنجاح.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#1f2937',
                color: '#fff'
            });
        } catch (err) {
            // handled by useApi
        }
    }
};

const filteredStaff = computed(() => {
    return staffList.value.filter(s => 
        s.name.toLowerCase().includes(search.value.toLowerCase()) ||
        s.email.toLowerCase().includes(search.value.toLowerCase())
    );
});
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
