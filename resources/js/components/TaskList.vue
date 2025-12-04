<template>
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800">Enterprise Tasks</h2>
        <ul v-if="tasks.length">
            <li v-for="task in tasks" :key="task.id" 
                class="p-2 border-b last:border-b-0 flex justify-between items center transition duration-150 ease-in-out cursor-pointer hover:bg-gray-50"
                @click="toggleCompletion(task)"
                >
                <span :class="{ 'line-through text-gray-500': task.is_completed}">
                    {{  task.title  }}

                </span>

                <button 
                    @click="deleteTask(task.id)"
                    class="text-sm text-red-600 hover:text-red-900 font-medium ml-4 transition duration-150 ease-in-out"
                    aria-label="Delete Task"
                    >
                    Delete
                </button>
                <!-- <span class="text-xs text-green-500" v-if="task.is_completed">COMPLETE</span>
                <span class="text-xs text-red-500">PENDING</span> -->

            </li>
        </ul>
        <p v-else class="text-gray-500">No tasks found. Create one by visiting /create-task.</p>
    </div>
</template>

<script setup>

import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

// 1. DEFINE THE PROP (initialTasks)
const props = defineProps({
    initialTasks: {
        type: Array,
        required: true
    }
})

// 2. USE 'ref' TO COPY PROPS INTO A LOCAL, REACTIVE VARIABLE
// This allow us to modify the task list later (e.g., when deleting a task)
const tasks = ref(props.initialTasks);

watch(() => props.initialTasks, (newTasks) => {
    tasks.value = newTasks;
})

// ADD THE DELETE METHOD
const deleteTask = (id) => {
    // Confirmation is important for destructive actions (Corrected typo)
    if (confirm('Are you sure you want to delete this task?')) { // <-- ADDED OPENING BRACE
        // Use Inertia's router to send a DELETE request
        router.delete(route('tasks.destroy', id), {
            preserveScroll: true, // Keeps the scroll position
        });
    } // <-- ADDED CLOSING BRACE
};

const toggleCompletion = (task) => {
    // 1. Calculate the new completion state
    const newStatus = !task.is_completed;

    // 2. Send the PATCH request to the controller
    router.patch(route('tasks.update', task.id), {
        is_completed: newStatus,
    }, {
        preserveScroll: true,
    });
};
</script>

<style scoped>
</style>