<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Loading State -->
        <div
            v-if="!props.master?.data"
            class="flex min-h-screen items-center justify-center"
        >
            <div class="text-center">
                <div
                    class="mx-auto mb-4 h-12 w-12 animate-spin rounded-full border-b-2 border-blue-500"
                ></div>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $t('common.loading') }}
                </p>
            </div>
        </div>

        <!-- Error State -->
        <div
            v-else-if="props.master && props.master.error"
            class="flex min-h-screen items-center justify-center"
        >
            <div class="text-center">
                <i
                    class="fa fa-exclamation-triangle mb-4 text-6xl text-red-500"
                ></i>
                <h3
                    class="mb-2 text-lg font-medium text-gray-900 dark:text-white"
                >
                    {{ $t('common.error') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Майстра не знайдено
                </p>
            </div>
        </div>

        <!-- Content -->
        <div v-else-if="props.master?.data">
            <!-- SEO Meta Tags -->
            <Head>
                <title>{{ metaTags.title }}</title>
                <meta name="description" :content="metaTags.description" />
                <meta name="keywords" :content="metaTags.keywords" />
                <link rel="canonical" :href="metaTags.canonicalUrl" />

                <!-- Open Graph -->
                <meta property="og:title" :content="metaTags.ogTitle" />
                <meta
                    property="og:description"
                    :content="metaTags.ogDescription"
                />
                <meta property="og:type" content="profile" />
                <meta property="og:url" :href="metaTags.ogUrl" />
                <meta property="og:image" :content="metaTags.ogImage" />

                <!-- Twitter Card -->
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" :content="metaTags.ogTitle" />
                <meta
                    name="twitter:description"
                    :content="metaTags.ogDescription"
                />
                <meta name="twitter:image" :content="metaTags.ogImage" />
            </Head>

            <!-- Breadcrumbs -->
            <div class="absolute left-4 top-4 z-10">
                <Breadcrumbs :items="breadcrumbItems" />
            </div>

            <!-- Main Content -->
            <main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <!-- Hero Section -->
                <section
                    class="mb-8 rounded-3xl bg-white p-8 shadow-xl dark:bg-gray-800"
                >
                    <div
                        class="grid grid-cols-1 items-center gap-8 lg:grid-cols-3"
                    >
                        <!-- Master Photo -->
                        <div class="flex justify-center lg:justify-start">
                            <div class="relative">
                                <!-- Check if master and master.data exist before rendering the image -->
                                <img
                                    v-if="
                                        props.master &&
                                        props.master.data &&
                                        props.master.data.main_photo &&
                                        props.master.data.name
                                    "
                                    :src="`/${props.master.data.main_photo}`"
                                    :alt="`Фото майстра ${props.master.data.name}`"
                                    class="h-48 w-48 rounded-full border-4 border-blue-500 object-cover shadow-2xl transition-transform duration-300 hover:scale-105 lg:h-64 lg:w-64 dark:border-green-500"
                                />
                                <!-- Show a placeholder if photo or name is missing -->
                                <div
                                    v-else
                                    class="flex h-48 w-48 items-center justify-center rounded-full border-4 border-gray-300 bg-gray-100 text-gray-400 shadow-2xl lg:h-64 lg:w-64 dark:border-gray-600 dark:bg-gray-700"
                                >
                                    <i class="fa fa-user text-6xl"></i>
                                </div>
                                <!-- Availability Badge -->
                                <div
                                    :class="[
                                        'absolute -bottom-2 -right-2 flex h-8 w-8 items-center justify-center rounded-full border-4 border-white dark:border-gray-800',
                                        props.master &&
                                        props.master.data &&
                                        typeof props.master.data.available !==
                                            'undefined' &&
                                        props.master.data.available
                                            ? 'bg-green-500'
                                            : 'bg-red-500',
                                    ]"
                                >
                                    <i
                                        :class="[
                                            'fa text-sm text-white',
                                            props.master.data.available
                                                ? 'fa-check'
                                                : 'fa-times',
                                        ]"
                                    ></i>
                                </div>
                            </div>
                        </div>

                        <!-- Master Info -->
                        <div
                            class="space-y-6 text-center lg:col-span-2 lg:text-left"
                        >
                            <div>
                                <h1
                                    class="mb-4 text-4xl font-bold text-gray-900 lg:text-5xl dark:text-white"
                                >
                                    {{ props.master.data.name }}
                                </h1>
                                <p
                                    class="text-lg leading-relaxed text-gray-600 dark:text-gray-400"
                                >
                                    {{ props.master.data.description }}
                                </p>
                            </div>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                                <div class="text-center">
                                    <div
                                        class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                                    >
                                        {{ props.master.data.rating }}
                                    </div>
                                    <div
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ $t('common.rating') }}
                                    </div>
                                    <!-- Stars -->
                                    <div
                                        class="mt-1 flex justify-center lg:justify-start"
                                    >
                                        <i
                                            v-for="i in 5"
                                            :key="i"
                                            class="fa fa-star text-sm"
                                            :class="{
                                                'text-yellow-400':
                                                    i <=
                                                    Math.round(
                                                        props.master.data
                                                            .rating,
                                                    ),
                                                'text-gray-300 dark:text-gray-500':
                                                    i >
                                                    Math.round(
                                                        props.master.data
                                                            .rating,
                                                    ),
                                            }"
                                        ></i>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <div
                                        class="text-2xl font-bold text-green-600 dark:text-green-400"
                                    >
                                        {{ props.master.data.reviews_count }}
                                    </div>
                                    <div
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ $t('common.reviews') }}
                                    </div>
                                </div>

                                <div class="text-center">
                                    <div
                                        class="text-2xl font-bold text-purple-600 dark:text-purple-400"
                                    >
                                        {{ props.master.data.age }}
                                    </div>
                                    <div
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ $t('common.age') }}
                                    </div>
                                </div>

                                <div class="text-center">
                                    <div
                                        class="text-2xl font-bold text-orange-600 dark:text-orange-400"
                                    >
                                        {{ props.master.data.services.length }}
                                    </div>
                                    <div
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ $t('common.services') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div
                                class="flex flex-col justify-center gap-4 sm:flex-row lg:justify-start"
                            >
                                <div
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-300"
                                >
                                    <i
                                        class="fa fa-location-dot text-blue-500"
                                    ></i>
                                    <span>{{ props.master.data.address }}</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-300"
                                >
                                    <i class="fa fa-phone text-green-500"></i>
                                    <a
                                        :href="`tel:${props.master.data.phone}`"
                                        class="transition-colors hover:text-blue-600 dark:hover:text-blue-400"
                                    >
                                        {{ props.master.data.phone }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Services Section -->
                <section
                    class="mb-8 rounded-3xl bg-white p-8 shadow-xl dark:bg-gray-800"
                >
                    <h2
                        class="mb-6 flex items-center gap-3 text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        <i class="fa fa-tools text-blue-500"></i>
                        {{ $t('masters.services.title') }}
                    </h2>

                    <div
                        class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="service in props.master.data.services"
                            :key="service.id"
                            class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-blue-700 dark:from-blue-900 dark:to-blue-800"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500"
                                >
                                    <i
                                        class="fa fa-wrench text-xl text-white"
                                    ></i>
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ service.name }}
                                    </h3>
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{
                                            $t(
                                                'masters.services.professional_service',
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Reviews Section -->
                <section
                    class="mb-8 rounded-3xl bg-white p-8 shadow-xl dark:bg-gray-800"
                >
                    <div class="mb-6 flex items-center justify-between">
                        <h2
                            class="flex items-center gap-3 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            <i class="fa fa-star text-yellow-500"></i>
                            {{ $t('masters.reviews.title') }}
                            <span
                                class="text-lg font-normal text-gray-600 dark:text-gray-400"
                            >
                                ({{ props.master.data.reviews_count }})
                            </span>
                        </h2>

                        <button
                            @click="showReviewForm = !showReviewForm"
                            class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 font-medium text-white transition-colors duration-200 hover:bg-blue-700"
                        >
                            <i class="fa fa-plus"></i>
                            {{ $t('masters.reviews.add_review') }}
                        </button>
                    </div>

                    <!-- Add Review Form -->
                    <div
                        v-if="showReviewForm"
                        class="mb-6 rounded-2xl bg-gray-50 p-6 dark:bg-gray-700"
                    >
                        <form @submit.prevent="submitReview" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        {{ $t('masters.reviews.your_name') }}
                                    </label>
                                    <input
                                        v-model="newReview.user_name"
                                        required
                                        :placeholder="
                                            $t(
                                                'masters.reviews.your_name_placeholder',
                                            )
                                        "
                                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        {{ $t('masters.reviews.rating') }}
                                    </label>
                                    <div class="flex gap-2">
                                        <button
                                            v-for="i in 5"
                                            :key="i"
                                            type="button"
                                            @click="newReview.rating = i"
                                            :class="[
                                                'flex h-10 w-10 items-center justify-center rounded-lg border-2 transition-colors duration-200',
                                                newReview.rating >= i
                                                    ? 'border-yellow-400 bg-yellow-400 text-white'
                                                    : 'border-gray-300 text-gray-400 hover:border-yellow-300 dark:border-gray-600',
                                            ]"
                                        >
                                            <i class="fa fa-star text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    {{ $t('masters.reviews.comment') }}
                                </label>
                                <textarea
                                    v-model="newReview.comment"
                                    required
                                    :placeholder="
                                        $t(
                                            'masters.reviews.comment_placeholder',
                                        )
                                    "
                                    rows="4"
                                    class="w-full resize-none rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                ></textarea>
                            </div>

                            <div class="flex gap-3">
                                <button
                                    type="submit"
                                    class="flex items-center gap-2 rounded-xl bg-green-600 px-6 py-3 font-medium text-white transition-colors duration-200 hover:bg-green-700"
                                >
                                    <i class="fa fa-paper-plane"></i>
                                    {{ $t('masters.reviews.submit') }}
                                </button>

                                <button
                                    type="button"
                                    @click="showReviewForm = false"
                                    class="rounded-xl bg-gray-500 px-6 py-3 font-medium text-white transition-colors duration-200 hover:bg-gray-600"
                                >
                                    {{ $t('common.cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Reviews List -->
                    <div v-if="reviews && reviews.length > 0" class="space-y-4">
                        <div
                            v-for="review in reviews.filter(
                                (r) => r && (r.user_name || r.user?.name),
                            )"
                            :key="review.id || review.created_at"
                            class="rounded-2xl border border-gray-200 bg-gray-50 p-6 dark:border-gray-600 dark:bg-gray-700"
                        >
                            <div class="mb-3 flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500"
                                    >
                                        <span
                                            class="text-sm font-semibold text-white"
                                        >
                                            {{
                                                String(
                                                    review.user_name ||
                                                        review.user?.name ||
                                                        'A',
                                                )
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <h4
                                            class="font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{
                                                review.user_name ||
                                                review.user?.name ||
                                                'Anonymous'
                                            }}
                                        </h4>
                                        <div class="flex items-center gap-1">
                                            <i
                                                v-for="i in 5"
                                                :key="i"
                                                class="fa fa-star text-sm"
                                                :class="{
                                                    'text-yellow-400':
                                                        i <= review.rating,
                                                    'text-gray-300 dark:text-gray-500':
                                                        i > review.rating,
                                                }"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatDate(review.created_at) }}
                                </span>
                            </div>
                            <p
                                class="leading-relaxed text-gray-700 dark:text-gray-300"
                            >
                                {{
                                    review.review ||
                                    review.comment ||
                                    'No comment'
                                }}
                            </p>
                        </div>
                    </div>

                    <div v-else class="py-12 text-center">
                        <i
                            class="fa fa-comments mb-4 text-6xl text-gray-400"
                        ></i>
                        <h3
                            class="mb-2 text-lg font-medium text-gray-900 dark:text-white"
                        >
                            {{ $t('masters.reviews.no_reviews.title') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $t('masters.reviews.no_reviews.description') }}
                        </p>
                    </div>
                </section>

                <!-- Contact & Location Section -->
                <section
                    class="rounded-3xl bg-white p-8 shadow-xl dark:bg-gray-800"
                >
                    <h2
                        class="mb-6 flex items-center gap-3 text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        <i class="fa fa-map-marker-alt text-red-500"></i>
                        {{ $t('masters.contact.title') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <!-- Contact Info -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500"
                                >
                                    <i
                                        class="fa fa-phone text-xl text-white"
                                    ></i>
                                </div>
                                <div>
                                    <h3
                                        class="font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ $t('masters.contact.phone') }}
                                    </h3>
                                    <a
                                        :href="`tel:${props.master.data.phone}`"
                                        class="text-blue-600 transition-colors hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        {{ props.master.data.phone }}
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500"
                                >
                                    <i
                                        class="fa fa-location-dot text-xl text-white"
                                    ></i>
                                </div>
                                <div>
                                    <h3
                                        class="font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ $t('masters.contact.address') }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        {{ props.master.data.address }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500"
                                >
                                    <i
                                        class="fa fa-clock text-xl text-white"
                                    ></i>
                                </div>
                                <div>
                                    <h3
                                        class="font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ $t('masters.contact.availability') }}
                                    </h3>
                                    <p
                                        :class="[
                                            'font-medium',
                                            props.master.data.available
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400',
                                        ]"
                                    >
                                        {{
                                            props.master.data.available
                                                ? $t(
                                                      'masters.contact.available_now',
                                                  )
                                                : $t(
                                                      'masters.contact.not_available',
                                                  )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Map Placeholder -->
                        <div>
                            <MastersMap
                                :masters="[
                                    {
                                        id: props.master.data.id,
                                        name: props.master.data.name,
                                        slug: props.master.data.slug,
                                        latitude: props.master.data.latitude,
                                        longitude: props.master.data.longitude,
                                        address: props.master.data.address,
                                        rating: props.master.data.rating,
                                    },
                                ]"
                                height="300px"
                            />
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import MastersMap from '@/Components/MastersMap.vue';
import { getSEOConfig } from '@/config/seo';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    master: Object,
});

const reviews = ref<any[]>([]);
const newReview = ref({ user_name: '', comment: '', rating: 0 });
const showReviewForm = ref(false);

// SEO Configuration
const seoConfig = getSEOConfig();
const metaTags = computed(() => {
    if (!props.master?.data) return {};

    const masterData = props.master.data;
    const services =
        masterData.services?.map((s: any) => s.name).join(', ') || '';
    const city = masterData.address?.split(',')[0] || '';

    return {
        title: `${masterData.name} - ${services} у ${city}`,
        description: `Персональна сторінка майстра ${masterData.name}. ${masterData.description} Перегляньте спеціалізації, відгуки та контактну інформацію.`,
        keywords: `${masterData.name}, ${services}, майстер, ${city}, автомайстерня, ремонт авто`,
        canonicalUrl: `${window.location.origin}/masters/${masterData.slug}`,
        ogTitle: `${masterData.name} - ${services} у ${city}`,
        ogDescription: `Персональна сторінка майстра ${masterData.name}. ${masterData.description}`,
        ogUrl: `${window.location.origin}/masters/${masterData.slug}`,
        ogImage: `${window.location.origin}/${masterData.main_photo}`,
    };
});

// Structured Data
const structuredData = computed(() => {
    if (!props.master?.data) return '';

    const masterData = props.master.data;
    return JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Person',
        name: masterData.name,
        description: masterData.description,
        image: `${window.location.origin}/${masterData.main_photo}`,
        address: {
            '@type': 'PostalAddress',
            addressLocality: masterData.address?.split(',')[0] || '',
            addressCountry: 'UA',
        },
        telephone: masterData.phone,
        knowsAbout: masterData.services?.map((s: any) => s.name) || [],
        aggregateRating: {
            '@type': 'AggregateRating',
            ratingValue: masterData.rating,
            reviewCount: masterData.reviews_count,
        },
    });
});

// Breadcrumbs
const breadcrumbItems = computed(() => {
    if (!props.master?.data) return [];

    return [
        { name: t('masters.title'), href: route('masters.index') },
        {
            name: props.master.data.name,
            href: route('masters.show', props.master.data.slug),
        },
    ];
});

// Methods
function injectStructuredData() {
    // Remove existing structured data script if any
    const existingScript = document.querySelector(
        'script[data-structured-data]',
    );
    if (existingScript) {
        existingScript.remove();
    }

    // Create new script tag
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.setAttribute('data-structured-data', 'true');
    script.textContent = structuredData.value;

    // Append to head
    document.head.appendChild(script);
}

async function loadReviews() {
    if (!props.master?.data?.id) {
        console.log('Master data not available yet');
        return;
    }

    try {
        const response = await fetch(
            `/api/reviews/master/${props.master.data.id}`,
        );
        if (response.ok) {
            const result = await response.json();
            console.log('Reviews API response:', result);
            if (result.success) {
                reviews.value = result.reviews;
                console.log('Loaded reviews:', reviews.value);

                // Log structure of first review for debugging
                if (result.reviews && result.reviews.length > 0) {
                    console.log('First review structure:', result.reviews[0]);
                    console.log('User object:', result.reviews[0].user);
                }
            }
        }
    } catch (error) {
        console.error('Error loading reviews:', error);
    }
}

async function submitReview() {
    if (newReview.value.rating === 0) {
        alert(t('masters.reviews.select_rating'));
        return;
    }

    if (!newReview.value.user_name?.trim()) {
        alert(t('masters.reviews.enter_name'));
        return;
    }

    if (!newReview.value.comment?.trim()) {
        alert(t('masters.reviews.enter_comment'));
        return;
    }

    const masterId = props.master?.data?.id;
    if (!masterId) {
        alert('Master is not loaded yet');
        return;
    }

    try {
        // Log data being sent
        console.log('Sending review data:', {
            user_name: newReview.value.user_name,
            comment: newReview.value.comment,
            rating: newReview.value.rating,
            master_id: masterId,
        });

        const response = await fetch('/api/reviews/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_name: newReview.value.user_name,
                comment: newReview.value.comment,
                rating: newReview.value.rating,
                master_id: masterId,
            }),
        });

        if (response.ok) {
            const result = await response.json();

            // Add the new review to the list
            if (result.success && result.review) {
                reviews.value.unshift({
                    id: result.review.id,
                    user_name: newReview.value.user_name,
                    rating: newReview.value.rating,
                    review: newReview.value.comment,
                    created_at: new Date().toISOString(),
                });
            }

            // Reset form and close
            newReview.value = { user_name: '', comment: '', rating: 0 };
            showReviewForm.value = false;

            // Show success message
            alert(t('masters.reviews.review_submitted'));
        } else {
            const error = await response.json();
            alert(
                error.message || 'Failed to submit review. Please try again.',
            );
        }
    } catch (error) {
        console.error('Error submitting review:', error);
        alert('Failed to submit review. Please try again.');
    }
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('uk-UA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

onMounted(() => {
    if (props.master?.data) {
        console.log('Master data:', props.master.data);
        injectStructuredData();

        // Load reviews from backend
        loadReviews();
    }
});

// Watch for master data changes to update structured data
watch(
    () => props.master?.data,
    () => {
        injectStructuredData();
    },
    { deep: true },
);
</script>
