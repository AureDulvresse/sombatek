<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import ProductCard from '@/components/site/ProductCard.vue';
import { Button } from '@/components/ui/button';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { BreadcrumbItem } from '@/types';
import {
    FunnelIcon,
    XMarkIcon,
    MagnifyingGlassIcon,
    Squares2X2Icon,
    ListBulletIcon
} from '@heroicons/vue/24/outline';
import {
    FunnelIcon as FunnelIconSolid,
} from '@heroicons/vue/24/solid';
import { debounce } from 'lodash';
import { computed, ref, watch, onMounted, nextTick } from 'vue';

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

// États locaux
const search = ref(props.filters?.search || '');
const isFilterMenuOpen = ref(false);
const viewMode = ref<'grid' | 'list'>('grid');
const currentPage = ref(1);
const itemsPerPage = ref(12);

// Filtres avec validation
const filters = ref({
    category: props.filters?.category || [],
    min_price: props.filters?.min_price || '',
    max_price: props.filters?.max_price || '',
    in_stock: props.filters?.in_stock || false,
    min_rating: props.filters?.min_rating || '',
    sort: props.filters?.sort || 'newest',
});

// Options de tri structurées
const sortOptions = [
    { value: 'newest', label: 'Plus récents', icon: '🆕' },
    { value: 'price_asc', label: 'Prix croissant', icon: '💰' },
    { value: 'price_desc', label: 'Prix décroissant', icon: '💎' },
    { value: 'name_asc', label: 'Nom A-Z', icon: '🔤' },
    { value: 'name_desc', label: 'Nom Z-A', icon: '🔠' },
    { value: 'rating', label: 'Meilleures notes', icon: '⭐' },
    { value: 'popular', label: 'Plus populaires', icon: '🔥' },
];

const ratingOptions = [
    { value: '', label: 'Toutes les notes' },
    { value: '4', label: '4+ étoiles' },
    { value: '3', label: '3+ étoiles' },
    { value: '2', label: '2+ étoiles' },
];

// Gestion du menu mobile
const toggleFilterMenu = () => {
    isFilterMenuOpen.value = !isFilterMenuOpen.value;
    if (isFilterMenuOpen.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
};

// Filtrage optimisé côté client
const filteredProducts = computed(() => {
    if (!props.products?.length) {
        console.warn('Aucun produit disponible');
        return [];
    }

    let result = [...props.products];

    // Filtre par recherche (optimisé)
    if (search.value.trim()) {
        const searchTerms = search.value.toLowerCase().trim().split(' ');
        result = result.filter(product => {
            const searchableText = `${product.name} ${product.description}`.toLowerCase();
            return searchTerms.every((term: string) => searchableText.includes(term));
        });
    }

    // Filtre par catégorie
    if (filters.value.category.length) {
        result = result.filter(product =>
            filters.value.category.includes(product.category_id)
        );
    }

    // Filtre par prix avec validation
    const minPrice = parseFloat(filters.value.min_price) || 0;
    const maxPrice = parseFloat(filters.value.max_price) || Infinity;

    if (filters.value.min_price || filters.value.max_price) {
        result = result.filter(product => {
            const price = product.sale_price || product.price;
            return price >= minPrice && price <= maxPrice;
        });
    }

    // Filtre par disponibilité
    if (filters.value.in_stock) {
        result = result.filter(product => product.stock > 0);
    }

    // Filtre par note
    if (filters.value.min_rating) {
        const minRating = parseFloat(filters.value.min_rating);
        result = result.filter(product => product.average_rating >= minRating);
    }

    // Tri optimisé
    const sortFunctions = {
        price_asc: (a: Product, b: Product) => (a.sale_price || a.price) - (b.sale_price || b.price),
        price_desc: (a: Product, b: Product) => (b.sale_price || b.price) - (a.sale_price || a.price),
        name_asc: (a: Product, b: Product) => a.name.localeCompare(b.name, 'fr'),
        name_desc: (a: Product, b: Product) => b.name.localeCompare(a.name, 'fr'),
        rating: (a: Product, b: Product) => b.average_rating - a.average_rating,
        popular: (a: Product, b: Product) => b.views - a.views,
        newest: (a: Product, b: Product) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
    };

    const sortFunction = sortFunctions[filters.value.sort as keyof typeof sortFunctions];
    if (sortFunction) {
        result.sort(sortFunction);
    }

    return result;
});

// Pagination améliorée
const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredProducts.value.slice(start, start + itemsPerPage.value);
});

const totalPages = computed(() =>
    Math.ceil(filteredProducts.value.length / itemsPerPage.value)
);

const paginationInfo = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value + 1;
    const end = Math.min(start + itemsPerPage.value - 1, filteredProducts.value.length);
    return {
        start,
        end,
        total: filteredProducts.value.length
    };
});

// Gestion des filtres avec historique URL
const applyFilters = debounce(() => {
    currentPage.value = 1;

    // Mise à jour de l'URL sans rechargement
    const params = new URLSearchParams();

    if (search.value) params.set('search', search.value);
    if (filters.value.category.length) params.set('category', filters.value.category.join(','));
    if (filters.value.min_price) params.set('min_price', filters.value.min_price);
    if (filters.value.max_price) params.set('max_price', filters.value.max_price);
    if (filters.value.in_stock) params.set('in_stock', '1');
    if (filters.value.min_rating) params.set('min_rating', filters.value.min_rating);
    if (filters.value.sort !== 'newest') params.set('sort', filters.value.sort);

    const queryString = params.toString();
    const newUrl = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;

    window.history.replaceState({}, '', newUrl);
}, 300);

// Debounce pour la recherche
const debouncedSearch = debounce(() => {
    currentPage.value = 1;
    applyFilters();
}, 500);

// Watchers
watch(search, debouncedSearch);
watch(filters, applyFilters, { deep: true });

// Compteur de filtres actifs optimisé
const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.category.length) count++;
    if (filters.value.min_price || filters.value.max_price) count++;
    if (filters.value.in_stock) count++;
    if (filters.value.min_rating) count++;
    if (filters.value.sort !== 'newest') count++;
    if (search.value.trim()) count++;
    return count;
});

// Réinitialisation des filtres
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

    // Nettoyer l'URL
    window.history.replaceState({}, '', window.location.pathname);
};

// Navigation pagination
const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        // Scroll vers le haut de la liste
        nextTick(() => {
            document.querySelector('.products-container')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    }
};

// Gestion du changement de vue
const toggleViewMode = () => {
    viewMode.value = viewMode.value === 'grid' ? 'list' : 'grid';
};

// Nettoyage à la destruction
onMounted(() => {
    return () => {
        document.body.style.overflow = '';
    };
});

// Méthode pour obtenir les pages de pagination
const getPaginationPages = computed(() => {
    const pages = [];
    const maxVisible = 5;
    const current = currentPage.value;
    const total = totalPages.value;

    if (total <= maxVisible) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 3) {
            pages.push(1, 2, 3, 4, '...', total);
        } else if (current >= total - 2) {
            pages.push(1, '...', total - 3, total - 2, total - 1, total);
        } else {
            pages.push(1, '...', current - 1, current, current + 1, '...', total);
        }
    }

    return pages;
});
</script>

<template>
    <SiteLayout title="Nos Produits">
        <!-- Bannière améliorée -->
        <PageBanner title="Nos Produits" description="Découvrez notre sélection de produits de qualité premium"
            :breadcrumbs="breadcrumbs" />

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header avec statistiques -->
            <div
                class="mb-8 rounded-2xl bg-gradient-to-r from-emerald-50 to-blue-50 p-6 dark:from-gray-800 dark:to-gray-700">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Catalogue Produits
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ paginationInfo.total }} produit{{ paginationInfo.total > 1 ? 's' : '' }}
                            {{ activeFiltersCount > 0 ? 'correspondant à vos critères' : 'disponible' +
                                (paginationInfo.total > 1 ? 's' : '') }}
                        </p>
                    </div>

                    <!-- Actions rapides -->
                    <div class="flex items-center gap-3">
                        <Button @click="toggleViewMode" variant="outline" size="sm" class="hidden sm:flex">
                            <component :is="viewMode === 'grid' ? ListBulletIcon : Squares2X2Icon" class="h-4 w-4" />
                        </Button>

                        <select v-model="itemsPerPage"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            <option :value="12">12 par page</option>
                            <option :value="24">24 par page</option>
                            <option :value="48">48 par page</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Barre de recherche et filtres modernisée -->
            <div class="mb-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Recherche améliorée -->
                    <div class="relative flex-1 lg:max-w-md">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                        </div>
                        <input v-model="search" type="text" placeholder="Rechercher un produit..."
                            class="block w-full rounded-xl border-0 bg-white py-3 pl-10 pr-12 text-sm shadow-sm ring-1 ring-gray-300 transition-all duration-200 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-600 dark:bg-gray-800 dark:ring-gray-700 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:ring-emerald-500" />
                        <div v-if="search" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button @click="search = ''"
                                class="rounded-full p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <XMarkIcon class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Bouton filtres mobile -->
                        <Button @click="toggleFilterMenu" variant="outline" class="flex items-center gap-2 lg:hidden">
                            <FunnelIcon class="h-5 w-5" />
                            Filtres
                            <span v-if="activeFiltersCount > 0"
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-xs font-medium text-white">
                                {{ activeFiltersCount }}
                            </span>
                        </Button>

                        <!-- Tri desktop -->
                        <div class="hidden lg:flex lg:items-center lg:gap-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Trier par:
                            </label>
                            <select v-model="filters.sort"
                                class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                                    {{ option.icon }} {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Filtres actifs -->
                <div v-if="activeFiltersCount > 0" class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Filtres actifs:
                    </span>

                    <!-- Tags des filtres -->
                    <div class="flex flex-wrap gap-2">
                        <span v-if="search.trim()"
                            class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                            "{{ search }}"
                            <button @click="search = ''" class="hover:text-emerald-600">
                                <XMarkIcon class="h-3 w-3" />
                            </button>
                        </span>

                        <span v-if="filters.category.length"
                            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ filters.category.length }} catégorie{{ filters.category.length > 1 ? 's' : '' }}
                            <button @click="filters.category = []" class="hover:text-blue-600">
                                <XMarkIcon class="h-3 w-3" />
                            </button>
                        </span>

                        <span v-if="filters.min_price || filters.max_price"
                            class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                            Prix: {{ filters.min_price || '0' }}€ - {{ filters.max_price || '∞' }}€
                            <button @click="filters.min_price = ''; filters.max_price = ''"
                                class="hover:text-purple-600">
                                <XMarkIcon class="h-3 w-3" />
                            </button>
                        </span>
                    </div>

                    <Button @click="resetFilters" variant="ghost" size="sm" class="text-xs">
                        Tout effacer
                    </Button>
                </div>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Sidebar filtres desktop -->
                <aside class="hidden w-full lg:block lg:w-80">
                    <div class="sticky top-4 space-y-6">
                        <!-- Panneau filtres principal -->
                        <div
                            class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div
                                class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-750">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <FunnelIconSolid class="h-5 w-5 text-emerald-600" />
                                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filtres</h2>
                                    </div>
                                    <Button v-if="activeFiltersCount > 0" @click="resetFilters" variant="ghost"
                                        size="sm">
                                        Réinitialiser
                                    </Button>
                                </div>
                            </div>

                            <div class="space-y-6 p-6">
                                <!-- Catégories avec compteurs -->
                                <div>
                                    <h3 class="mb-3 flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        Catégories
                                    </h3>
                                    <div class="space-y-3">
                                        <label v-for="category in categories" :key="category.id"
                                            class="group flex cursor-pointer items-center justify-between rounded-lg p-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <div class="flex items-center">
                                                <input type="checkbox" :value="category.id" v-model="filters.category"
                                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600" />
                                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                                    {{ category.name }}
                                                </span>
                                            </div>
                                            <span
                                                class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                {{ category.products_count }}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Prix avec slider visuel -->
                                <div>
                                    <h3 class="mb-3 flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                        Fourchette de prix
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex gap-3">
                                            <div class="flex-1">
                                                <label
                                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Prix minimum
                                                </label>
                                                <input v-model="filters.min_price" type="number" placeholder="0" min="0"
                                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                            </div>
                                            <div class="flex-1">
                                                <label
                                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Prix maximum
                                                </label>
                                                <input v-model="filters.max_price" type="number" placeholder="∞" min="0"
                                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Options avancées -->
                                <div class="space-y-4">
                                    <h3 class="flex items-center gap-2 font-medium text-gray-900 dark:text-white">
                                        <span class="h-2 w-2 rounded-full bg-purple-500"></span>
                                        Options avancées
                                    </h3>

                                    <!-- Disponibilité -->
                                    <label
                                        class="flex cursor-pointer items-center justify-between rounded-lg p-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">En stock
                                            uniquement</span>
                                        <input type="checkbox" v-model="filters.in_stock"
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600" />
                                    </label>

                                    <!-- Note minimale -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Note minimale
                                        </label>
                                        <select v-model="filters.min_rating"
                                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            <option v-for="option in ratingOptions" :key="option.value"
                                                :value="option.value">
                                                {{ option.label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Publicités optimisées -->
                        <div class="space-y-4">
                            <div v-for="ad in advertisements" :key="ad.id"
                                class="group overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md dark:bg-gray-800 dark:ring-gray-700">
                                <a :href="route('advertisements.click', ad.id)" target="_blank" class="block">
                                    <div class="aspect-[4/3] overflow-hidden">
                                        <img :src="ad.image" :alt="ad.title"
                                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                                    </div>
                                    <div class="p-4">
                                        <h3
                                            class="font-medium text-gray-900 group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
                                            {{ ad.title }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ ad.description }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Liste des produits -->
                <main class="flex-1">
                    <div class="products-container">
                        <!-- Informations pagination -->
                        <div class="mb-6 flex items-center justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Affichage de {{ paginationInfo.start }}-{{ paginationInfo.end }}
                                sur {{ paginationInfo.total }} produits
                            </p>

                            <div class="hidden sm:flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Vue:</span>
                                <div class="flex rounded-lg border border-gray-300 dark:border-gray-600">
                                    <button @click="viewMode = 'grid'" :class="[
                                        'p-2 text-sm',
                                        viewMode === 'grid'
                                            ? 'bg-emerald-500 text-white'
                                            : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                    ]">
                                        <Squares2X2Icon class="h-4 w-4" />
                                    </button>
                                    <button @click="viewMode = 'list'" :class="[
                                        'p-2 text-sm border-l border-gray-300 dark:border-gray-600',
                                        viewMode === 'list'
                                            ? 'bg-emerald-500 text-white'
                                            : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                    ]">
                                        <ListBulletIcon class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Grille des produits -->
                        <div v-if="paginatedProducts.length === 0" class="py-12 text-center">
                            <div class="mx-auto max-w-md">
                                <div class="mb-4 text-6xl">🔍</div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                                    Aucun produit trouvé
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Essayez d'ajuster vos filtres ou votre recherche
                                </p>
                                <Button @click="resetFilters" class="mt-4">
                                    Réinitialiser les filtres
                                </Button>
                            </div>
                        </div>

                        <div v-else :class="[
                            'grid gap-6',
                            viewMode === 'grid'
                                ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
                                : 'grid-cols-1'
                        ]">
                            <ProductCard v-for="product in paginatedProducts" :key="product.id" :product="product"
                                :view-mode="viewMode" />
                        </div>

                        <!-- Pagination -->
                        <div v-if="totalPages > 1" class="mt-12 flex items-center justify-center">
                            <nav class="flex items-center gap-2">
                                <!-- Bouton précédent -->
                                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                    class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                    Précédent
                                </button>

                                <!-- Pages -->
                                <div class="flex items-center gap-1">
                                    <button v-for="page in getPaginationPages" :key="page"
                                        @click="typeof page === 'number' ? goToPage(page) : null"
                                        :disabled="typeof page !== 'number'" :class="[
                                            'flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium transition-colors',
                                            typeof page === 'number'
                                                ? page === currentPage
                                                    ? 'bg-emerald-500 text-white'
                                                    : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                                : 'text-gray-400'
                                        ]">
                                        {{ page }}
                                    </button>
                                </div>

                                <!-- Bouton suivant -->
                                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                    class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                    Suivant
                                </button>
                            </nav>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Menu mobile des filtres -->
        <div v-if="isFilterMenuOpen" class="fixed inset-0 z-50 lg:hidden" @click="toggleFilterMenu">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
            <div class="absolute right-0 top-0 h-full w-80 max-w-[80vw] bg-white p-6 shadow-xl dark:bg-gray-800"
                @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filtres</h2>
                    <button @click="toggleFilterMenu"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Contenu du menu mobile -->
                <div class="space-y-6">
                    <!-- Catégories -->
                    <div>
                        <h3 class="mb-3 font-medium text-gray-900 dark:text-white">Catégories</h3>
                        <div class="space-y-3">
                            <label v-for="category in categories" :key="category.id"
                                class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" :value="category.id" v-model="filters.category"
                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ category.name }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">{{ category.products_count }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Prix -->
                    <div>
                        <h3 class="mb-3 font-medium text-gray-900 dark:text-white">Prix</h3>
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Min
                                    </label>
                                    <input v-model="filters.min_price" type="number" placeholder="0"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Max
                                    </label>
                                    <input v-model="filters.max_price" type="number" placeholder="∞"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-gray-900 dark:text-white">Options</h3>

                        <label class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">En stock uniquement</span>
                            <input type="checkbox" v-model="filters.in_stock"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                        </label>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Note minimale
                            </label>
                            <select v-model="filters.min_rating"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <option v-for="option in ratingOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-6">
                        <Button @click="resetFilters" variant="outline" class="flex-1">
                            Réinitialiser
                        </Button>
                        <Button @click="toggleFilterMenu" class="flex-1">
                            Appliquer
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>