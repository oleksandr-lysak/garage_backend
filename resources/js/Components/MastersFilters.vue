<template>
    <div
        class="w-full rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
    >
        <!-- Filters Header -->
        <div class="border-b border-gray-200 p-3 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fa fa-filter mr-2 text-blue-500"></i>
                    {{ $t('masters.filters.title') }}
                </h3>

                <div class="flex items-center space-x-2">
                    <button
                        @click="toggleAdvancedFilters"
                        class="text-sm text-blue-600 transition-colors duration-200 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        {{
                            showAdvancedFilters
                                ? $t('common.hide')
                                : $t('masters.search.advanced_search')
                        }}
                    </button>

                    <button
                        @click="clearAllFilters"
                        class="text-sm text-red-600 transition-colors duration-200 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    >
                        {{ $t('masters.filters.clear_filters') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Basic Filters -->
        <div class="space-y-3 p-3">
            <!-- Search Input -->
            <div class="relative">
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                >
                    <i class="fa fa-search text-gray-400"></i>
                </div>
                <input
                    v-model="filters.search"
                    type="text"
                    :placeholder="$t('masters.search.placeholder')"
                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 text-gray-900 placeholder-gray-500 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    @keyup.enter="handleSearchEnter"
                />
            </div>

            <!-- Quick Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:grid-cols-4">
                <!-- Services -->
                <div class="space-y-1">
                    <label
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ $t('masters.filters.services') }}
                    </label>
                    <select
                        v-model="filters.service_id"
                        @change="applyFilters"
                        class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">{{ $t('common.all') }}</option>
                        <option
                            v-for="service in availableServices"
                            :key="service.id"
                            :value="service.id"
                        >
                            {{ service.name }}
                        </option>
                    </select>
                </div>

                <!-- Min Rating -->
                <div class="space-y-1">
                    <label
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ $t('masters.filters.min_rating') }}
                    </label>
                    <select
                        v-model="filters.minRating"
                        @change="applyFilters"
                        class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">{{ $t('common.all') }}</option>
                        <option value="4.5">4.5+</option>
                        <option value="4.0">4.0+</option>
                        <option value="3.5">3.5+</option>
                        <option value="3.0">3.0+</option>
                    </select>
                </div>

                <!-- Available -->
                <div class="space-y-1">
                    <label
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ $t('masters.filters.available') }}
                    </label>
                    <select
                        v-model="filters.available"
                        @change="applyFilters"
                        class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">{{ $t('common.all') }}</option>
                        <option value="true">{{ $t('common.available') }}</option>
                        <option value="false">
                            {{ $t('common.not_available') }}
                        </option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div class="space-y-1">
                    <label
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ $t('masters.filters.sort_by') }}
                    </label>
                    <select
                        v-model="filters.sortBy"
                        @change="applyFilters"
                        class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option
                            v-for="sortOption in sortOptions"
                            :key="sortOption.value"
                            :value="sortOption.value"
                        >
                            {{ sortOption.label }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div
                v-if="showAdvancedFilters"
                class="space-y-3 border-t border-gray-200 pt-3 dark:border-gray-700"
            >
                <!-- Price Range -->
                <div class="space-y-1">
                    <label
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ $t('masters.filters.price_range') }}
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input
                                v-model.number="filters.minPrice"
                                type="number"
                                min="0"
                                :placeholder="$t('common.min')"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 placeholder-gray-500 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                @input="applyFilters"
                            />
                        </div>

                        <div>
                            <input
                                v-model.number="filters.maxPrice"
                                type="number"
                                min="0"
                                :placeholder="$t('common.max')"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 placeholder-gray-500 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                @input="applyFilters"
                            />
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="space-y-1">
                    <label
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ $t('masters.filters.location') }}
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <select
                                v-model="filters.city"
                                @change="applyFilters"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">{{ $t('common.all') }}</option>
                                <option
                                    v-for="city in cities"
                                    :key="city"
                                    :value="city"
                                >
                                    {{ city }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <input
                                v-model="filters.distance"
                                type="number"
                                min="1"
                                max="50"
                                :placeholder="$t('common.distance') + ' (км)'"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 placeholder-gray-500 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                @input="applyFilters"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters Display -->
            <div
                v-if="hasActiveFilters"
                class="border-t border-gray-200 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        {{ $t('common.active_filters') }}:
                    </span>
                    <button
                        @click="clearAllFilters"
                        class="text-xs text-red-600 transition-colors duration-200 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    >
                        {{ $t('masters.filters.clear_filters') }}
                    </button>
                </div>

                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-for="(value, key) in activeFilters"
                        :key="key"
                        class="inline-flex items-center rounded-full border border-blue-200 bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:border-blue-700 dark:bg-blue-900 dark:text-blue-200"
                    >
                        {{ getFilterDisplayName(key, value) }}
                        <button
                            @click="removeFilter(key)"
                            class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            <i class="fa fa-times text-xs"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface Filters {
    search: string;
    service_id: string;
    minRating: string;
    available: string;
    minPrice: number | null;
    maxPrice: number | null;
    city: string;
    distance: number | null;
    sortBy: string;
}

interface Service {
    id: number;
    name: string;
}

const props = defineProps<{
    modelValue: Filters;
    availableServices: Service[];
    cities: string[];
}>();

const emit = defineEmits<{
    'update:modelValue': [filters: Filters];
    'filters-changed': [filters: Filters];
}>();

const filters = ref<Filters>({ ...props.modelValue });
const showAdvancedFilters = ref(false);

// Sort options
const sortOptions = [
    { value: 'rating', label: t('masters.filters.sort_rating') },
    { value: 'name', label: t('masters.filters.sort_name') },
    { value: 'age', label: t('masters.filters.sort_age') },
];

// Computed properties
const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some((value) => {
        if (Array.isArray(value)) return value.length > 0;
        if (typeof value === 'number') return value !== null;
        return value !== '' && value !== null;
    });
});

const activeFilters = computed(() => {
    const active: Record<string, any> = {};
    Object.entries(filters.value).forEach(([key, value]) => {
        if (Array.isArray(value) && value.length > 0) {
            active[key] = value;
        } else if (typeof value === 'number' && value !== null) {
            active[key] = value;
        } else if (value !== '' && value !== null) {
            active[key] = value;
        }
    });
    return active;
});

// Methods
const toggleAdvancedFilters = () => {
    showAdvancedFilters.value = !showAdvancedFilters.value;
};

const applyFilters = () => {
    emit('update:modelValue', { ...filters.value });
    emit('filters-changed', { ...filters.value });
};

const clearAllFilters = () => {
    filters.value = {
        search: '',
        service_id: '',
        minRating: '',
        available: '',
        minPrice: null,
        maxPrice: null,
        city: '',
        distance: null,
        sortBy: 'rating',
    };
    applyFilters();
};

const removeFilter = (key: string) => {
    if (Array.isArray(filters.value[key as keyof Filters])) {
        (filters.value[key as keyof Filters] as any) = [];
    } else if (typeof filters.value[key as keyof Filters] === 'number') {
        (filters.value[key as keyof Filters] as any) = null;
    } else {
        (filters.value[key as keyof Filters] as any) = '';
    }
    applyFilters();
};

const getFilterDisplayName = (key: string, value: any): string => {
    switch (key) {
        case 'service_id': {
            // Find the service by id and return its name, or the value if not found
            const service = props.availableServices.find(
                (s) => s.id.toString() === value,
            );
            return service ? service.name : value;
        }
        case 'minRating':
            return `${t('masters.filters.min_rating')}: ${value}+`;
        case 'available':
            return value === 'true'
                ? t('common.available')
                : t('common.not_available');
        case 'minPrice':
            return `${t('common.min')} ${t('common.price')}: ${value}`;
        case 'maxPrice':
            return `${t('common.max')} ${t('common.price')}: ${value}`;
        case 'city':
            return value;
        case 'distance':
            return `${t('common.distance')}: ${value} км`;
        case 'sortBy': {
            const sortOption = sortOptions.find((s) => s.value === value);
            return sortOption ? sortOption.label : value;
        }
        default:
            return value;
    }
};

const handleSearchEnter = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        applyFilters();
    }
};

// Watch for prop changes
watch(
    () => props.modelValue,
    (newValue) => {
        filters.value = { ...newValue };
    },
    { deep: true },
);
</script>
