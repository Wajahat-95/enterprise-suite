<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import TaskList from '@/components/TaskList.vue';
import TaskForm from '@/components/TaskForm.vue';

// 1. DEFINE THE PROPS WE ARE RECEIVING FROM THE SERVER
const props = defineProps({
    tasks: Array, // Expecting an array of tasks
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Enterprise Task Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <TaskForm />

                    <TaskList :initialTasks="props.tasks" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
