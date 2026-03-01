<template>
    <div class="flex flex-col h-screen overflow-hidden bg-gray-900 text-right font-tajawal" dir="rtl">
        <!-- الهيدر الموحد -->
        <PosHeader 
            :current-path="route.path"
            @go-to="goTo" 
            @logout="logout" 
            @open-driver-settlement="showDriverSettlement = true" 
        />

        <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            <div class="max-w-7xl mx-auto">
                <router-view></router-view>
            </div>
        </div>

        <!-- مودال تسوية الطيارين (يجب أن يكون متاحاً من الهيدر في كل الصفحات) -->
        <DriverSettlementModal 
            :show="showDriverSettlement"
            :drivers="drivers"
            v-model:selected-driver-id="selectedDriverId"
            :orders="driverSettlementOrders"
            :loading="isLoadingSettlement"
            @close="showDriverSettlement = false"
            @settle="settleOrders"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useApi } from "../composables/useApi";
import PosHeader from "./pos/PosHeader.vue";
import DriverSettlementModal from "./pos/DriverSettlementModal.vue";

const router = useRouter();
const route = useRoute();
const { apiCall } = useApi();

const showDriverSettlement = ref(false);
const drivers = ref([]);
const selectedDriverId = ref(null);
const driverSettlementOrders = ref([]);
const isLoadingSettlement = ref(false);

const goTo = (path) => router.push(path);
const logout = () => {
    localStorage.removeItem("token");
    router.push("/login");
};

const fetchDrivers = async () => {
    try {
        const resp = await apiCall("/delivery-persons");
        drivers.value = resp.data || resp;
    } catch (err) {
        console.error("Error fetching drivers:", err);
    }
};

const settleOrders = async (id, ids) => {
    isLoadingSettlement.value = true;
    try {
        await apiCall("/orders/settle", { method: "POST", body: { driver_id: id, order_ids: ids } });
        alert("تمت التسوية بنجاح");
        showDriverSettlement.value = false;
    } catch (err) {
        alert(err.message);
    } finally {
        isLoadingSettlement.value = false;
    }
};

onMounted(() => {
    fetchDrivers();
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>

