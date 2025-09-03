<template>
  <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:px-6">
    <!-- Mobile: Previous/Next -->
    <div class="flex flex-1 justify-between sm:hidden">
      <button
        @click="$emit('page-changed', currentPage - 1)"
        :disabled="currentPage <= 1"
        :class="[
          'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200',
          currentPage <= 1
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500'
            : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'
        ]"
      >
        {{ $t('masters.pagination.previous') }}
      </button>

      <button
        @click="$emit('page-changed', currentPage + 1)"
        :disabled="currentPage >= totalPages"
        :class="[
          'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200',
          currentPage >= totalPages
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500'
            : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'
        ]"
      >
        {{ $t('masters.pagination.next') }}
      </button>
    </div>

    <!-- Desktop: Full Pagination -->
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <!-- Results Info -->
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          {{ $t('masters.pagination.showing') }}
          <span class="font-medium">{{ startItem }}</span>
          {{ $t('masters.pagination.to') }}
          <span class="font-medium">{{ endItem }}</span>
          {{ $t('masters.pagination.of_total', { total: totalItems }) }}
        </p>
      </div>

      <!-- Page Navigation -->
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <!-- Previous Page -->
          <button
            @click="$emit('page-changed', currentPage - 1)"
            :disabled="currentPage <= 1"
            :class="[
              'relative inline-flex items-center px-2 py-2 rounded-l-md border text-sm font-medium transition-colors duration-200',
              currentPage <= 1
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 border-gray-300 dark:border-gray-600'
                : 'bg-white text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 border-gray-300 dark:border-gray-600'
            ]"
          >
            <span class="sr-only">{{ $t('masters.pagination.previous') }}</span>
            <i class="fa fa-chevron-left h-5 w-5"></i>
          </button>

          <!-- Page Numbers -->
          <template v-for="page in visiblePages" :key="page">
            <!-- Ellipsis -->
            <span
              v-if="page === '...'"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              ...
            </span>

            <!-- Page Number -->
            <button
              v-else
              @click="$emit('page-changed', page as number)"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200',
                page === currentPage
                  ? 'z-10 bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-900/20 dark:border-blue-400 dark:text-blue-400'
                  : 'bg-white text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 border-gray-300 dark:border-gray-600'
              ]"
            >
              {{ page }}
            </button>
          </template>

          <!-- Next Page -->
          <button
            @click="$emit('page-changed', currentPage + 1)"
            :disabled="currentPage >= totalPages"
            :class="[
              'relative inline-flex items-center px-2 py-2 rounded-r-md border text-sm font-medium transition-colors duration-200',
              currentPage >= totalPages
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 border-gray-300 dark:border-gray-600'
                : 'bg-white text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 border-gray-300 dark:border-gray-600'
            ]"
          >
            <span class="sr-only">{{ $t('masters.pagination.next') }}</span>
            <i class="fa fa-chevron-right h-5 w-5"></i>
          </button>
        </nav>
      </div>
    </div>

    <!-- Page Size Selector -->
    <div class="hidden sm:flex sm:items-center sm:ml-6">
      <label class="text-sm text-gray-700 dark:text-gray-300 mr-2">
        {{ $t('common.per_page') }}:
      </label>
      <select
        :value="perPage"
        @change="(event) => $emit('per-page-changed', Number((event.target as HTMLSelectElement).value))"
        class="block w-16 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
      >
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface Props {
  currentPage: number;
  totalPages: number;
  totalItems: number;
  perPage: number;
}

const props = defineProps<Props>();

defineEmits<{
  'page-changed': [page: number];
  'per-page-changed': [perPage: number];
}>();

// Computed properties
const startItem = computed(() => (props.currentPage - 1) * props.perPage + 1);

const endItem = computed(() => Math.min(props.currentPage * props.perPage, props.totalItems));

// Generate visible page numbers with ellipsis
const visiblePages = computed(() => {
  const pages: (number | string)[] = [];
  const total = props.totalPages;
  const current = props.currentPage;

  if (total <= 7) {
    // Show all pages if total is small
    for (let i = 1; i <= total; i++) {
      pages.push(i);
    }
  } else {
    // Always show first page
    pages.push(1);

    if (current > 3) {
      pages.push('...');
    }

    // Show pages around current page
    const start = Math.max(2, current - 1);
    const end = Math.min(total - 1, current + 1);

    for (let i = start; i <= end; i++) {
      if (i !== 1 && i !== total) {
        pages.push(i);
      }
    }

    if (current < total - 2) {
      pages.push('...');
    }

    // Always show last page
    if (total > 1) {
      pages.push(total);
    }
  }

  return pages;
});
</script>
