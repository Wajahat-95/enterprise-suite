<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    logs: Object,
    users: Array,
    filters: Object,
});

const searchForm = useForm({
    action: props.filters?.action || "",
    user_id: props.filters?.user_id || "",
    date_from: props.filters?.date_from || "",
    date_to: props.filters?.date_to || "",
});

const submitSearch = () => {
    searchForm.get(route("admin.activity-logs"), {
        preserveScroll: true,
        preserveState: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    searchForm.get(route("admin.activity-logs"));
};

const formatDate = (date) => {
    return new Date(date).toLocaleString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getActionColor = (action) => {
    const colors = {
        created: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        updated: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        deleted: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        login: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200",
        logout: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
    };
    return colors[action] || "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300";
};

const getActionIcon = (action) => {
    const icons = {
        created: "M12 4v16m8-8H4",
        updated: "M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z",
        deleted: "M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16",
        login: "M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1",
        logout: "M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1",
    };
    return icons[action] || "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z";
};
</script>

<template>
    <Head title="Activity Logs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100"
                >
                    Activity Logs
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
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                    <!-- Filters -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <form @submit.prevent="submitSearch" class="space-y-4">
                            <!-- Filter Row 1 -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Action Filter -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Action Type
                                    </label>
                                    <select
                                        v-model="searchForm.action"
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="">All Actions</option>
                                        <option value="created">Created</option>
                                        <option value="updated">Updated</option>
                                        <option value="deleted">Deleted</option>
                                        <option value="login">Login</option>
                                        <option value="logout">Logout</option>
                                    </select>
                                </div>

                                <!-- User Filter -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        User
                                    </label>
                                    <select
                                        v-model="searchForm.user_id"
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="">All Users</option>
                                        <option
                                            v-for="user in users"
                                            :key="user.id"
                                            :value="user.id"
                                        >
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Date From -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        From Date
                                    </label>
                                    <input
                                        v-model="searchForm.date_from"
                                        type="date"
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>

                                <!-- Date To -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        To Date
                                    </label>
                                    <input
                                        v-model="searchForm.date_to"
                                        type="date"
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3">
                                <button
                                    type="submit"
                                    class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition"
                                >
                                    Apply Filters
                                </button>
                                <button
                                    type="button"
                                    @click="clearFilters"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Activity Logs List -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div
                            v-if="logs.data.length === 0"
                            class="p-12 text-center"
                        >
                            <svg
                                class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <p
                                class="text-lg font-medium text-gray-500 dark:text-gray-400"
                            >
                                No activity logs found
                            </p>
                            <p
                                class="text-sm text-gray-400 dark:text-gray-500 mt-1"
                            >
                                Try adjusting your filters
                            </p>
                        </div>

                        <div
                            v-for="log in logs.data"
                            :key="log.id"
                            class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                        >
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div
                                    :class="getActionColor(log.action)"
                                    class="flex-shrink-0 p-3 rounded-full"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            :d="getActionIcon(log.action)"
                                        />
                                    </svg>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            :class="getActionColor(log.action)"
                                            class="px-2 py-1 text-xs font-semibold rounded-full uppercase"
                                        >
                                            {{ log.action }}
                                        </span>
                                        <span
                                            v-if="log.model_type"
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ log.model_type.split('\\').pop() }}
                                            #{{ log.model_id }}
                                        </span>
                                    </div>

                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white mb-1"
                                    >
                                        {{ log.description }}
                                    </p>

                                    <div
                                        class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <span
                                            v-if="log.user"
                                            class="flex items-center gap-1"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            {{ log.user.name }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg
                                                class="w-4 h-4"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            {{ formatDate(log.created_at) }}
                                        </span>
                                        <span
                                            v-if="log.ip_address"
                                            class="flex items-center gap-1"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            {{ log.ip_address }}
                                        </span>
                                    </div>

                                    <!-- Properties (if any) -->
                                    <div
                                        v-if="log.properties && Object.keys(log.properties).length > 0"
                                        class="mt-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg"
                                    >
                                        <p
                                            class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2"
                                        >
                                            Details:
                                        </p>
                                        <pre
                                            class="text-xs text-gray-600 dark:text-gray-400 overflow-x-auto"
                                        >{{ JSON.stringify(log.properties, null, 2) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <Pagination :links="logs.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>