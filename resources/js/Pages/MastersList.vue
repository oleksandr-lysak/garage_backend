<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- SEO Meta Tags -->
    <Head>
      <title>{{ metaTags.title }}</title>
      <meta name="description" :content="metaTags.description" />
      <meta name="keywords" :content="metaTags.keywords" />
      <link rel="canonical" :href="metaTags.canonicalUrl" />

      <!-- Open Graph -->
      <meta property="og:title" :content="metaTags.ogTitle" />
      <meta property="og:description" :content="metaTags.ogDescription" />
      <meta property="og:type" content="website" />
      <meta property="og:url" :href="metaTags.ogUrl" />
      <meta property="og:image" :content="metaTags.ogImage" />

      <!-- Twitter Card -->
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" :content="metaTags.ogTitle" />
      <meta name="twitter:description" :content="metaTags.ogDescription" />
      <meta name="twitter:image" :content="metaTags.ogImage" />
    </Head>

    <!-- Breadcrumbs (positioned absolutely, not taking space) -->
    <div class="absolute top-4 left-4 z-10">
      <Breadcrumbs :items="breadcrumbItems" />
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Title and Description -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
          {{ $t('masters.title') }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
          {{ $t('masters.description') }}
        </p>
      </div>

      <!-- Filters and Controls Row -->
      <div class="mb-8">
        <!-- Filters -->
        <div class="w-full">
          <MastersFilters
            v-model="filters"
            :available-services="availableServices"
            :cities="cities"
            @filters-changed="handleFiltersChanged"
          />
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
          <p class="text-gray-600 dark:text-gray-400">{{ $t('common.loading') }}</p>
        </div>
      </div>

      <!-- No Results State -->
      <div v-else-if="masters.data.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
          <i class="fa fa-search text-gray-400 text-6xl mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {{ $t('masters.no_results.title') }}
          </h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ $t('masters.no_results.description') }}
          </p>

          <!-- Popular Searches -->
          <div class="mb-6">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              {{ $t('masters.no_results.suggestions') }}
            </p>
            <div class="flex flex-wrap gap-2 justify-center">
              <button
                v-for="suggestion in popularSearches"
                :key="suggestion"
                @click="applyPopularSearch(suggestion)"
                class="px-3 py-1 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-200"
              >
                {{ suggestion }}
              </button>
            </div>
          </div>

          <button
            @click="clearAllFilters"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
          >
            {{ $t('masters.no_results.clear_filters') }}
          </button>
        </div>
      </div>

      <!-- Masters Grid -->
      <div v-if="masters.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <MasterCard
          v-for="master in masters.data"
          :key="master.id"
          :master-id="master.id"
          :image-url="master.main_photo"
          :name="master.name"
          :address="master.address"
          :rating="master.rating"
          :description="master.description"
          :phone="master.phone"
          :slug="master.slug"
          :age="master.age"
          :available="master.available"
          :experience="5"
          :reviews-count="master.reviews_count"
          :specializations="[]"
          :services="master.services"
        />
      </div>

      <!-- Pagination -->
      <Pagination
        v-if="masters.data.length > 0"
        :current-page="masters.current_page || 1"
        :total-pages="masters.last_page || 1"
        :total-items="masters.total || 0"
        :per-page="perPage"
        @page-changed="handlePageChanged"
        @per-page-changed="handlePerPageChanged"
        class="mt-8"
      />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

// Components
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import MastersFilters from '@/Components/MastersFilters.vue';
import MasterCard from '@/Components/MasterCard.vue';
import Pagination from '@/Components/Pagination.vue';

// SEO Configuration
import { getSEOConfig, generateMetaTags, generateStructuredData, generateBreadcrumbData } from '@/config/seo';

const { t, locale } = useI18n();
const page = usePage();

// SEO Configuration
const seoConfig = getSEOConfig('auto_mechanics'); // Can be changed based on environment
const metaTags = computed(() => generateMetaTags(seoConfig, locale.value));
const breadcrumbItems = computed(() => generateBreadcrumbData(seoConfig, locale.value));

// State
interface Master {
  id: number;
  name: string;
  description: string;
  address: string;
  age: number;
  phone: string;
  reviews_count: number;
  rating: number;
  main_photo: string;
  distance: number;
  main_service_id: number;
  slug: string;
  services: Array<{ id: number; name: string }>;
  available: boolean;
  tariff_id: number;
  tariff: string;
  approved: boolean;
  latitude?: number;
  longitude?: number;
}

const masters = ref<{
  data: Master[];
  current_page: number;
  last_page: number;
  total: number;
  prev_page_url: string | null;
  next_page_url: string | null;
}>({
  data: [],
  current_page: 1,
  last_page: 1,
  total: 0,
  prev_page_url: null,
  next_page_url: null,
});

const isLoading = ref(false);
const perPage = ref(20);

// Filters
const filters = ref({
  search: '',
  service_id: '', // Changed from specialization to service_id
  minRating: '',
  available: '',
  minPrice: null,
  maxPrice: null,
  selectedServices: [],
  city: '',
  distance: null,
  sortBy: 'rating',
});





const availableServices = ref([
  { id: 1, name: 'Технічний огляд' },
  { id: 2, name: 'Заміна масла' },
  { id: 3, name: 'Ремонт гальм' },
  { id: 4, name: 'Ремонт кондиціонера' },
  { id: 5, name: 'Ремонт АКПП' },
]);

const cities = ref(['Київ', 'Львів', 'Харків', 'Одеса', 'Дніпро']);

const popularSearches = ref([
  'Діагностика',
  'Ремонт двигуна',
  'Технічний огляд',
  'Ремонт гальм',
]);

// Computed properties
const startItem = computed(() => (masters.value.current_page - 1) * perPage.value + 1);
const endItem = computed(() => Math.min(masters.value.current_page * perPage.value, masters.value.total));

const structuredData = computed(() =>
  generateStructuredData(seoConfig, masters.value.data, locale.value)
);



// Methods
const fetchMasters = async (url = '/api/masters') => {
  try {
    isLoading.value = true;

    const params = new URLSearchParams();
    params.append('page', masters.value.current_page.toString());
    params.append('per_page', perPage.value.toString());
    params.append('sort_by', filters.value.sortBy);

    // Add filters
    Object.entries(filters.value).forEach(([key, value]) => {
      if (value !== '' && value !== null && value !== undefined) {
        if (Array.isArray(value) && value.length > 0) {
          value.forEach((v: any) => params.append(key, v.toString()));
        } else {
          params.append(key, value.toString());
        }
      }
    });

    const { data } = await axios.get(`${url}?${params.toString()}`);
    masters.value = data.masters;

    // Update URL without page reload
    updateUrl(params.toString());

  } catch (error) {
    console.error('Error fetching masters:', error);
    // Handle error - show notification, etc.
  } finally {
    isLoading.value = false;
  }
};

const updateUrl = (queryString: string) => {
  const newUrl = `/masters?${queryString}`;
  if (window.location.pathname + window.location.search !== newUrl) {
    window.history.pushState({}, '', newUrl);
  }
};

const handleFiltersChanged = (newFilters: any) => {
  filters.value = newFilters;
  masters.value.current_page = 1; // Reset to first page
  fetchMasters();
};



const handlePageChanged = (page: number) => {
  masters.value.current_page = page;
  fetchMasters();
};

const handlePerPageChanged = (newPerPage: number) => {
  perPage.value = newPerPage;
  masters.value.current_page = 1;
  fetchMasters();
};

const clearAllFilters = () => {
  filters.value = {
    search: '',
    service_id: '',
    minRating: '',
    available: '',
    minPrice: null,
    maxPrice: null,
    selectedServices: [],
    city: '',
    distance: null,
    sortBy: 'rating',
  };
  fetchMasters();
};

const applyPopularSearch = (search: string) => {
  filters.value.search = search;
  fetchMasters();
};

// Lifecycle
onMounted(() => {
  fetchMasters();

  // Parse URL parameters on page load
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('page')) {
    masters.value.current_page = parseInt(urlParams.get('page') || '1');
  }
  if (urlParams.has('per_page')) {
    perPage.value = parseInt(urlParams.get('per_page') || '20');
  }

  // Inject structured data script if enabled
  if (seoConfig.seo_settings.enable_structured_data) {
    injectStructuredData();
  }
});

// Inject structured data script into document head
const injectStructuredData = () => {
  // Remove existing structured data script if any
  const existingScript = document.querySelector('script[data-structured-data]');
  if (existingScript) {
    existingScript.remove();
  }

  // Create new script tag
  const script = document.createElement('script');
  script.type = 'application/ld+json';
  script.setAttribute('data-structured-data', 'true');
  script.textContent = JSON.stringify(structuredData.value);

  // Append to document head
  document.head.appendChild(script);
};

// Watch for route changes
watch(() => page.url, () => {
  // Handle route changes if needed
}, { immediate: true });

// Watch for masters data changes to update structured data
watch(() => masters.value.data, () => {
  if (seoConfig.seo_settings.enable_structured_data) {
    injectStructuredData();
  }
}, { deep: true });
</script>
