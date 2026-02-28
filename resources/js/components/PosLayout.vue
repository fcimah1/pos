<template>
    <div class="flex h-screen bg-gray-900 text-white font-sans overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="w-20 bg-gray-800 flex flex-col items-center py-4 space-y-4 border-r border-gray-700"
        >
            <div class="mb-4">
                <!-- Logo/Icon -->
                <div
                    class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center font-bold text-xl"
                >
                    P
                </div>
            </div>

            <nav class="flex-1 flex flex-col gap-4 w-full px-2">
                <button
                    class="p-3 rounded-xl bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white transition-all group relative"
                >
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
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                        />
                    </svg>
                    <span
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-700 text-xs rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-50"
                        >POS</span
                    >
                </button>

                <button
                    class="p-3 rounded-xl hover:bg-gray-700 text-gray-400 hover:text-white transition-all group relative"
                >
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
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                        />
                    </svg>
                    <span
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-700 text-xs rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-50"
                        >Orders</span
                    >
                </button>
            </nav>

            <div class="mt-auto">
                <button
                    class="p-3 rounded-xl hover:bg-gray-700 text-gray-400 hover:text-white transition-all"
                >
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
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col relative overflow-hidden">
            <!-- Top Navbar -->
            <header
                class="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6 shadow-sm"
            >
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-5 w-5 text-gray-500"
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
                        </span>
                        <input
                            type="text"
                            placeholder="Search product..."
                            class="pl-10 pr-4 py-2 bg-gray-700 border-none rounded-lg text-sm text-white focus:ring-2 focus:ring-blue-500 w-64 placeholder-gray-500"
                        />
                    </div>
                    <button
                        @click="$emit('add-customer')"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-blue-900/20 transition-all flex items-center gap-2 whitespace-nowrap"
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
                        New Client
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex bg-gray-700 rounded-lg p-1">
                        <button
                            class="px-4 py-2 rounded-lg font-semibold transition shadow-sm bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2"
                        >
                            🍽️ صالة
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg font-semibold transition shadow-sm bg-white text-gray-700 border border-gray-300 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700"
                        >
                            🥡 تيك أواي
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg font-semibold transition shadow-sm bg-white text-gray-700 border border-gray-300 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700"
                        >
                            🚚 توصيل
                        </button>
                    </div>
                    <div
                        class="h-8 w-8 rounded-full bg-gray-600 flex items-center justify-center text-xs font-bold ring-2 ring-gray-700"
                    >
                        Admin
                    </div>
                </div>
            </header>

            <!-- Dynamic Page Content -->
            <div class="flex-1 overflow-auto bg-gray-900 p-4">
                <slot></slot>
            </div>
        </main>
    </div>
</template>

<script setup>
defineEmits(["add-customer"]);
</script>
