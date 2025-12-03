<template>
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800">Enterprise Tasks</h2>
        <ul v-if="tasks.length">
            <li v-for="task in tasks" :key="task.id" class="p-2 border-b last:border-b-0 flex justify-between items center">
                <span :class="{ 'line-through text-gray-500': task.is_completed}">
                    {{  task.title  }} (User ID: {{ task.user_id }})

                </span>
                <span class="text-xs text-green-500" v-if="task.is_completed">COMPLETE</span>
                <span class="text-xs text-red-500">PENDING</span>

            </li>
        </ul>
        <p v-else class="text-gray-500">No tasks found. Create one by visiting /create-task.</p>
    </div>
</template>

<script>
import axios from 'axios';

    export default {
        name: 'TaskList',
        // 2. Initial state (data storage)
        data() {
            return {
                tasks: [], // Empty array to hold the tasks fetched from the API
            };
        },
        // 3. Lifecycle hook: Runs immediately after the component loads
        mounted() {
            this.fetchTasks();
        },

        methods: {
            fetchTasks() {
                // 4. API call to your Laravel backend route
                axios.get('/tasks')
                    .then(response => {
                        // Success: Assign the data received from the API to our 'tasks' array
                        this.tasks = response.data;
                        console.log("Tasks loaded succeccfully!");
                    })
                    .catch(error => {
                        // Error: If the API call fails
                        console.error("Error fetching tasks:", error)
                    })
            }
        }
    }
</script>

<style scoped>
</style>