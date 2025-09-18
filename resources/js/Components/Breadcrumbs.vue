<template>
    <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-1">
            <!-- Home Icon -->
            <li class="flex items-center">
                <Link
                    :href="route('welcome')"
                    class="group flex items-center gap-2 rounded-lg px-3 py-2 text-gray-500 transition-colors duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                >
                    <i
                        class="fa fa-home text-sm transition-transform duration-200 group-hover:scale-110"
                    ></i>
                    <span class="hidden sm:inline">{{ $t('home') }}</span>
                </Link>
            </li>

            <!-- Separator -->
            <li class="flex items-center">
                <i
                    class="fa fa-chevron-right mx-2 text-xs text-gray-300 dark:text-gray-600"
                ></i>
            </li>

            <!-- Dynamic Breadcrumbs -->
            <li
                v-for="(item, index) in items"
                :key="index"
                class="flex items-center"
            >
                <Link
                    v-if="item.href && index < items.length - 1"
                    :href="item.href"
                    class="group rounded-lg px-3 py-2 text-gray-500 transition-colors duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                >
                    <span class="group-hover:underline">{{ item.name }}</span>
                </Link>
                <span
                    v-else
                    class="rounded-lg border border-blue-200 bg-blue-100 px-3 py-2 font-medium text-gray-900 dark:border-blue-700 dark:bg-blue-900/30 dark:text-white"
                >
                    {{ item.name }}
                </span>

                <!-- Separator (except for last item) -->
                <i
                    v-if="index < items.length - 1"
                    class="fa fa-chevron-right mx-2 text-xs text-gray-300 dark:text-gray-600"
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
