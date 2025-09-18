<template>
    <div
        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 dark:border-gray-700 dark:bg-gray-800"
    >
        <!-- Mobile: Previous/Next -->
        <div class="flex flex-1 justify-between sm:hidden">
            <button
                @click="$emit('page-changed', currentPage - 1)"
                :disabled="currentPage <= 1"
                :class="[
                    'relative inline-flex items-center rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200',
                    currentPage <= 1
                        ? 'cursor-not-allowed bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500'
                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700',
                ]"
            >
                {{ $t('masters.pagination.previous') }}
            </button>

            <button
                @click="$emit('page-changed', currentPage + 1)"
                :disabled="currentPage >= totalPages"
                :class="[
                    'relative inline-flex items-center rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200',
                    currentPage >= totalPages
                        ? 'cursor-not-allowed bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500'
                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700',
                ]"
            >
                {{ $t('masters.pagination.next') }}
            </button>
        </div>

        <!-- Desktop: Full Pagination -->
        <div
            class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
        >
            <!-- Results Info -->
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $t('masters.pagination.showing') }}
                    <span class="font-medium">{{ startItem }}</span>
                    {{ $t('masters.pagination.to') }}
                    <span class="font-medium">{{ endItem }}</span>
                    {{
                        $t('masters.pagination.of_total', { total: totalItems })
                    }}
                </p>
            </div>

            <!-- Page Navigation -->
            <div>
                <nav
                    class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <!-- Previous Page -->
                    <button
                        @click="$emit('page-changed', currentPage - 1)"
                        :disabled="currentPage <= 1"
                        :class="[
                            'relative inline-flex items-center rounded-l-md border px-2 py-2 text-sm font-medium transition-colors duration-200',
                            currentPage <= 1
                                ? 'cursor-not-allowed border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-500'
                                : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700',
                        ]"
                    >
                        <span class="sr-only">{{
                            $t('masters.pagination.previous')
                        }}</span>
                        <i class="fa fa-chevron-left h-5 w-5"></i>
                    </button>

                    <!-- Page Numbers -->
                    <template v-for="page in visiblePages" :key="page">
                        <!-- Ellipsis -->
                        <span
                            v-if="page === '...'"
                            class="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300"
                        >
                            ...
                        </span>

                        <!-- Page Number -->
                        <button
                            v-else
                            @click="$emit('page-changed', page as number)"
                            :class="[
                                'relative inline-flex items-center border px-4 py-2 text-sm font-medium transition-colors duration-200',
                                page === currentPage
                                    ? 'z-10 border-blue-500 bg-blue-50 text-blue-600 dark:border-blue-400 dark:bg-blue-900/20 dark:text-blue-400'
                                    : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700',
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
                            'relative inline-flex items-center rounded-r-md border px-2 py-2 text-sm font-medium transition-colors duration-200',
                            currentPage >= totalPages
                                ? 'cursor-not-allowed border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-500'
                                : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700',
                        ]"
                    >
                        <span class="sr-only">{{
                            $t('masters.pagination.next')
                        }}</span>
                        <i class="fa fa-chevron-right h-5 w-5"></i>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Page Size Selector -->
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
            <label class="mr-2 text-sm text-gray-700 dark:text-gray-300">
                {{ $t('common.per_page') }}:
            </label>
            <select
                :value="perPage"
                @change="
                    (event) =>
                        $emit(
                            'per-page-changed',
                            Number((event.target as HTMLSelectElement).value),
                        )
                "
                class="block w-16 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
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

const endItem = computed(() =>
    Math.min(props.currentPage * props.perPage, props.totalItems),
);

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
