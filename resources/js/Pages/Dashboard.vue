<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import TaskList from "@/Components/TaskList.vue";
import TaskForm from "@/Components/TaskForm.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

// DEFINE THE PROPS WE ARE RECEIVING FROM THE SERVER
const props = defineProps({
    tasks: Object, // Prop type is now Object (Laravel Paginator object)
    stats: Object,
    filters: Object,
});

const page = usePage();
const showTaskModal = ref(false);
const editingTask = ref(null);

// SEARCH AND FILTER FORM
const searchForm = useForm({
    search: props.filters?.search || "",
    status: props.filters?.status || "",
    priority: props.filters?.priority || "",
    sort: props.filters?.sort || "created_at",
    direction: props.filters?.direction || "desc",
});

const submitSearch = () => {
    searchForm.get(route("dashboard"), {
        preserveScroll: true,
        preserveState: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    searchForm.get(route("dashboard"));
};

const openTaskModal = (task = null) => {
    editingTask.value = task;
    showTaskModel.value = true;
};

const closeTaskModal = () => {
    showTaskModal.value = false;
    editingTask.value = null;
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

// Check for flash messages
const flashMessage = computed(() => page.props.flash?.success);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100"
                >
                    Task Manager Pro
                </h2>
                <button
                    @click="openTaskModal()"
                    class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-200 shadow-md hover:shadow-lg"
                >
                    + New Task
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div
                    v-if="flashMessage"
                    class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center"
                >
                    <svg
                        class="w-5 h-5 mr-2"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ flashMessage }}
                </div>

                <!-- Statistics Cards -->
                <StatsCards :stats="stats" class="mb-6" />

                <!-- Main Content Card -->
                <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg">
                    <!-- Filter Section -->
                    <div
                        class="p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <form @submit.prevent="submitSearch" class="space-y-4">
                            <!-- Search Bar -->
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <input
                                        type="search"
                                        v-model="searchForm.search"
                                        placeholder="Search tasks by title or description..."
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>

                                <button
                                    type="submit"
                                    class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-200 whitespace-nowrap"
                                >
                                    Search
                                </button>
                                <button
                                    type="button"
                                    @click="clearFilters"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-200 whitespace-nowrap dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Clear
                                </button>
                            </div>
                            <!-- Filter Buttons -->
                             <div class="flex flex-wrap gap-3 items-center">
                                <span
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Status:</span
                                >
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = '';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        !searchForm.status
                                            ? 'bg-indigo-600 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    All
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = 'pending';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.status === 'pending'
                                            ? 'bg-yellow-500 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Pending
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = 'in_progress';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.status === 'in_progress'
                                            ? 'bg-blue-500 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    In Progress
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = 'completed';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.status === 'completed'
                                            ? 'bg-green-500 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Completed
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = 'overdue';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.status === 'overdue'
                                            ? 'bg-red-500 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Overdue
                                </button>
                            </div>
                            <!-- Priority Filter -->
                             <div class="flex flex-wrap gap-3 items-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Priority:
                                </span>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.priority = '';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        !searchForm.priority
                                            ? 'bg-indigo-600 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    All
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.priority = 'urgent';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.priority === 'urgent'
                                            ? 'bg-red-600 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Urgent
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.priority = 'high';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.priority === 'high'
                                            ? 'bg-orange-500 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    High
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.priority = 'medium';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.priority === 'medium'
                                            ? 'bg-yellow-500 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Medium
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.priority = 'low';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition duration-200',
                                        searchForm.priority === 'low'
                                            ? 'bg-green-500 text-white shadow-md'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Low
                                </button>
                             </div>
                             <!-- Export Button -->
                              <div class="flex justify-end">
                                <Link 
                                    :href="route('tasks.export')"
                                    class="px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition duration-200"
                                
                                >
                                    📥 Export to CSV
                                </Link>
                              </div>
                        </form>
                    </div>
                    <!-- Task List -->
                    <TaskList 
                        :tasks="tasks.data"
                        @edit="openTaskModal"
                        @sort="changeSort"
                    />

                    <!-- Pagination -->
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <Pagination :links="tasks.links" />

                    </div>
                </div>
            </div>
        </div>
        <!-- Task Modal -->
         <TaskModal 
            :show="showTaskModal"
            :task="editingTask"
            @close="closeTaskModal"
         />
    </AuthenticatedLayout>
</template>
