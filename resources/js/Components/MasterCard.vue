<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<template>
    <Link :href="`/masters/${slug}`" class="block">
        <div
            :class="[
                'relative flex h-full flex-col overflow-hidden rounded-2xl border-2 bg-white shadow-lg transition-all duration-300 hover:shadow-2xl dark:bg-gray-800',
                available
                    ? 'border-green-500 hover:border-green-600 dark:border-green-400'
                    : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600',
            ]"
        >
            <!-- Availability Badge -->
            <div v-if="available" class="absolute right-3 top-3 z-10">
                <span
                    class="inline-flex animate-pulse items-center rounded-full border border-green-300 bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:border-green-700 dark:bg-green-900 dark:text-green-200"
                >
                    <span
                        class="mr-2 h-2 w-2 animate-ping rounded-full bg-green-500"
                    ></span>
                    {{ $t('common.available') }}
                </span>
            </div>

            <!-- Master Image -->
            <div
                class="relative h-48 flex-shrink-0 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-700 dark:to-gray-800"
            >
                <img
                    :src="imageUrl || '/images/default-master.jpg'"
                    :alt="`${$t('common.photo')} ${name}`"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />

                <!-- Rating Overlay -->
                <div
                    class="absolute bottom-3 left-3 flex items-center space-x-1 rounded-lg bg-white/90 px-2 py-1 backdrop-blur-sm dark:bg-gray-800/90"
                >
                    <i class="fa fa-star text-sm text-yellow-500"></i>
                    <span
                        class="text-sm font-semibold text-gray-900 dark:text-white"
                        >{{ rating }}</span
                    >
                    <span class="text-xs text-gray-600 dark:text-gray-400"
                        >({{ reviewsCount || 0 }})</span
                    >
                </div>

                <!-- Experience Badge -->
                <div
                    class="absolute bottom-3 right-3 rounded-lg bg-blue-500/90 px-2 py-1 text-xs font-medium text-white backdrop-blur-sm"
                >
                    {{ experience }} {{ $t('common.years') }}
                </div>
            </div>

            <!-- Master Info -->
            <div class="flex flex-1 flex-col space-y-3 p-4">
                <!-- Name and Specialization -->
                <div class="flex-1">
                    <h3
                        class="mb-2 line-clamp-1 text-lg font-bold text-gray-900 transition-colors duration-200 group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400"
                    >
                        {{ name }}
                    </h3>
                    <p
                        class="line-clamp-2 flex-1 text-sm text-gray-600 dark:text-gray-400"
                    >
                        {{ description }}
                    </p>
                </div>

                <!-- Specializations -->
                <div
                    v-if="specializations && specializations.length"
                    class="flex flex-wrap gap-1"
                >
                    <span
                        v-for="spec in specializations.slice(0, 3)"
                        :key="spec.id"
                        class="inline-flex items-center rounded-full border border-blue-200 bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:border-blue-700 dark:bg-blue-900 dark:text-blue-200"
                    >
                        {{ spec.name }}
                    </span>
                    <span
                        v-if="specializations.length > 3"
                        class="text-xs text-gray-500 dark:text-gray-400"
                    >
                        +{{ specializations.length - 3 }}
                    </span>
                </div>

                <!-- Location and Contact -->
                <div class="space-y-2">
                    <div
                        class="flex items-center text-sm text-gray-600 dark:text-gray-400"
                    >
                        <i
                            class="fa fa-location-dot mr-2 w-4 text-blue-500"
                        ></i>
                        <span class="truncate">{{ address }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div
                            class="flex items-center text-sm text-gray-600 dark:text-gray-400"
                        >
                            <i class="fa fa-phone mr-2 w-4 text-green-500"></i>
                            <span class="font-mono">{{
                                formatPhone(phone)
                            }}</span>
                        </div>

                        <div
                            class="flex items-center text-sm text-gray-600 dark:text-gray-400"
                        >
                            <i
                                class="fa fa-calendar-alt mr-2 w-4 text-purple-500"
                            ></i>
                            <span>{{ age }} {{ $t('common.years') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Services Preview -->
                <div
                    v-if="services && services.length"
                    class="border-t border-gray-100 pt-2 dark:border-gray-700"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-medium text-gray-700 dark:text-gray-300"
                        >
                            {{ $t('common.services') }}:
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ services.length }}
                            {{
                                services.length === 1
                                    ? $t('common.service')
                                    : $t('common.services')
                            }}
                        </span>
                    </div>
                    <div class="mt-1 flex flex-wrap gap-1">
                        <span
                            v-for="service in services.slice(0, 2)"
                            :key="service.id"
                            class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-300"
                        >
                            {{ service.name }}
                        </span>
                        <span
                            v-if="services.length > 2"
                            class="text-xs text-gray-500 dark:text-gray-400"
                        >
                            +{{ services.length - 2 }}
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto flex space-x-2 pt-2">
                    <button
                        @click.prevent="handleQuickView"
                        class="flex flex-1 items-center justify-center space-x-2 rounded-lg bg-blue-500 px-3 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-blue-600"
                    >
                        <i class="fa fa-eye text-sm"></i>
                        <span>{{ $t('common.view') }}</span>
                    </button>

                    <button
                        @click.prevent="handleContact"
                        class="flex flex-1 items-center justify-center space-x-2 rounded-lg bg-green-500 px-3 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-green-600"
                    >
                        <i class="fa fa-phone text-sm"></i>
                        <span>{{ $t('common.contact') }}</span>
                    </button>
                </div>
            </div>

            <!-- Hover Effect Overlay -->
            <div
                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-blue-600/0 to-blue-600/0 transition-all duration-300 group-hover:from-blue-600/5 group-hover:to-blue-600/10"
            ></div>
        </div>
    </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface Specialization {
    id: number;
    name: string;
}

interface Service {
    id: number;
    name: string;
    price?: number;
}

interface Props {
    masterId: number;
    imageUrl?: string;
    description: string;
    address: string;
    name: string;
    phone: string;
    age: number;
    rating: number;
    slug: string;
    available: boolean;
    experience?: number;
    reviewsCount?: number;
    specializations?: Specialization[];
    services?: Service[];
}

const props = withDefaults(defineProps<Props>(), {
    experience: 5,
    reviewsCount: 0,
    specializations: () => [],
    services: () => [],
});

// Format phone number for display
const formatPhone = (phone: string): string => {
    if (!phone) return '';

    // Remove all non-digit characters
    const digits = phone.replace(/\D/g, '');

    // Format Ukrainian phone number
    if (digits.startsWith('380') && digits.length === 12) {
        return `+${digits.slice(0, 3)} (${digits.slice(3, 5)}) ${digits.slice(5, 8)}-${digits.slice(8, 10)}-${digits.slice(10)}`;
    }

    // Format other phone numbers
    if (digits.length === 10) {
        return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
    }

    return phone;
};

// Handle quick view action
const handleQuickView = () => {
    // Emit event or navigate to master page
    window.location.href = `/masters/${props.slug}`;
};

// Handle contact action
const handleContact = () => {
    // Emit event or open contact modal
    if (props.phone) {
        window.location.href = `tel:${props.phone}`;
    }
};
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar-track {
    background: #374151;
}

.dark ::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Ensure all cards have the same height */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}
</style>
