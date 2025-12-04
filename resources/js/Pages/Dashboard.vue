<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import TaskList from "@/components/TaskList.vue";
import TaskForm from "@/components/TaskForm.vue";
import Pagination from "@/components/Pagination.vue";
import { Link } from "@inertiajs/vue3";

// 1. DEFINE THE PROPS WE ARE RECEIVING FROM THE SERVER
const props = defineProps({
    tasks: Object, // Prop type is now Object (Laravel Paginator object)
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Enterprise Task Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <TaskForm />

                    <div class="flex space-x-2 mb-4 justify-end">
                        <div class="flex space-x-2 items-center">
                            <span class="text-sm text-gray-600">Show:</span>
                            <Link
                                :href="route('dashboard')"
                                class="px-3 py-1 text-sm border rounded"
                                :class="{
                                    'bg-gray-200 font-semibold':
                                        !route().params.status,
                                }"
                                >All</Link
                            >
                            <Link
                                :href="
                                    route('dashboard', { status: 'completed' })
                                "
                                class="px-3 py-1 text-sm border rounded"
                                :class="{
                                    'bg-gray-200 font-semibold':
                                        route().params.status === 'completed',
                                }"
                                >Completed</Link
                            >
                            <Link
                                :href="
                                    route('dashboard', { status: 'pending' })
                                "
                                class="px-3 py-1 text-sm border rounded"
                                :class="{
                                    'bg-gray-200 font-semibold':
                                        route().params.status === 'pending',
                                }"
                                >Pending</Link
                            >
                        </div>
                    </div>

                    <TaskList :initialTasks="props.tasks.data" />

                    <Pagination :links="props.tasks.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
