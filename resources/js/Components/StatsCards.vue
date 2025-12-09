<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const completionRate = computed(() => {
    if(props.stats.total === 0) return 0;
    return Math.round((props.stats.completed / props.stats.total * 100));
});
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Tasks -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-indigo-500"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1"
                    >
                        Total Tasks
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ stats.total }}
                    </p>
                </div>
                <div
                    class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full"
                >
                    <svg
                        class="w-8 h-8 text-indigo-600 dark:text-indigo-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1"
                    >
                        Completed
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ stats.completed }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ completionRate }}% completion rate
                    </p>
                </div>
                <div
                    class="p-3 bg-green-100 dark:bg-green-900 rounded-full"
                >
                    <svg
                        class="w-8 h-8 text-green-600 dark:text-green-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1"
                    >
                        Pending
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ stats.pending }}
                    </p>
                </div>
                <div
                    class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full"
                >
                    <svg
                        class="w-8 h-8 text-yellow-600 dark:text-yellow-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Overdue Tasks -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-red-500"
        >
            <div class="flex items-center justify-between">
                <div>
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1"
                    >
                        Overdue
                    </p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ stats.overdue }}
                    </p>
                    <p
                        v-if="stats.overdue > 0"
                        class="text-xs text-red-600 dark:text-red-400 mt-1 font-medium"
                    >
                        Needs attention!
                    </p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                    <svg
                        class="w-8 h-8 text-red-600 dark:text-red-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>