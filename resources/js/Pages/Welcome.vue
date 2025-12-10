<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
        <!-- Navigation -->
        <nav class="p-6">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <h1 class="text-3xl font-bold text-white">TaskManager Pro</h1>
                <div v-if="canLogin" class="space-x-4">
                    <Link
                        :href="route('login')"
                        class="px-6 py-2 text-white hover:text-gray-200 font-medium transition"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-6 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg"
                    >
                        Get Started
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-6 py-20 text-center">
            <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                Manage Tasks Like a Pro
            </h2>
            <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">
                Professional task management with advanced filtering, priorities, 
                due dates, and real-time collaboration. Built for teams and individuals 
                who demand excellence.
            </p>
            <Link
                v-if="canRegister"
                :href="route('register')"
                class="inline-block px-8 py-4 bg-white text-indigo-600 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-2xl hover:shadow-xl transform hover:scale-105"
            >
                Start Free Trial →
            </Link>
        </div>

        <!-- Features Grid -->
        <div class="max-w-7xl mx-auto px-6 py-20">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 text-white">
                    <div class="text-4xl mb-4">🎯</div>
                    <h3 class="text-2xl font-bold mb-3">Priority Management</h3>
                    <p class="text-white/80">Organize tasks by urgency with color-coded priorities</p>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 text-white">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-2xl font-bold mb-3">Analytics Dashboard</h3>
                    <p class="text-white/80">Track completion rates and productivity metrics</p>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 text-white">
                    <div class="text-4xl mb-4">🔒</div>
                    <h3 class="text-2xl font-bold mb-3">Secure & Private</h3>
                    <p class="text-white/80">Enterprise-grade security with data encryption</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center text-white/60 py-8">
            <p>© 2024 TaskManager Pro. Built with Laravel & Vue.js</p>
        </footer>
    </div>
</template>