<script setup lang="ts">
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

onMounted(() => {
  // Redirect to masters page after a short delay
  setTimeout(() => {
    router.visit('/masters');
  }, 1000);
});
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
    <div class="text-center">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500 mx-auto mb-4"></div>
      <p class="text-gray-600 dark:text-gray-400">Перенаправлення на сторінку майстрів...</p>
        </div>
    </div>
</template>

<style scoped>

</style>
