<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';

const props = defineProps({
    show: Boolean,
    task: Object,
});

const emit = defineEmits(["close"]);

const isEditing = computed(() => !!props.task);

const form = useForm({
    title: "",
    description: "",
    priority: "medium",
    status: "pending",
    due_date: "",
    tags: [],
});

watch(
    () => props.task,
    (newTask) => {
        if(newTask) {
            form.title = newTask.title || "",
            form.description = newTask.description || "";
            form.priority = newTask.priority || "medium";
            form.status = newTask.status || "pending";
            form.due_date = newTask.due_date || "";
            form.tags = newTask.tags || [];
        } else {
            form.reset();
        }
    }
);

const submitForm = () => {
    if(isEditing.value) {
        form.patch(route("tasks.update", props.task.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close");
                form.reset();
            },
        });
    } else {
        form.post(route("tasks.store"), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close");
                form.reset();
            },
        });
    }
};

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit("close");
}
</script>
<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click="closeModal"
        >
            <div
                class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                ></div>

                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl"
                    @click.stop
                >
                    <form @submit.prevent="submitForm">
                        <div class="bg-white dark:bg-gray-800 px-6 py-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3
                                    class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                                >
                                    {{
                                        isEditing ? "Edit Task" : "Create New Task"
                                    }}
                                </h3>
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                                >
                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Title -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Task Title *
                                    </label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Enter task title..."
                                    />
                                    <p
                                        v-if="form.errors.title"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.title }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Description
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        rows="4"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Add task description..."
                                    ></textarea>
                                    <p
                                        v-if="form.errors.description"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.description }}
                                    </p>
                                </div>

                                <!-- Priority and Status Row -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Priority -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                        >
                                            Priority
                                        </label>
                                        <select
                                            v-model="form.priority"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        >
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                        >
                                            Status
                                        </label>
                                        <select
                                            v-model="form.status"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        >
                                            <option value="pending">Pending</option>
                                            <option value="in_progress">
                                                In Progress
                                            </option>
                                            <option value="completed">
                                                Completed
                                            </option>
                                            <option value="cancelled">
                                                Cancelled
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Due Date
                                    </label>
                                    <input
                                        v-model="form.due_date"
                                        type="date"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p
                                        v-if="form.errors.due_date"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.due_date }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3"
                        >
                            <button
                                type="button"
                                @click="closeModal"
                                class="w-full sm:w-auto px-6 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition duration-200"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{
                                    form.processing
                                        ? "Saving..."
                                        : isEditing
                                        ? "Update Task"
                                        : "Create Task"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>