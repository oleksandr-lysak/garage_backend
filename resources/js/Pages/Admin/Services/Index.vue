<template>
    <div class="min-h-screen bg-gray-50">
        <header class="sticky top-0 z-10 backdrop-blur bg-white/70 border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Services</h1>
                <div class="flex gap-2">
                    <input v-model="search" @input="debouncedFetch" type="search" placeholder="Search"
                           class="rounded-xl bg-gray-100 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-6">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                <button class="inline-flex items-center gap-1 hover:text-gray-700" @click="toggleSort('masters_count')">
                                    Masters
                                    <i v-if="sortBy === 'masters_count' && sortDir === 'asc'" class="fa fa-sort-asc"></i>
                                    <i v-else-if="sortBy === 'masters_count' && sortDir === 'desc'" class="fa fa-sort-desc"></i>
                                    <i v-else class="fa fa-sort text-gray-300"></i>
                                </button>
                            </th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="s in services" :key="s.id" class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-gray-600">{{ s.id }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ s.name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ s.masters_count }}</td>
                            <td class="px-6 py-3 text-right space-x-3">
                                <button @click="confirmDelete(s)" class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-lg">
                <h3 class="text-lg font-semibold mb-2">Confirm deletion</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Service <span class="font-medium">"{{ preview?.service?.name }}"</span> will be deleted.
                </p>
                <div class="mb-4 space-y-2">
                    <p class="text-sm">Affected masters: <span class="font-medium">{{ preview?.affected_masters_count }}</span></p>
                    <div v-if="preview?.masters_to_delete?.length" class="text-sm">
                        The following masters will be fully deleted because this is their only service:
                        <ul class="mt-2 list-disc pl-6 max-h-40 overflow-auto">
                            <li v-for="m in preview?.masters_to_delete" :key="m.id">#{{ m.id }} — {{ m.name }}</li>
                        </ul>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button @click="closeModal" class="rounded-lg border px-4 py-2 text-sm">Cancel</button>
                    <button @click="performDelete" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

const services = ref<Array<{ id: number; name: string; masters_count: number }>>([]);
const search = ref('');
const sortBy = ref<'name' | 'masters_count'>('name');
const sortDir = ref<'asc' | 'desc'>('asc');

const showModal = ref(false);
const pendingServiceId = ref<number | null>(null);
const preview = ref<any>(null);

async function fetchData() {
    const params = new URLSearchParams();
    if (search.value) params.set('search', search.value);
    if (sortBy.value) params.set('sort_by', sortBy.value);
    if (sortDir.value) params.set('sort_dir', sortDir.value);
    const { data } = await axios.get(`/admin-api/admin-services?${params.toString()}`);
    services.value = data.data;
}

function toggleSort(column: 'masters_count') {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = column;
        sortDir.value = 'desc';
    }
    fetchData();
}

let debounceTimer: any;
function debouncedFetch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchData, 400);
}

async function confirmDelete(s: { id: number; name: string }) {
    const { data } = await axios.get(`/admin-api/admin-services/${s.id}/delete-preview`);
    preview.value = data;
    pendingServiceId.value = s.id;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    preview.value = null;
    pendingServiceId.value = null;
}

async function performDelete() {
    if (! pendingServiceId.value) return;
    await axios.delete(`/admin-api/admin-services/${pendingServiceId.value}`);
    closeModal();
    await fetchData();
}

onMounted(() => {
    fetchData();
});
</script>
