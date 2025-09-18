<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
    // Redirect to masters page after a short delay
    setTimeout(() => {
        router.visit('/masters');
    }, 1000);
});
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-gray-50 dark:bg-gray-900"
    >
        <div class="text-center">
            <div
                class="mx-auto mb-4 h-16 w-16 animate-spin rounded-full border-b-2 border-blue-500"
            ></div>
            <p class="text-gray-600 dark:text-gray-400">
                Перенаправлення на сторінку майстрів...
            </p>
        </div>
    </div>
</template>

<style scoped></style>
