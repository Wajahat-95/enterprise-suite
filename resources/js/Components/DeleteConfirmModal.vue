<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    task: Object,
    bulkDelete: {
        type: Boolean,
        default: false,
    },
    taskIds: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close"]);

const confirmDelete = () => {
    if(props.bulkDelete) {
        // Bulk Delete
        router.post(
            route("tasks.bulk-delete"),
            { task_ids: props.taskIds },
            {
                preserveScroll: true,
                onSuccess: () => {
                    emit("close");
                }
            }
        );
    } else {
        // Single Delete
        router.delete(route("tasks.destroy", props.task.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close");
            },
        });
    }
};

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
            @click="emit('close')"
        >
            <div
                class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0"
            >
                <div
                    class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                ></div>

                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    @click.stop
                >
                    <div class="bg-white dark:bg-gray-800 px-6 py-5">
                        <!-- Icon -->
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30"
                        >
                            <svg
                                class="h-6 w-6 text-red-600 dark:text-red-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="mt-4 text-center">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                            >
                                {{
                                    bulkDelete
                                        ? "Delete Multiple Tasks"
                                        : "Delete Task"
                                }}
                            </h3>
                            <div class="mt-2">
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    <template v-if="bulkDelete">
                                        Are you sure you want to delete
                                        <strong class="text-red-600">{{
                                            taskIds.length
                                        }}</strong>
                                        task(s)? This action cannot be undone.
                                    </template>
                                    <template v-else>
                                        Are you sure you want to delete
                                        <strong
                                            class="text-gray-900 dark:text-gray-100"
                                            >"{{ task?.title }}"</strong
                                        >? This action cannot be undone.
                                    </template>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3"
                    >
                        <button
                            type="button"
                            @click="emit('close')"
                            class="w-full sm:w-auto px-6 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition duration-200"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="w-full sm:w-auto px-6 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            {{
                                bulkDelete
                                    ? `Delete ${taskIds.length} Task(s)`
                                    : "Delete Task"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>