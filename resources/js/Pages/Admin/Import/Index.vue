<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Import Masters</h1>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <!-- Import Form -->
                <form @submit.prevent="startImport" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service</label>
                        <select v-model="form.service_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option :value="0">Auto-detect</option>
                            <option v-for="service in services" :key="service.id" :value="service.id">
                                {{ service.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">RateList URL</label>
                        <input
                            v-model="form.url"
                            type="url"
                            placeholder="https://ratelist.top/l/kyiv/rating-435"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Limit (optional)</label>
                        <input
                            v-model.number="form.limit"
                            type="number"
                            min="1"
                            placeholder="Leave empty for no limit"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="importing"
                            class="rounded-xl bg-blue-600 px-4 py-2 text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            {{ importing ? 'Importing...' : 'Start Import' }}
                        </button>
                    </div>
                </form>

                <!-- Progress -->
                <div v-if="progress" class="mt-8 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-700">Progress</div>
                        <div v-if="progress.status !== 'error'" class="text-sm text-gray-500">
                            {{ progress.processed || 0 }} total
                            ({{ progress.imported || 0 }} imported, {{ progress.skipped || 0 }} skipped)
                        </div>
                    </div>

                    <div class="relative">
                        <div class="h-4 rounded-full bg-gray-200">
                            <div
                                class="h-4 rounded-full transition-all duration-500"
                                :class="{
                                    'bg-blue-600': progress.status === 'running',
                                    'bg-green-600': progress.status === 'completed',
                                    'bg-red-600': progress.status === 'error'
                                }"
                                :style="{ width: progressWidth }"
                            ></div>
                        </div>
                    </div>

                    <div v-if="progress.error" class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error</h3>
                                <div class="mt-2 text-sm text-red-700">{{ progress.error }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-if="progress.status === 'completed'" class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Import completed</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    Successfully imported {{ progress.imported || 0 }} masters
                                    ({{ progress.skipped || 0 }} skipped)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue';
import axios from 'axios';

const services = ref([]);
const importing = ref(false);
const progress = ref(null);
const currentJobId = ref(null);
let progressInterval = null;

const form = ref({
    service_id: 0,
    url: '',
    limit: null
});

const progressWidth = computed(() => {
    if (!progress.value || progress.value.status === 'error') return '0%';
    if (progress.value.status === 'completed') return '100%';

    const total = progress.value.total_urls || form.value.limit || 100;
    if (total > 0) {
        const percentage = (progress.value.processed / total) * 100;
        return `${Math.min(percentage, 100)}%`;
    }

    return '60%';
});

async function loadServices() {
    try {
        const response = await axios.get('/admin-api/services');
        services.value = response.data;
    } catch (error) {
        console.error('Failed to load services:', error);
    }
}

async function startImport() {
    if (importing.value) return;

    try {
        importing.value = true;
        progress.value = {
            status: 'running',
            imported: 0,
            skipped: 0,
            processed: 0
        };

        const response = await axios.post('/admin-api/import/start', form.value);
        currentJobId.value = response.data.job_id;
        startProgressPolling();
    } catch (error) {
        console.error('Failed to start import:', error);
        progress.value = {
            status: 'error',
            error: error.response?.data?.message || 'Failed to start import'
        };
        importing.value = false;
    }
}

function startProgressPolling() {
    if (progressInterval) {
        clearInterval(progressInterval);
    }

    progressInterval = setInterval(async () => {
        if (!currentJobId.value) return;

        try {
            const response = await axios.get(`/admin-api/import/progress/${currentJobId.value}`);
            progress.value = response.data;

            if (response.data.status === 'completed' || response.data.status === 'error') {
                importing.value = false;
                clearInterval(progressInterval);
                progressInterval = null;
            }
        } catch (error) {
            console.error('Failed to fetch progress:', error);
            if (error.response?.status === 404) {
                importing.value = false;
                clearInterval(progressInterval);
                progressInterval = null;
                progress.value = {
                    status: 'error',
                    error: 'Import job not found or expired'
                };
            }
        }
    }, 1000);
}

onUnmounted(() => {
    if (progressInterval) {
        clearInterval(progressInterval);
    }
});

// Load services on mount
loadServices();
</script>
