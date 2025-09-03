<template>
  <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-1">
      <!-- Home Icon -->
      <li class="flex items-center">
        <Link
          :href="route('welcome')"
          class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 px-3 py-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group"
        >
          <i class="fa fa-home text-sm group-hover:scale-110 transition-transform duration-200"></i>
          <span class="hidden sm:inline">{{ $t('home') }}</span>
        </Link>
      </li>

      <!-- Separator -->
      <li class="flex items-center">
        <i class="fa fa-chevron-right text-gray-300 dark:text-gray-600 mx-2 text-xs"></i>
      </li>

      <!-- Dynamic Breadcrumbs -->
      <li v-for="(item, index) in items" :key="index" class="flex items-center">
        <Link
          v-if="item.href && index < items.length - 1"
          :href="item.href"
          class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 px-3 py-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group"
        >
          <span class="group-hover:underline">{{ item.name }}</span>
        </Link>
        <span
          v-else
          class="text-gray-900 dark:text-white font-medium px-3 py-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700"
        >
          {{ item.name }}
        </span>

        <!-- Separator (except for last item) -->
        <i
          v-if="index < items.length - 1"
          class="fa fa-chevron-right text-gray-300 dark:text-gray-600 mx-2 text-xs"
        ></i>
      </li>
    </ol>
  </nav>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface BreadcrumbItem {
  name: string;
  href?: string;
}

interface Props {
  items: BreadcrumbItem[];
}

defineProps<Props>();
</script>
