<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    info: Object,
});

const getStatusColor = (value) => {
    if (value === 'production') return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    if (value === 'local' || value === 'development') return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    if (value === true) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
};
</script>

<template>
    <Head title="System Information" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    System Information
                </h2>
                <Link
                    :href="route('admin.dashboard')"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                >
                    ← Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <!-- System Overview -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            System Overview
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">PHP Version</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ info.php_version }}
                                    </p>
                                </div>
                                <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Laravel Version</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ info.laravel_version }}
                                    </p>
                                </div>
                                <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Server Configuration -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Server Configuration
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Server Software</span>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ info.server_software }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Database Driver</span>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ info.database }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cache Driver</span>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ info.cache_driver }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Queue Driver</span>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ info.queue_driver }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</span>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ info.timezone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Environment Status -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Environment Status
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Environment</span>
                                <span :class="getStatusColor(info.environment)" class="px-3 py-1 text-xs font-semibold rounded-full uppercase">
                                    {{ info.environment }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Debug Mode</span>
                                <span :class="getStatusColor(info.debug_mode)" class="px-3 py-1 text-xs font-semibold rounded-full">
                                    {{ info.debug_mode ? 'ENABLED' : 'DISABLED' }}
                                </span>
                            </div>
                        </div>

                        <!-- Warning for Production -->
                        <div v-if="info.debug_mode && info.environment === 'production'" class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-200">Security Warning</p>
                                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                        Debug mode is enabled in production environment. This exposes sensitive information and should be disabled immediately.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>