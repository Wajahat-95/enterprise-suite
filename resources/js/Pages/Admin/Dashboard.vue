<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    recentActivity: Array,
    tasksByPriority: Array,
    tasksByStatus: Array,
    userGrowth: Array,
    topUsers: Array,
});

const completionRate = computed(() => {
    if (props.stats.total_tasks === 0) return 0;
    return Math.round(
        (props.stats.completed_tasks / props.stats.total_tasks) * 100
    );
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getActionColor = (action) => {
    const colors = {
        created: "text-green-600 bg-green-100",
        updated: "text-blue-600 bg-blue-100",
        deleted: "text-red-600 bg-red-100",
        login: "text-purple-600 bg-purple-100",
        logout: "text-gray-600 bg-gray-100",
    };
    return colors[action] || "text-gray-600 bg-gray-100";
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100"
                >
                    Admin Dashboard
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('admin.users')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                    >
                        Manage Users
                    </Link>
                    <Link
                        :href="route('admin.tasks')"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                    >
                        View All Tasks
                    </Link>
                    <Link
                        :href="route('admin.system-info')"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                    >
                        System Info
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Cards -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <!-- Total Users -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Total Users
                                </p>
                                <p
                                    class="text-3xl font-bold text-gray-900 dark:text-white mt-1"
                                >
                                    {{ stats.total_users }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    {{ stats.users_today }} new today
                                </p>
                            </div>
                            <div
                                class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full"
                            >
                                <svg
                                    class="w-8 h-8 text-blue-600 dark:text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Users -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Active Users
                                </p>
                                <p
                                    class="text-3xl font-bold text-gray-900 dark:text-white mt-1"
                                >
                                    {{ stats.active_users }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    {{
                                        Math.round(
                                            (stats.active_users /
                                                stats.total_users) *
                                                100
                                        )
                                    }}% of total
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

                    <!-- Total Tasks -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Total Tasks
                                </p>
                                <p
                                    class="text-3xl font-bold text-gray-900 dark:text-white mt-1"
                                >
                                    {{ stats.total_tasks }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    {{ stats.tasks_today }} created today
                                </p>
                            </div>
                            <div
                                class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full"
                            >
                                <svg
                                    class="w-8 h-8 text-purple-600 dark:text-purple-400"
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
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-indigo-500"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Completed Tasks
                                </p>
                                <p
                                    class="text-3xl font-bold text-gray-900 dark:text-white mt-1"
                                >
                                    {{ stats.completed_tasks }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    {{ completionRate }}% completion rate
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
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks by Priority -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-orange-500"
                    >
                        <div>
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3"
                            >
                                Tasks by Priority
                            </p>
                            <div class="space-y-2">
                                <div
                                    v-for="priority in tasksByPriority"
                                    :key="priority.priority"
                                    class="flex justify-between items-center"
                                >
                                    <span
                                        class="text-sm capitalize text-gray-700 dark:text-gray-300"
                                        >{{ priority.priority }}:</span
                                    >
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white"
                                        >{{ priority.count }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks by Status -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-pink-500"
                    >
                        <div>
                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3"
                            >
                                Tasks by Status
                            </p>
                            <div class="space-y-2">
                                <div
                                    v-for="status in tasksByStatus"
                                    :key="status.status"
                                    class="flex justify-between items-center"
                                >
                                    <span
                                        class="text-sm capitalize text-gray-700 dark:text-gray-300"
                                        >{{
                                            status.status.replace("_", " ")
                                        }}:</span
                                    >
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white"
                                        >{{ status.count }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Users -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Recent Users
                            </h3>
                            <Link
                                :href="route('admin.users')"
                                class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                            >
                                View All →
                            </Link>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="user in recentUsers"
                                :key="user.id"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                            >
                                <div>
                                    <p
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ user.name }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ user.email }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        :class="
                                            user.role === 'admin'
                                                ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                                                : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                        "
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ user.role }}
                                    </span>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                    >
                                        {{ formatDate(user.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Recent Activity
                            </h3>
                            <Link
                                :href="route('admin.activity-logs')"
                                class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                            >
                                View All →
                            </Link>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="activity in recentActivity"
                                :key="activity.id"
                                class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                            >
                                <span
                                    :class="getActionColor(activity.action)"
                                    class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                >
                                    {{ activity.action }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm text-gray-900 dark:text-white truncate"
                                    >
                                        {{ activity.description }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                    >
                                        <span v-if="activity.user">{{
                                            activity.user.name
                                        }}</span>
                                        • {{ formatDate(activity.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Users Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Top Performing Users
                    </h3>
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        User
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Total Tasks
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Completed
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Completion Rate
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="user in topUsers"
                                    :key="user.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ user.name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ user.tasks_count }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ user.completed_tasks }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm"
                                    >
                                        <div class="flex items-center">
                                            <div
                                                class="w-full bg-gray-200 rounded-full h-2 mr-2 dark:bg-gray-700"
                                                style="max-width: 100px"
                                            >
                                                <div
                                                    class="bg-green-500 h-2 rounded-full"
                                                    :style="{
                                                        width:
                                                            Math.round(
                                                                (user.completed_tasks /
                                                                    user.tasks_count) *
                                                                    100
                                                            ) + '%',
                                                    }"
                                                ></div>
                                            </div>
                                            <span
                                                class="text-gray-700 dark:text-gray-300"
                                            >
                                                {{
                                                    Math.round(
                                                        (user.completed_tasks /
                                                            user.tasks_count) *
                                                            100
                                                    )
                                                }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
