<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Initialize the form state
const form = useForm({
    title: '',
});

// Function to handle form submission
const submit = () => {
    form.post(route('tasks.store'), {
        onSuccess: () => {
            // Clear the input field on successful submission
            form.reset('title');
        },
    });
};
</script>

<template>
    <section class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">Add New Task</h2>
        </header>

        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <input 
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    placeholder="E.g., Prepare quarterly budget report"
                    required
                    autofocus
                    
                />
                <InputError class="mt-2" :message="form.errors.title" />

            </div>
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save Task</PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved</p>
                </Transition>
            </div>

        </form>

    </section>
</template>
