<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import DeleteConfirmModal from "./DeleteConfirmModal.vue";

const props = defineProps({
    tasks: {
        type: Array,
        required: true,
    }
});

const emit = defineEmits(["edit", "sort"]);

const selectedTasks = ref([]);
const showDeleteModal = ref(false);
const taskToDelete = ref(null);
const isBulkDelete = ref(false);

const toggleTaskSelection = (taskId) => {
    const index = selectedTasks.value.indexOf(taskId);
    if(index > -1) {
        selectedTasks.value.splice(index, 1);
    } else {
        selectedTasks.value.push(taskId);
    }
};

const toggleAll = () => {
    if (selectedTasks.value.length === props.tasks.length) {
        selectedTasks.value = [];
    } else {
        selectedTasks.value = props.tasks.map((t) => t.id);
    }
};

const openDeleteModal = (task) => {
    taskToDelete.value = task;
    isBulkDelete.value = false;
    showDeleteModal.value = true;
};

const openBulkDeleteModal = () => {
    isBulkDelete.value = true;
    showDeleteModal.value = true;
}

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    taskToDelete.value = null;
    isBulkDelete.value = false;
    selectedTasks.value = [];
}

const toggleCompletion = (task) => {
    router.patch(
        route("tasks.update", task.id),
        { is_completed: !task.is_completed },
        { preserveScroll: true }
    );
};

const getPriorityColor = (priority) => {
    const colors = {
        urgent: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        high: "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200",
        medium:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        low: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
    };
    return colors[priority] || colors.medium;
};

const getDisplayStatus = (task) => {
    if(task.is_completed) {
        return "Completed";
    }
    if(task.is_overdue) {
        return "Overdue";
    }

    return task.status || "Pending";
}

const getStatusColor = (status) => {
    const normalizedStatus = status.toLowerCase().replace('-', '_');
    const colors = {
        pending:
            "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
        in_progress:
            "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        completed:
            "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        overdue:
            "bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200",
        cancelled:
            "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    };
    return colors[normalizedStatus] || colors.pending;
};

const formatDate = (date) => {
    if (!date) return "No due date";
    const d = new Date(date);
    return d.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};
</script>

<template>
    <div class="overflow-x-auto">
        <!-- Bulk Actions -->
        <div
            v-if="selectedTasks.length > 0"
            class="p-4 bg-indigo-50 border-b border-indigo-200 flex items-center justify-between dark:bg-indigo-900"
        >
            <span class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                {{ selectedTasks.length }} task(s) selected
            </span>
            <button
                @click="openBulkDeleteModal"
                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition duration-200"
            >
                Delete Selected
            </button>
        </div>

        <!-- Task Table -->
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input
                            type="checkbox"
                            @change="toggleAll"
                            :checked="
                                selectedTasks.length === tasks.length &&
                                tasks.length > 0
                            "
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                    </th>
                    <th
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                    >
                        Done
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                    >
                        Status
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        @click="emit('sort', 'title')"
                    >
                        Task ↕
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        @click="emit('sort', 'priority')"
                    >
                        Priority ↕
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        @click="emit('sort', 'due_date')"
                    >
                        Due Date ↕
                    </th>
                    <th
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody
                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
            >
                <tr
                    v-if="tasks.length === 0"
                    class="text-center"
                >
                    <td colspan="7" class="px-6 py-12 text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center">
                            <svg
                                class="w-16 h-16 mb-4 text-gray-300 dark:text-gray-600"
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
                            <p class="text-lg font-medium">No tasks found</p>
                            <p class="text-sm">Create your first task to get started!</p>
                        </div>
                    </td>
                </tr>
                <tr
                    v-for="task in tasks"
                    :key="task.id"
                    class="hover:bg-gray-50 transition duration-150 dark:hover:bg-gray-700"
                    :class="{
                        'opacity-60': task.is_completed,
                        'bg-red-50 dark:bg-red-900/20': task.is_overdue && !task.is_completed,
                    }"
                >
                    <td class="px-6 py-4">
                        <input
                            type="checkbox"
                            :checked="selectedTasks.includes(task.id)"
                            @change="toggleTaskSelection(task.id)"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button
                            @click="toggleCompletion(task)"
                            class="flex items-center justify-center w-6 h-6 mx-auto rounded border-2 transition duration-200"
                            :class="
                                task.is_completed
                                    ? 'bg-green-500 border-green-500'
                                    : 'border-gray-300 hover:border-green-500'
                            "
                        >
                            <svg
                                v-if="task.is_completed"
                                class="w-4 h-4 text-white"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </td>

                    <td class="px-6 py-4">
                        <span
                            :class="getStatusColor(getDisplayStatus(task))"
                            class="px-2 py-1 text-xs font-semibold rounded-full uppercase whitespace-nowrap"
                        >
                            {{ getDisplayStatus(task) }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                :class="{
                                    'line-through text-gray-500':
                                        task.is_completed,
                                }"
                            >
                                {{ task.title }}
                            </span>
                            <span
                                v-if="task.description"
                                class="text-xs text-gray-500 mt-1 dark:text-gray-400 line-clamp-1"
                            >
                                {{ task.description }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span
                            :class="getPriorityColor(task.priority)"
                            class="px-2 py-1 text-xs font-semibold rounded-full uppercase whitespace-nowrap"
                        >
                            {{ task.priority }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span
                                class="text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap"
                                :class="{
                                    'text-red-600 font-semibold':
                                        task.is_overdue && !task.is_completed,
                                }"
                            >
                                {{ formatDate(task.due_date) }}
                            </span>
                            <span
                                v-if="task.is_overdue && !task.is_completed"
                                class="text-xs text-red-600 font-medium mt-1 whitespace-nowrap"
                            >
                                Overdue!
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        <button
                            @click="emit('edit', task)"
                            class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition duration-150 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            Edit
                        </button>
                        <button
                            @click="deleteTask(task.id)"
                            class="text-red-600 hover:text-red-900 font-medium text-sm transition duration-150 dark:text-red-400 dark:hover:text-red-300"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Delete Confirmation Modal -->
     <DeleteConfirmModal 
        :show="showDeleteModal"
        :task="taskToDelete"
        :bulk-delete="isBulkDelete"
        :task-ids="selectedTasks"
        @close="closeDeleteModal"
     />
</template>

<style scoped></style>
