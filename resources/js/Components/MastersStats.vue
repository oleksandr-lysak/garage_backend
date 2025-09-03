<template>
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Masters -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900"
                    >
                        <i
                            class="fa fa-users text-lg text-blue-600 dark:text-blue-400"
                        ></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        {{ $t('masters.stats.total_masters') }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ totalMasters }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Available Now -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900"
                    >
                        <i
                            class="fa fa-check-circle text-lg text-green-600 dark:text-green-400"
                        ></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        {{ $t('masters.stats.available_now') }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ availableNow }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-100 dark:bg-yellow-900"
                    >
                        <i
                            class="fa fa-star text-lg text-yellow-600 dark:text-yellow-400"
                        ></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        {{ $t('masters.stats.avg_rating') }}
                    </p>
                    <div class="flex items-center">
                        <p
                            class="mr-2 text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ averageRating.toFixed(1) }}
                        </p>
                        <div class="flex items-center">
                            <i
                                v-for="i in 5"
                                :key="i"
                                class="fa fa-star text-sm"
                                :class="{
                                    'text-yellow-400':
                                        i <= Math.round(averageRating),
                                    'text-gray-300 dark:text-gray-500':
                                        i > Math.round(averageRating),
                                }"
                            ></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Reviews -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900"
                    >
                        <i
                            class="fa fa-comments text-lg text-purple-600 dark:text-purple-400"
                        ></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        {{ $t('masters.stats.total_reviews') }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ totalReviews }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

useI18n();

interface Master {
    id: number;
    rating: number;
    available: boolean;
    reviews_count?: number;
}

interface Props {
    masters: Master[];
}

const props = defineProps<Props>();

// Computed properties for statistics
const totalMasters = computed(() => props.masters.length);

const availableNow = computed(
    () => props.masters.filter((master) => master.available).length,
);

const averageRating = computed(() => {
    if (props.masters.length === 0) return 0;
    const totalRating = props.masters.reduce(
        (sum, master) => sum + master.rating,
        0,
    );
    return totalRating / props.masters.length;
});

const totalReviews = computed(() =>
    props.masters.reduce((sum, master) => sum + (master.reviews_count || 0), 0),
);
</script>
