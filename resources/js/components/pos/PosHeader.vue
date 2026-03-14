<template>
    <header class="bg-gray-800 text-white px-3 md:px-6 shadow-md z-50 select-none flex justify-between items-center h-auto min-h-16 md:min-h-20 py-2 md:py-0 sticky top-0 flex-wrap gap-2 md:gap-0" dir="rtl">
        <div class="flex items-center gap-2 md:gap-6 w-full md:w-auto justify-between md:justify-start">
            <!-- الـ Logo -->
            <div class="flex items-center gap-3 cursor-pointer group" @click="$emit('go-to', '/pos')">
                <div class="bg-blue-600 p-2 rounded-xl shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="font-black text-xl tracking-tight hidden md:block font-tajawal">نظام POS المطور</span>
                    <span class="text-[10px] text-blue-400 font-bold uppercase tracking-widest hidden md:block">الإصدار الذكي</span>
                </div>
            </div>
            
            <!-- روابط التنقل -->
            <nav class="flex items-center bg-gray-900/50 backdrop-blur-md rounded-2xl p-1 gap-1 border border-gray-700/30 relative flex-wrap justify-center w-full md:w-auto mt-2 md:mt-0">
                <button 
                    @click="$emit('go-to', '/admin/settings')" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all hover:bg-gray-800 text-blue-400 hover:text-blue-300 whitespace-nowrap flex items-center gap-2"
                    :class="(currentPath || '').includes('/admin/settings') ? 'bg-blue-600/20 text-blue-400 border border-blue-600/30' : ''"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    الإعدادات
                </button>
                <button 
                    @click="$emit('go-to', '/pos')" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all hover:bg-gray-800 text-gray-400 hover:text-white whitespace-nowrap flex items-center gap-2"
                    :class="currentPath === '/pos' ? 'bg-blue-600 text-white shadow-xl shadow-blue-500/40' : 'hover:bg-gray-800 text-gray-400 hover:text-white'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    كاشير (POS)
                </button>
                <div class="relative">
                    <button @click="toggleInventoryMenu" class="px-5 py-2 rounded-xl text-sm font-bold transition-all hover:bg-gray-800 text-gray-400 hover:text-white whitespace-nowrap flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        المخزون ▾
                    </button>
                    <div v-if="showInventory" class="absolute right-0 mt-2 w-56 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden">
                        <button @click="$emit('go-to', '/admin/categories'); showInventory=false" class="block w-full text-right px-4 py-2 text-sm text-gray-300 hover:bg-gray-800">الاقسام</button>
                        <button @click="$emit('go-to', '/admin/products'); showInventory=false" class="block w-full text-right px-4 py-2 text-sm text-gray-300 hover:bg-gray-800">المنتجات</button>
                    </div>
                </div>
                <button 
                    @click="$emit('go-to', '/admin/staff')" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all hover:bg-gray-800 text-gray-400 hover:text-white whitespace-nowrap flex items-center gap-2"
                    :class="(currentPath || '').includes('/admin/staff') ? 'bg-gray-800 text-white' : ''"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    الموظفين
                </button>
                <button 
                    @click="$emit('go-to', '/admin/permissions')" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all hover:bg-gray-800 text-gray-400 hover:text-white whitespace-nowrap flex items-center gap-2"
                    :class="(currentPath || '').includes('/admin/permissions') ? 'bg-gray-800 text-white' : ''"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    الصلاحيات
                </button>
                <button 
                    @click="$emit('open-driver-settlement')" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all hover:bg-indigo-600/20 text-indigo-400 hover:text-indigo-300 whitespace-nowrap flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    تحصيل الطيارين
                </button>
                <div class="relative">
                    <button @click="toggleExpensesMenu" class="px-5 py-2 rounded-xl text-sm font-bold transition-all hover:bg-gray-800 text-amber-400 hover:text-amber-300 whitespace-nowrap flex items-center gap-2">
                        المصاريف ▾
                    </button>
                    <div v-if="showExpenses" class="absolute right-0 mt-2 w-56 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden">
                        <button @click="$emit('open-expenses'); showExpenses=false" class="block w-full text-right px-4 py-2 text-sm text-gray-300 hover:bg-gray-800">قائمة المصاريف</button>
                        <button @click="$emit('open-add-expense'); showExpenses=false" class="block w-full text-right px-4 py-2 text-sm text-green-300 hover:bg-gray-800">إضافة مصروف</button>
                    </div>
                </div>
                <div class="relative">
                    <button @click="showMenu = !showMenu" class="px-4 py-2 rounded-xl text-sm font-bold transition-all hover:bg-gray-800 text-gray-300 whitespace-nowrap flex items-center gap-2">
                        المزيد ▼
                    </button>
                    <div v-if="showMenu" class="absolute right-0 mt-2 w-56 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden">
                        <button @click="$emit('switch-shift'); showMenu=false" class="block w-full text-right px-4 py-2 text-sm text-amber-300 hover:bg-gray-800">تبديل الوردية</button>
                        <button @click="$emit('current-shift-report'); showMenu=false" class="block w-full text-right px-4 py-2 text-sm text-blue-300 hover:bg-gray-800">مبيعات الوردية الحالية</button>
                        <button @click="$emit('export-shift-pdf'); showMenu=false" class="block w-full text-right px-4 py-2 text-sm text-emerald-300 hover:bg-gray-800">تقرير الوردية الحاليه</button>
                        <button @click="$emit('export-daily-pdf'); showMenu=false" class="block w-full text-right px-4 py-2 text-sm text-emerald-300 hover:bg-gray-800">تقرير الورديات السابقه</button>
                        <button @click="$emit('open-daily-report'); showMenu=false" class="block w-full text-right px-4 py-2 text-sm text-gray-300 hover:bg-gray-800">عرض المبيعات بالورديه</button>
                        <button @click="$emit('export-products-pdf'); showMenu=false" class="block w-full text-right px-4 py-2 text-sm text-emerald-300 hover:bg-gray-800">تقرير المنتجات يومى/شهرى</button>
                    </div>
                </div>
            </nav>
        </div>
        
        <!-- أقصى اليمين (المستخدم والخروج) -->
        <div class="flex items-center gap-2 md:gap-4 shrink-0 w-full md:w-auto justify-between md:justify-end mt-2 md:mt-0 border-t border-gray-700/50 md:border-0 pt-2 md:pt-0">
            <div class="flex flex-col items-start leading-tight">
                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">المدير المسؤول</span>
                <span class="text-sm font-black text-white group-hover:text-blue-400 transition-colors">أدمن النظام</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-10 w-px bg-gray-700 mx-1 hidden md:block"></div>
                <button @click="$emit('logout')" class="p-2.5 text-gray-400 hover:bg-red-500/10 hover:text-red-500 rounded-2xl transition-all group bg-gray-800 md:bg-transparent" title="تسجيل الخروج">
                    <svg class="w-6 h-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
defineProps({
    currentPath: String
});

defineEmits(['go-to', 'logout', 'open-driver-settlement', 'open-expenses', 'open-add-expense', 'open-daily-report', 'current-shift-report', 'export-shift-pdf', 'export-daily-pdf', 'export-products-pdf', 'switch-shift']);

const showMenu = ref(false);
const showInventory = ref(false);
const showExpenses = ref(false);
const onDocClick = (e) => {
    const header = document.querySelector('header');
    if (header && !header.contains(e.target)) {
        showMenu.value = false;
        showInventory.value = false;
        showExpenses.value = false;
    }
};
const toggleInventoryMenu = () => {
    showInventory.value = !showInventory.value;
    showExpenses.value = false;
    showMenu.value = false;
};
const toggleExpensesMenu = () => {
    showExpenses.value = !showExpenses.value;
    showInventory.value = false;
    showMenu.value = false;
};
onMounted(() => document.addEventListener('click', onDocClick));
onBeforeUnmount(() => document.removeEventListener('click', onDocClick));
</script>
