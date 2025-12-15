<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import DeleteConfirmModal from "@/Components/DeleteConfirmModal.vue";

const props = defineProps({
    users: Object,
    filters: Object,
});

const searchForm = useForm({
    search: props.filters?.search || "",
    role: props.filters?.role || "",
    status: props.filters?.status || "",
    sort: props.filters?.sort || "created_at",
    direction: props.filters?.direction || "desc",
});

const editingUser = ref(null);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const editForm = useForm({
    name: "",
    email: "",
    role: "",
    is_active: true,
});
const submitSearch = () => {
    searchForm.get(route("admin.users"), {
        preserveScroll: true,
        preserveState: true,
    });
};

const clearFilters = () => {
    searchForm.reset();
    searchForm.get(route("admin.users"));
};

const openEditModal = (user) => {
    editingUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.role = user.role;
    editForm.is_active = user.is_active;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingUser.value = null;
    editForm.reset();
};

const submitEdit = () => {
    editForm.patch(route("admin.users.update", editingUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const openDeleteModal = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    userToDelete.value = null;
};

const confirmDelete = () => {
    router.delete(route("admin.users.delete", userToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
        },
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100"
                >
                    User Management
                </h2>
                <Link
                    :href="route('admin.dashboard')"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                >
                    ← Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                    <!-- Filters -->
                    <div
                        class="p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <form @submit.prevent="submitSearch" class="space-y-4">
                            <!-- Search -->
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input
                                    type="search"
                                    v-model="searchForm.search"
                                    placeholder="Search by name or email..."
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <button
                                    type="submit"
                                    class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition"
                                >
                                    Search
                                </button>
                                <button
                                    type="button"
                                    @click="clearFilters"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Clear
                                </button>
                            </div>

                            <!-- Filters -->
                            <div class="flex flex-wrap gap-3 items-center">
                                <span
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Role:</span
                                >
                                <button
                                    type="button"
                                    @click="
                                        searchForm.role = '';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        !searchForm.role
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    All
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.role = 'admin';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.role === 'admin'
                                            ? 'bg-purple-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Admins
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.role = 'user';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.role === 'user'
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Users
                                </button>

                                <span
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-4"
                                    >Status:</span
                                >
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = '';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        !searchForm.status
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    All
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = 'active';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.status === 'active'
                                            ? 'bg-green-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Active
                                </button>
                                <button
                                    type="button"
                                    @click="
                                        searchForm.status = 'inactive';
                                        submitSearch();
                                    "
                                    :class="[
                                        'px-4 py-2 text-sm font-medium rounded-lg transition',
                                        searchForm.status === 'inactive'
                                            ? 'bg-red-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    Inactive
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        User
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Role
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Tasks
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                                    >
                                        Joined
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
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td class="px-6 py-4">
                                        <div>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ user.name }}
                                            </p>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="
                                                user.role === 'admin'
                                                    ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                                                    : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                            "
                                            class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                        >
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="
                                                user.is_active
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            "
                                            class="px-2 py-1 text-xs font-semibold rounded-full"
                                        >
                                            {{
                                                user.is_active
                                                    ? "Active"
                                                    : "Inactive"
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ user.tasks_count }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right space-x-2"
                                    >
                                        <button
                                            @click="openEditModal(user)"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium text-sm dark:text-indigo-400"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="openDeleteModal(user)"
                                            class="text-red-600 hover:text-red-900 font-medium text-sm dark:text-red-400"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="p-6 border-t border-gray-200 dark:border-gray-700"
                    >
                        <Pagination :links="users.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Edit User Modal -->
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="showEditModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click="closeEditModal"
        >
            <div
                class="flex min-h-screen items-center justify-center p-4 text-center"
            >
                <div
                    class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                ></div>

                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    @click.stop
                >
                    <form @submit.prevent="submitEdit">
                        <div class="bg-white dark:bg-gray-800 px-6 py-5">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                            >
                                Edit User
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Name
                                    </label>
                                    <input
                                        v-model="editForm.name"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Email
                                    </label>
                                    <input
                                        v-model="editForm.email"
                                        type="email"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Role
                                    </label>
                                    <select
                                        v-model="editForm.role"
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>

                                <div class="flex items-center">
                                    <input
                                        v-model="editForm.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <label
                                        class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                    >
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3"
                        >
                            <button
                                type="button"
                                @click="closeEditModal"
                                class="w-full sm:w-auto px-6 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="editForm.processing"
                                class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
                            >
                                {{
                                    editForm.processing
                                        ? "Saving..."
                                        : "Save Changes"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Transition>

    <!-- Delete Confirmation Modal -->
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click="closeDeleteModal"
        >
            <div
                class="flex min-h-screen items-center justify-center p-4 text-center"
            >
                <div
                    class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                ></div>

                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    @click.stop
                >
                    <div class="bg-white dark:bg-gray-800 px-6 py-5">
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 mb-4"
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
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white text-center"
                        >
                            Delete User
                        </h3>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 mt-2 text-center"
                        >
                            Are you sure you want to delete
                            <strong class="text-gray-900 dark:text-white">{{
                                userToDelete?.name
                            }}</strong
                            >? This action cannot be undone.
                        </p>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3"
                    >
                        <button
                            type="button"
                            @click="closeDeleteModal"
                            class="w-full sm:w-auto px-6 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="w-full sm:w-auto px-6 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition"
                        >
                            Delete User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>