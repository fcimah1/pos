<template>
    <div class="space-y-6 animate-fade-in text-right" dir="rtl">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">إدارة الموظفين والطيارين</h1>
            <div class="flex gap-3">
                <button
                    v-if="activeTab === 'delivery'"
                    @click="openDeliveryModal()"
                    class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-lg shadow-orange-900/20"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة طيار
                </button>
                <button
                    v-if="activeTab === 'staff'"
                    @click="openModal()"
                    class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة موظف
                </button>
            </div>
        </div>

        <!-- التبويبات -->
        <div class="flex border-b border-gray-700 bg-gray-800/30 rounded-t-2xl overflow-hidden">
            <button 
                @click="activeTab = 'staff'" 
                :class="activeTab === 'staff' ? 'bg-blue-600/20 text-blue-400 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-300'"
                class="flex-1 py-4 font-black transition-all"
            >
                الموظفون
            </button>
            <button 
                @click="activeTab = 'delivery'" 
                :class="activeTab === 'delivery' ? 'bg-orange-600/20 text-orange-400 border-b-2 border-orange-600' : 'text-gray-500 hover:text-gray-300'"
                class="flex-1 py-4 font-black transition-all"
            >
                الطيارون (الدليفري)
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
                    :placeholder="activeTab === 'staff' ? 'ابحث عن موظف...' : 'ابحث عن طيار...'"
                    class="w-full bg-gray-900/50 text-white pl-10 pr-4 py-2.5 rounded-xl focus:ring-2 focus:ring-blue-500 border-gray-700 transition-all text-right"
                />
            </div>
        </div>

        <!-- الجدول -->
        <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-2xl border border-gray-700 overflow-x-auto">
            <table class="w-full text-right text-gray-300">
                <thead class="bg-gray-700/50 text-gray-400 uppercase text-xs font-bold">
                    <tr v-if="activeTab === 'staff'">
                        <th class="px-6 py-4">الموظف</th>
                        <th class="px-6 py-4">الدور الوظيفي</th>
                        <th class="px-6 py-4">البريد الإلكتروني</th>
                        <th class="px-6 py-4 text-left">العمليات</th>
                    </tr>
                    <tr v-else>
                        <th class="px-6 py-4">الطيار</th>
                        <th class="px-6 py-4">رقم الهاتف</th>
                        <th class="px-6 py-4">الحالة</th>
                        <th class="px-6 py-4 text-left">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <template v-if="activeTab === 'staff'">
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
                                    {{ staff.designation?.name || 'موظف' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-left" dir="ltr">{{ staff.email }}</td>
                            <td class="px-6 py-4 text-left space-x-reverse space-x-3">
                                <button @click="openModal(staff)" class="text-blue-400 hover:text-blue-300 transition-colors p-2 hover:bg-blue-400/10 rounded-lg">تعديل</button>
                                <button @click="deleteStaff(staff)" class="text-red-400 hover:text-red-300 transition-colors p-2 hover:bg-red-400/10 rounded-lg">حذف</button>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr v-for="dp in filteredDelivery" :key="dp.id" class="hover:bg-gray-750/50 transition-colors group">
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-600 to-red-600 flex items-center justify-center text-white font-bold shadow-lg">
                                    {{ dp.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-white group-hover:text-orange-400 transition-colors">{{ dp.name }}</p>
                                    <p class="text-xs text-gray-500">ID: #{{ dp.id }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-left font-mono" dir="ltr">{{ dp.phone }}</td>
                            <td class="px-6 py-4 flex flex-wrap gap-2">
                                <span :class="dp.status === 'available' ? 'bg-green-600/10 text-green-400 border-green-600/20' : 'bg-gray-600/10 text-gray-400 border-gray-600/20'" class="px-3 py-1 rounded-full text-xs font-bold border">
                                    {{ dp.status === 'available' ? 'متاح' : (dp.status === 'on_delivery' ? 'في طلب' : 'غير متصل') }}
                                </span>
                                <span :class="dp.is_active ? 'bg-orange-600/10 text-orange-400 border-orange-600/20' : 'bg-red-600/10 text-red-400 border-red-600/20'" class="px-3 py-1 rounded-full text-xs font-bold border">
                                    {{ dp.is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-left space-x-reverse space-x-3">
                                <button @click="openDeliveryModal(dp)" class="text-orange-400 hover:text-orange-300 transition-colors p-2 hover:bg-orange-400/10 rounded-lg">تعديل</button>
                                <button @click="deleteDelivery(dp)" class="text-red-400 hover:text-red-300 transition-colors p-2 hover:bg-red-400/10 rounded-lg">حذف</button>
                            </td>
                        </tr>
                    </template>
                    
                    <tr v-if="(activeTab === 'staff' ? filteredStaff : filteredDelivery).length === 0 && !isLoading">
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            لا يوجد بيانات متاحة حالياً حالياً
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- المودالات -->
        <StaffModal
            :show="showModal"
            :staff="editingStaff"
            :loading="isLoading"
            @close="showModal = false"
            @save="saveStaff"
        />

        <DeliveryPersonModal
            :show="showDeliveryModal"
            :delivery-person="editingDelivery"
            :loading="isLoading"
            @close="showDeliveryModal = false"
            @save="saveDelivery"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useApi } from "../../../composables/useApi";
import StaffModal from "./StaffModal.vue";
import DeliveryPersonModal from "./DeliveryPersonModal.vue";

const { apiCall, isLoading, Swal } = useApi();
const activeTab = ref("staff");
const staffList = ref([]);
const deliveryList = ref([]);
const search = ref("");

const showModal = ref(false);
const editingStaff = ref(null);

const showDeliveryModal = ref(false);
const editingDelivery = ref(null);

onMounted(() => {
    fetchStaff();
    fetchDeliveryPersons();
});

// مراقبة تغيير التبويب لإعادة تعيين البحث
watch(activeTab, () => {
    search.value = "";
});

const fetchStaff = async () => {
    try {
        const data = await apiCall("/employees");
        staffList.value = data.data || data;
    } catch (err) {
        console.error("Error fetching staff:", err);
    }
};

const fetchDeliveryPersons = async () => {
    try {
        const data = await apiCall("/delivery-persons");
        deliveryList.value = data.data || data;
    } catch (err) {
        console.error("Error fetching delivery persons:", err);
    }
};

const openModal = (staff = null) => {
    editingStaff.value = staff;
    showModal.value = true;
};

const openDeliveryModal = (dp = null) => {
    editingDelivery.value = dp;
    showDeliveryModal.value = true;
};

const saveStaff = async (staffData) => {
    try {
        if (staffData.id) {
            await apiCall(`/employees/${staffData.id}`, {
                method: "POST",
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

const saveDelivery = async (deliveryData) => {
    try {
        if (deliveryData.id) {
            await apiCall(`/delivery-persons/${deliveryData.id}`, {
                method: "POST",
                body: { ...deliveryData, _method: 'PUT' }
            });
        } else {
            await apiCall("/delivery-persons", {
                method: "POST",
                body: deliveryData
            });
        }
        
        showDeliveryModal.value = false;
        fetchDeliveryPersons();
        
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح',
            text: deliveryData.id ? 'تم تحديث بيانات الطيار' : 'تم إضافة الطيار بنجاح',
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

const deleteDelivery = async (dp) => {
    const result = await Swal.fire({
        title: 'هل أنت متأكد؟',
        text: `لن تتمكن من استعادة الطيار "${dp.name}"!`,
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
            await apiCall(`/delivery-persons/${dp.id}`, { method: "DELETE" });
            fetchDeliveryPersons();
            Swal.fire({
                title: 'تم الحذف!',
                text: 'تم حذف الطيار بنجاح.',
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
        (s.name || '').toLowerCase().includes(search.value.toLowerCase()) ||
        (s.email || '').toLowerCase().includes(search.value.toLowerCase())
    );
});

const filteredDelivery = computed(() => {
    return deliveryList.value.filter(dp => 
        (dp.name || '').toLowerCase().includes(search.value.toLowerCase()) ||
        (dp.phone || '').toString().includes(search.value)
    );
});
</script>
