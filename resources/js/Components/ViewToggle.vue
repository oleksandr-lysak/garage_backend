<template>
    <div
        class="rounded-lg border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-700 dark:bg-gray-800"
    >
        <div class="flex items-center space-x-1">
            <!-- Grid View -->
            <button
                @click="$emit('update:modelValue', 'grid')"
                :class="[
                    'flex h-10 w-10 items-center justify-center rounded-lg transition-all duration-200',
                    modelValue === 'grid'
                        ? 'bg-blue-500 text-white shadow-md'
                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700',
                ]"
                :title="$t('masters.view_options.grid')"
            >
                <i class="fa fa-th-large text-sm"></i>
            </button>

            <!-- List View -->
            <button
                @click="$emit('update:modelValue', 'list')"
                :class="[
                    'flex h-10 w-10 items-center justify-center rounded-lg transition-all duration-200',
                    modelValue === 'list'
                        ? 'bg-blue-500 text-white shadow-md'
                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700',
                ]"
                :title="$t('masters.view_options.list')"
            >
                <i class="fa fa-list text-sm"></i>
            </button>

            <!-- Map View -->
            <button
                @click="$emit('update:modelValue', 'map')"
                :class="[
                    'flex h-10 w-10 items-center justify-center rounded-lg transition-all duration-200',
                    modelValue === 'map'
                        ? 'bg-blue-500 text-white shadow-md'
                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700',
                ]"
                :title="$t('masters.view_options.map')"
            >
                <i class="fa fa-map-marker-alt text-sm"></i>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface Props {
    modelValue: 'grid' | 'list' | 'map';
}

defineProps<Props>();
defineEmits<{
    'update:modelValue': [value: 'grid' | 'list' | 'map'];
}>();
</script>
