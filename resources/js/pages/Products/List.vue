<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import ProductCard from '@/components/site/ProductCard.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { BreadcrumbItem } from '@/types';
import { FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { debounce } from 'lodash';
import { computed, ref, watch } from 'vue';

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string;
    price: number;
    sale_price: number | null;
    formatted_price: string;
    formatted_sale_price: string | null;
    default_image: string;
    images: Array<{
        id: number;
        path: string;
        is_default: boolean;
        order: number;
    }>;
    stock: number;
    average_rating: number;
    reviews_count: number;
    isOnSale: boolean;
    discount_percentage: number;
    category_id: number;
    created_at: string;
    views: number;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    products_count: number;
}

interface Advertisement {
    id: number;
    title: string;
    description: string;
    image: string;
}

interface Props {
    products: Product[];
    categories: Category[];
    featuredProducts: Product[];
    advertisements: Advertisement[];
    filters: {
        search?: string;
        category?: number[];
        min_price?: string;
        max_price?: string;
        in_stock?: boolean;
        min_rating?: string;
        sort?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Catalogue',
        href: '/products',
    },
];

// État local
const search = ref(props.filters?.search || '');
const filters = ref({
    category: props.filters?.category || [],
    min_price: props.filters?.min_price || '',
    max_price: props.filters?.max_price || '',
    in_stock: props.filters?.in_stock || false,
    min_rating: props.filters?.min_rating || '',
    sort: props.filters?.sort || 'newest',
});

// État pour le menu mobile des filtres
const isFilterMenuOpen = ref(false);
const toggleFilterMenu = () => {
    isFilterMenuOpen.value = !isFilterMenuOpen.value;
};

// Filtrage côté client
const filteredProducts = computed(() => {
    if (!props.products || !Array.isArray(props.products)) {
        console.warn('Les données des produits ne sont pas dans le format attendu');
        return [];
    }

    let result = [...props.products];

    // Filtre par recherche
    if (search.value) {
        const searchLower = search.value.toLowerCase();
        result = result.filter(
            (product) => product.name?.toLowerCase().includes(searchLower) || product.description?.toLowerCase().includes(searchLower),
        );
    }

    // Filtre par catégorie
    if (filters.value.category.length) {
        result = result.filter((product) => filters.value.category.includes(product.category_id));
    }

    // Filtre par prix
    if (filters.value.min_price) {
        result = result.filter((product) => product.price >= parseFloat(filters.value.min_price));
    }
    if (filters.value.max_price) {
        result = result.filter((product) => product.price <= parseFloat(filters.value.max_price));
    }

    // Filtre par disponibilité
    if (filters.value.in_stock) {
        result = result.filter((product) => product.stock > 0);
    }

    // Filtre par note
    if (filters.value.min_rating) {
        result = result.filter((product) => product.rating >= parseFloat(filters.value.min_rating));
    }

    // Tri
    switch (filters.value.sort) {
        case 'price_asc':
            result.sort((a, b) => a.price - b.price);
            break;
        case 'price_desc':
            result.sort((a, b) => b.price - a.price);
            break;
        case 'name_asc':
            result.sort((a, b) => a.name.localeCompare(b.name));
            break;
        case 'name_desc':
            result.sort((a, b) => b.name.localeCompare(a.name));
            break;
        case 'rating':
            result.sort((a, b) => b.rating - a.rating);
            break;
        case 'popular':
            result.sort((a, b) => b.views - a.views);
            break;
        default: // newest
            result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
    }

    return result;
});

// Pagination côté client
const currentPage = ref(1);
const itemsPerPage = 12;
const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredProducts.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage));

// Gestion des filtres
const applyFilters = () => {
    currentPage.value = 1; // Réinitialiser la pagination lors du changement de filtre
};

// Debounce pour la recherche
const debouncedSearch = debounce(() => {
    currentPage.value = 1;
}, 300);

watch(search, debouncedSearch);
watch(filters, applyFilters, { deep: true });

// Compteur de filtres actifs
const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.category.length) count++;
    if (filters.value.min_price || filters.value.max_price) count++;
    if (filters.value.in_stock) count++;
    if (filters.value.min_rating) count++;
    if (filters.value.sort !== 'newest') count++;
    return count;
});

// Réinitialiser les filtres
const resetFilters = () => {
    filters.value = {
        category: [],
        min_price: '',
        max_price: '',
        in_stock: false,
        min_rating: '',
        sort: 'newest',
    };
    search.value = '';
    currentPage.value = 1;
};
</script>

<template>
    <SiteLayout title="Nos Produits">
        <!-- Bannière de page -->
        <PageBanner title="Nos Produits" description="Découvrez notre sélection de produits de qualité" :breadcrumbs="breadcrumbs" />

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Barre de recherche et filtres -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher un produit..."
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2 pl-10 text-sm transition-all duration-200 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    />
                    <svg class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Bouton des filtres pour mobile -->
                <button
                    @click="toggleFilterMenu"
                    class="flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition-all hover:bg-gray-50 sm:hidden dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-700"
                >
                    <FunnelIcon class="h-5 w-5" />
                    Filtres
                    <span
                        v-if="activeFiltersCount > 0"
                        class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-xs font-medium text-white"
                    >
                        {{ activeFiltersCount }}
                    </span>
                </button>

                <!-- Tri pour desktop -->
                <select
                    v-model="filters.sort"
                    class="hidden rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 sm:block dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300"
                >
                    <option value="newest">Plus récents</option>
                    <option value="price_asc">Prix croissant</option>
                    <option value="price_desc">Prix décroissant</option>
                    <option value="name_asc">Nom A-Z</option>
                    <option value="name_desc">Nom Z-A</option>
                    <option value="rating">Meilleures notes</option>
                    <option value="popular">Plus populaires</option>
                </select>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Filtres (desktop) -->
                <div class="hidden w-full lg:block lg:w-1/4">
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Filtres</h2>
                            <button
                                v-if="activeFiltersCount > 0"
                                @click="resetFilters"
                                class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300"
                            >
                                Réinitialiser
                            </button>
                        </div>

                        <!-- Catégories -->
                        <div class="mb-6">
                            <h3 class="mb-2 font-medium text-gray-900 dark:text-white">Catégories</h3>
                            <div class="space-y-2">
                                <label v-for="category in categories" :key="category.id" class="flex items-center">
                                    <input
                                        type="checkbox"
                                        :value="category.id"
                                        v-model="filters.category"
                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ category.name }} ({{ category.products_count }})
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="mb-6">
                            <h3 class="mb-2 font-medium text-gray-900 dark:text-white">Prix</h3>
                            <div class="flex gap-2">
                                <input
                                    v-model="filters.min_price"
                                    type="number"
                                    placeholder="Min"
                                    class="w-1/2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                />
                                <input
                                    v-model="filters.max_price"
                                    type="number"
                                    placeholder="Max"
                                    class="w-1/2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                />
                            </div>
                        </div>

                        <!-- Disponibilité -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="filters.in_stock"
                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">En stock uniquement</span>
                            </label>
                        </div>

                        <!-- Note minimale -->
                        <div class="mb-6">
                            <h3 class="mb-2 font-medium text-gray-900 dark:text-white">Note minimale</h3>
                            <select
                                v-model="filters.min_rating"
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="">Toutes les notes</option>
                                <option value="4">4 étoiles et plus</option>
                                <option value="3">3 étoiles et plus</option>
                                <option value="2">2 étoiles et plus</option>
                            </select>
                        </div>
                    </div>

                    <!-- Publicités -->
                    <div class="mt-6 space-y-4">
                        <div
                            v-for="ad in advertisements"
                            :key="ad.id"
                            class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md dark:bg-gray-800 dark:ring-gray-700"
                        >
                            <a :href="route('advertisements.click', ad.id)" target="_blank" class="block">
                                <img :src="ad.image" :alt="ad.title" class="h-auto w-full" />
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ ad.title }}</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ ad.description }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Liste des produits -->
                <div class="w-full lg:w-3/4">
                    <!-- Produits en vedette -->
                    <div v-if="featuredProducts.length" class="mb-8">
                        <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Produits en vedette</h2>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <ProductCard v-for="(product, index) in featuredProducts" :key="product.id" :product="product" :index="index" />
                        </div>
                    </div>

                    <!-- Tous les produits -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <ProductCard v-for="(product, index) in paginatedProducts" :key="product.id" :product="product" :index="index" />
                    </div>

                    <!-- Pagination -->
                    <div v-if="totalPages > 1" class="mt-8 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Précédent
                            </button>
                            <span class="text-sm text-gray-700 dark:text-gray-300"> Page {{ currentPage }} sur {{ totalPages }} </span>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage === totalPages"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Suivant
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu des filtres mobile -->
        <div v-if="isFilterMenuOpen" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm lg:hidden" @click="toggleFilterMenu">
            <div class="min-h-screen px-4 text-center">
                <div class="fixed inset-y-0 right-0 w-full max-w-xs overflow-y-auto bg-white p-6 shadow-xl dark:bg-gray-800" @click.stop>
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Filtres</h2>
                        <button
                            @click="toggleFilterMenu"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-700"
                        >
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <!-- Contenu des filtres mobile -->
                    <div class="mt-6 space-y-6">
                        <!-- Catégories -->
                        <div>
                            <h3 class="mb-2 font-medium text-gray-900 dark:text-white">Catégories</h3>
                            <div class="space-y-2">
                                <label v-for="category in categories" :key="category.id" class="flex items-center">
                                    <input
                                        type="checkbox"
                                        :value="category.id"
                                        v-model="filters.category"
                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ category.name }} ({{ category.products_count }})
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div>
                            <h3 class="mb-2 font-medium text-gray-900 dark:text-white">Prix</h3>
                            <div class="flex gap-2">
                                <input
                                    v-model="filters.min_price"
                                    type="number"
                                    placeholder="Min"
                                    class="w-1/2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                />
                                <input
                                    v-model="filters.max_price"
                                    type="number"
                                    placeholder="Max"
                                    class="w-1/2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                />
                            </div>
                        </div>

                        <!-- Disponibilité -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="filters.in_stock"
                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">En stock uniquement</span>
                            </label>
                        </div>

                        <!-- Note minimale -->
                        <div>
                            <h3 class="mb-2 font-medium text-gray-900 dark:text-white">Note minimale</h3>
                            <select
                                v-model="filters.min_rating"
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="">Toutes les notes</option>
                                <option value="4">4 étoiles et plus</option>
                                <option value="3">3 étoiles et plus</option>
                                <option value="2">2 étoiles et plus</option>
                            </select>
                        </div>

                        <!-- Tri -->
                        <div>
                            <h3 class="mb-2 font-medium text-gray-900 dark:text-white">Trier par</h3>
                            <select
                                v-model="filters.sort"
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-1 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="newest">Plus récents</option>
                                <option value="price_asc">Prix croissant</option>
                                <option value="price_desc">Prix décroissant</option>
                                <option value="name_asc">Nom A-Z</option>
                                <option value="name_desc">Nom Z-A</option>
                                <option value="rating">Meilleures notes</option>
                                <option value="popular">Plus populaires</option>
                            </select>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex gap-4">
                            <button
                                @click="resetFilters"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Réinitialiser
                            </button>
                            <button
                                @click="toggleFilterMenu"
                                class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                            >
                                Appliquer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>

<style scoped>
/* Animations de base */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Classes d'animation */
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}

.animate-fade-in-right {
    animation: fadeInRight 0.8s ease-out forwards;
}

/* Transitions fluides */
.section-transition {
    transition: all 0.3s ease-in-out;
}

/* Effets de survol */
.hover-scale {
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.02);
}

/* Ombres dynamiques */
.shadow-hover {
    transition: box-shadow 0.3s ease;
}

.shadow-hover:hover {
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
}
</style>
