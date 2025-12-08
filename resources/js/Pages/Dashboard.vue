<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import TaskList from "@/Components/TaskList.vue";
import TaskForm from "@/Components/TaskForm.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link, useForm } from "@inertiajs/vue3";

// 1. DEFINE THE PROPS WE ARE RECEIVING FROM THE SERVER
const props = defineProps({
    tasks: Object, // Prop type is now Object (Laravel Paginator object)
});

// 2. CREATE SEARCH FORM STATE
const searchForm = useForm({
    // Initialize the search input with the current URL parameter value
    search: route().params.search || '',
});

// 3. SEARCH SUBMISSION HANDLER
const submitSearch = () => {
    // Perform a GET request, which Inertia handles as a visit
    searchForm.get(route('dashboard'), {
        preserveScroll: true, // Keep the user's scroll position
        preserveState: true, // Important: Keeps the form input populated
        // We only pass the search term; Laravel automatically merges it with any existing status parameter.
    })
}
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
                    <form @submit.prevent="submitSearch" class="flex items-center space-x-2 mb-4">
                        <input 
                          type="search"
                          v-model="searchForm.search"
                          placeholder="Search tasks..."
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring-indigo-200 focus:ring-opacity-50"
                          >
                          <button 
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out"
                            >
                            Search
                          </button>
                          <Link :href="route('dashboard', { status: route().params.status })"
                                class="text-sm text-gray-500 hover:text-gray-800"
                          >
                        Clear
                        </Link>

                    </form>

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
