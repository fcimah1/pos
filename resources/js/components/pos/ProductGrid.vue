<template>
    <div class="flex-1 flex flex-col z-10 w-full min-h-[500px] lg:min-h-0 font-tajawal">
        <!-- شريط أزرار نوع الطلب الفاخر -->
        <div class="bg-white px-5 pt-4 pb-3 shadow-md flex gap-3 items-center border-b border-gray-100">
            <button
                v-for="type in ['dinein', 'takeaway', 'delivery']"
                :key="type"
                @click="$emit('update:order-type', type)"
                :class="[
                    'px-6 py-2.5 rounded-xl font-black transition-all duration-300 flex items-center gap-2 transform active:scale-95 shadow-sm',
                    orderType === type
                        ? 'bg-gray-900 text-white shadow-xl shadow-gray-200 -translate-y-0.5'
                        : 'bg-gray-50 text-gray-400 hover:bg-white hover:text-gray-700 hover:shadow-md border border-transparent hover:border-gray-200',
                ]"
            >
                <span class="text-lg">{{ type === 'dinein' ? '🍽️' : (type === 'takeaway' ? '🥡' : '🚚') }}</span>
                <span class="text-sm tracking-tight">{{ type === 'dinein' ? 'صالة' : (type === 'takeaway' ? 'تيك أواي' : 'توصيل') }}</span>
            </button>
        </div>

        <!-- شريط البحث والخيارات الإضافية -->
        <slot name="top-bar"></slot>

        <div class="flex flex-col lg:flex-row flex-1 overflow-hidden min-h-0">
            <!-- قائمة التصنيفات الفاخرة -->
            <div class="w-full lg:w-1/5 bg-gray-50/50 border-b lg:border-b-0 lg:border-l border-gray-100 p-2 lg:p-4 flex flex-row lg:flex-col gap-2 lg:gap-3 overflow-x-auto lg:overflow-y-auto min-h-0 shadow-inner whitespace-nowrap lg:whitespace-normal large-scrollbar pb-4 lg:pb-0">
                <button
                    v-for="(items, cat) in categories"
                    :key="cat"
                    @click="$emit('update:active-category', cat)"
                    :class="[
                        'min-w-[100px] lg:w-full py-2 lg:py-4 px-3 lg:px-4 rounded-xl lg:rounded-2xl border-2 transition-all duration-300 font-black text-xs text-center flex flex-col items-center justify-center gap-1 lg:gap-2 group relative overflow-hidden shrink-0',
                        activeCategory === cat
                            ? 'bg-blue-600 text-white border-blue-600 shadow-md lg:shadow-2xl shadow-blue-200 lg:scale-[1.02]'
                            : 'bg-white text-gray-500 border-transparent shadow-sm hover:border-blue-400/50 hover:text-blue-600 hover:bg-blue-50/30',
                    ]"
                >
                    <div v-if="activeCategory === cat" class="absolute top-0 right-0 w-8 h-8 bg-white/20 rounded-full -mr-4 -mt-4 blur-xl animate-pulse hidden lg:block"></div>
                    <span class="text-xl lg:text-2xl transform group-hover:scale-110 lg:group-hover:scale-125 transition-transform duration-300">
                        {{ cat === 'بيتزا' ? '🍕' : (cat === 'وجبات سريعة' ? '🍔' : (cat === 'مقبلات' ? '🍟' : (cat === 'سلطات' ? '🥗' : (cat === 'مشروبات' ? '🥤' : (cat === 'حلويات' ? '🍰' : (cat === 'إفطار' ? '🍳' : '📁')))))) }}
                    </span>
                    <span class="leading-none tracking-tight">{{ cat }}</span>
                </button>
            </div>

            <!-- شبكة المنتجات الفاخرة -->
            <div class="flex-1 p-3 lg:p-6 flex flex-row lg:grid lg:grid-cols-3 xl:grid-cols-4 gap-3 lg:gap-6 overflow-x-auto lg:overflow-y-auto lg:overflow-x-hidden min-h-0 content-start bg-white select-none large-scrollbar">
                <button
                    v-for="item in products"
                    :key="item.id"
                    @click="$emit('add-item', item)"
                    class="group bg-gray-50/50 rounded-[2rem] p-5 border-2 border-transparent hover:border-blue-500 hover:bg-white shadow-sm hover:shadow-[0_20px_50px_rgba(59,130,246,0.15)] hover:-translate-y-2 active:scale-95 transition-all duration-500 flex flex-col items-center justify-center gap-3 relative overflow-hidden shrink-0 min-w-[140px] lg:min-w-0"
                >
                    <!-- تأثير ضوئي عند المرور -->
                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
                    
                    <div class="h-20 w-20 bg-white rounded-3xl shadow-lg flex items-center justify-center text-4xl transform group-hover:rotate-12 transition-all duration-500 border border-gray-50 group-hover:border-blue-100">
                        {{ item.icon || '🍛' }}
                    </div>
                    
                    <div class="flex flex-col items-center gap-1 w-full mt-1">
                        <span class="font-black text-gray-900 text-sm h-10 flex items-center text-center leading-tight group-hover:text-blue-600 transition-colors px-2">
                            {{ item.name }}
                        </span>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="bg-gray-900 text-white font-black text-xs px-4 py-1.5 rounded-full shadow-lg shadow-gray-200 group-hover:bg-blue-600 group-hover:shadow-blue-200 transition-all">
                                {{ item.price }} <small class="text-[8px] font-normal">ج</small>
                            </span>
                        </div>
                    </div>

                    <!-- شريط سفلي جمالي -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-12 h-1 bg-blue-500 rounded-t-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    orderType: String,
    activeCategory: String,
    categories: Object,
    products: Array
});

defineEmits(['update:order-type', 'update:active-category', 'add-item']);
</script>
