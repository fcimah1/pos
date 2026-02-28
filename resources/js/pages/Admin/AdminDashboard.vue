<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat Card -->
        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-600/10 text-blue-500 rounded-xl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
                <span
                    class="text-xs font-bold text-green-500 flex items-center gap-1"
                >
                    +12.5%
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3 w-3"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </span>
            </div>
            <h3 class="text-gray-400 text-sm font-medium">Total Sales</h3>
            <p class="text-2xl font-bold text-white mt-1">$ 24,500.00</p>
        </div>

        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-600/10 text-purple-500 rounded-xl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                        />
                    </svg>
                </div>
                <span
                    class="text-xs font-bold text-green-500 flex items-center gap-1"
                >
                    +5 New
                </span>
            </div>
            <h3 class="text-gray-400 text-sm font-medium">Total Orders</h3>
            <p class="text-2xl font-bold text-white mt-1">1,245</p>
        </div>

        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-600/10 text-yellow-500 rounded-xl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                        />
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm font-medium">Products</h3>
            <p class="text-2xl font-bold text-white mt-1">48</p>
        </div>

        <div
            class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-lg"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-600/10 text-green-500 rounded-xl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm font-medium">Customers</h3>
            <p class="text-2xl font-bold text-white mt-1">840</p>
        </div>
    </div>
</template>
