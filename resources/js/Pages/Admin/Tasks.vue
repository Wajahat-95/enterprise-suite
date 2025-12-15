<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    tasks: Object,
    filters: Object,
});

const searchForm = useForm({
    search: props.filters?.search || "",
    priority: props.filters?.priority || "",
    status: props.filters?.status || "",
    sort: props.filters?.sort || "created_at",
    direction: props.filters?.direction || "desc",
});

const submitSearch = () => {
    searchForm.get(route("admin.tasks"), {
        preserveScroll: true,
        preserveState: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    searchForm.get(route("admin.tasks"));
};

const changeSort = (field) => {
    if (searchForm.sort === field) {
        searchForm.direction = searchForm.direction === "asc" ? "desc" : "asc";
    } else {
        searchForm.sort = field;
        searchForm.direction = "desc";
    }
    submitSearch();
};

const formatDate = (date) => {
    if (!date) return "No due date";
    return new Date(date).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

const getPriorityColor = (priority) => {
    const colors = {
        urgent: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        high: "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200",
        medium: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        low: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
    };
    return colors[priority] || colors.medium;
};

const getStatusColor = (status) => {
    const colors = {
        pending: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
        in_progress: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        completed: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        cancelled: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    };
    return colors[status] || colors.pending;
};
</script>

<template>
    <Head title="All Tasks - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                >
                    All Tasks
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
                            <!-- Search Bar -->
                            <div class="flex gap-3">
                                <input
                                    type="search"
                                    v-model="searchForm.search"
                                    placeholder="Search tasks or users..."
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <button
                                    type="submit"
                                    class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition"
                                >
                                    Search
                                </button>
                                <button
                                    type="button"
                                    @click="clearFilters"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Clear
                                </button>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="flex flex-wrap gap-3 items-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Priority:
                                </span>
                                <button
                                    type="button"
                                    @click="searchForm.priority = ''; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        !searchForm.priority
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    All
                                </button>
                                <button
                                    type="button"
                                    @click="searchForm.priority = 'urgent'; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.priority === 'urgent'
                                            ? 'bg-red-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    Urgent
                                </button>
                                <button
                                    type="button"
                                    @click="searchForm.priority = 'high'; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.priority === 'high'
                                            ? 'bg-orange-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    High
                                </button>
                                <button
                                    type="button"
                                    @click="searchForm.priority = 'medium'; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.priority === 'medium'
                                            ? 'bg-yellow-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    Medium
                                </button>
                                <button
                                    type="button"
                                    @click="searchForm.priority = 'low'; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.priority === 'low'
                                            ? 'bg-green-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    Low
                                </button>
                            </div>

                            <!-- Status Filters -->
                            <div class="flex flex-wrap gap-3 items-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status:
                                </span>
                                <button
                                    type="button"
                                    @click="searchForm.status = ''; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        !searchForm.status
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    All
                                </button>
                                <button
                                    type="button"
                                    @click="searchForm.status = 'pending'; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.status === 'pending'
                                            ? 'bg-yellow-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    Pending
                                </button>
                                <button
                                    type="button"
                                    @click="searchForm.status = 'completed'; submitSearch();"
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.status === 'completed'
                                            ? 'bg-green-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                                    ]"
                                >
                                    Completed
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tasks Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th
                                        @click="changeSort('title')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    >
                                        Task ↕
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        User
                                    </th>
                                    <th
                                        @click="changeSort('priority')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    >
                                        Priority ↕
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        Status
                                    </th>
                                    <th
                                        @click="changeSort('due_date')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    >
                                        Due Date ↕
                                    </th>
                                    <th
                                        @click="changeSort('created_at')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    >
                                        Created ↕
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-if="tasks.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No tasks found</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-for="task in tasks.data"
                                    :key="task.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                >
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ task.title }}
                                            </div>
                                            <div v-if="task.description" class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                {{ task.description }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ task.user.name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ task.user.email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="getPriorityColor(task.priority)"
                                            class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                        >
                                            {{ task.priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="getStatusColor(task.status)"
                                            class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                        >
                                            {{ task.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div :class="{ 'text-red-600 font-semibold': task.is_overdue }">
                                            {{ formatDate(task.due_date) }}
                                        </div>
                                        <div v-if="task.is_overdue" class="text-xs text-red-600 font-medium">
                                            Overdue!
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDate(task.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <Pagination :links="tasks.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>