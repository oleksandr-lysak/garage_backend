<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-2">
    <div class="flex items-center space-x-1">
      <!-- Grid View -->
      <button
        @click="$emit('update:modelValue', 'grid')"
        :class="[
          'flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-200',
          modelValue === 'grid'
            ? 'bg-blue-500 text-white shadow-md'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
        ]"
        :title="$t('masters.view_options.grid')"
      >
        <i class="fa fa-th-large text-sm"></i>
      </button>

      <!-- List View -->
      <button
        @click="$emit('update:modelValue', 'list')"
        :class="[
          'flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-200',
          modelValue === 'list'
            ? 'bg-blue-500 text-white shadow-md'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
        ]"
        :title="$t('masters.view_options.list')"
      >
        <i class="fa fa-list text-sm"></i>
      </button>

      <!-- Map View -->
      <button
        @click="$emit('update:modelValue', 'map')"
        :class="[
          'flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-200',
          modelValue === 'map'
            ? 'bg-blue-500 text-white shadow-md'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
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
