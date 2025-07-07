<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">
        <!-- En-tête de la boutique -->
        <div class="bg-white/90 dark:bg-gray-800/90 shadow-sm rounded-b-3xl">
            <div class="mx-auto max-w-5xl px-4 py-10 flex flex-col md:flex-row md:items-center gap-8">
                <div class="flex-shrink-0">
                    <img :src="shop.logo || '/images/shop-placeholder.jpg'" :alt="shop.name"
                        class="h-32 w-32 rounded-2xl object-cover border-4 border-emerald-100 shadow-md" />
                </div>
                <div class="flex-1">
                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <h1 class="text-3xl font-bold flex items-center text-gray-900 dark:text-white">
                            {{ shop.name }}
                            <span v-if="shop.is_verified"
                                class="ml-2 px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-full">Vérifiée</span>
                        </h1>
                        <div class="flex items-center gap-2">
                            <button
                                class="rounded-lg border p-2 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                                <HeartIcon class="h-5 w-5" :class="{ 'fill-current text-red-500': isWishlisted }" />
                            </button>
                            <a :href="`mailto:${shop.email}`"
                                class="inline-block px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">Contacter</a>
                        </div>
                    </div>
                    <div class="mb-4 flex flex-wrap items-center gap-4 text-gray-600 dark:text-gray-300">
                        <div class="flex items-center">
                            <StarIcon class="h-5 w-5 text-yellow-400" />
                            <span class="ml-1 font-semibold text-gray-900 dark:text-white">{{ shop.rating }}</span>
                            <span class="ml-1">({{ shop.reviews_count }} avis)</span>
                        </div>
                        <span>{{ shop.products_count }} produits</span>
                        <span>Membre depuis {{ formatDate(shop.created_at) }}</span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 max-w-2xl">{{ shop.description }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation de la boutique -->
        <div class="border-b bg-white/80 dark:bg-gray-800/80 sticky top-0 z-10 shadow-sm">
            <div class="mx-auto max-w-5xl px-4">
                <nav class="flex space-x-8">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                        'border-b-2 py-4 text-sm font-medium transition',
                        activeTab === tab.id
                            ? 'border-emerald-600 text-emerald-700 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200',
                    ]">
                        {{ tab.name }}
                    </button>
                </nav>
            </div>
        </div>

        <div class="mx-auto max-w-5xl px-4 py-10">
            <!-- Onglet Produits -->
            <div v-if="activeTab === 'products'" class="space-y-8">
                <!-- Filtres -->
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <select v-model="sortBy"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100">
                            <option value="newest">Plus récents</option>
                            <option value="price_asc">Prix croissant</option>
                            <option value="price_desc">Prix décroissant</option>
                            <option value="rating">Meilleures notes</option>
                        </select>
                        <select v-model="categoryFilter"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100">
                            <option value="">Toutes les catégories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="viewMode = 'grid'"
                            :class="['p-2 rounded-lg', viewMode === 'grid' ? 'bg-emerald-100 text-emerald-700' : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700']">
                            <Squares2X2Icon class="h-5 w-5" />
                        </button>
                        <button @click="viewMode = 'list'"
                            :class="['p-2 rounded-lg', viewMode === 'list' ? 'bg-emerald-100 text-emerald-700' : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700']">
                            <ListBulletIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <!-- Grille des produits -->
                <div
                    :class="[viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-6']">
                    <ProductCard v-for="(product, idx) in filteredProducts" :key="product.id" :product="product"
                        :index="idx" />
                </div>
            </div>

            <!-- Onglet À propos -->
            <div v-if="activeTab === 'about'" class="space-y-8">
                <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold text-emerald-700 dark:text-emerald-400">À propos de la boutique
                    </h2>
                    <p class="text-gray-700 dark:text-gray-200">{{ shop.description }}</p>
                </div>

                <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold text-emerald-700 dark:text-emerald-400">Informations</h2>
                    <div class="space-y-4 text-gray-700 dark:text-gray-200">
                        <div class="flex items-center">
                            <MapPinIcon class="h-5 w-5 text-gray-400" />
                            <span class="ml-2">{{ shop.address }}</span>
                        </div>
                        <div class="flex items-center">
                            <PhoneIcon class="h-5 w-5 text-gray-400" />
                            <span class="ml-2">{{ shop.phone }}</span>
                        </div>
                        <div class="flex items-center">
                            <EnvelopeIcon class="h-5 w-5 text-gray-400" />
                            <span class="ml-2">{{ shop.email }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold text-emerald-700 dark:text-emerald-400">Horaires d'ouverture</h2>
                    <div class="space-y-2">
                        <div v-for="(hours, day) in shop.openingHours" :key="day" class="flex justify-between">
                            <span class="font-medium">{{ day }}</span>
                            <span>{{ hours }}</span>
                        </div>
                    </div>
                </div>

                <div v-if="shop.social_media && shop.social_media.length" class="flex gap-2 mt-2">
                    <a v-for="link in shop.social_media" :key="link.url" :href="link.url" target="_blank"
                        class="text-blue-500 hover:underline">
                        {{ link.platform }}
                    </a>
                </div>
            </div>

            <!-- Onglet Avis -->
            <div v-if="activeTab === 'reviews'" class="space-y-8">
                <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 p-6 shadow-sm">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-emerald-700 dark:text-emerald-400">Avis clients</h2>
                            <div class="mt-1 flex items-center">
                                <StarIcon class="h-5 w-5 text-yellow-400" />
                                <span class="ml-1 font-semibold text-gray-900 dark:text-white">{{ shop.rating }}</span>
                                <span class="ml-1 text-gray-600">({{ shop.reviews_count }} avis)</span>
                            </div>
                        </div>
                        <button @click="showReviewForm = true"
                            class="hover:bg-emerald-700 rounded-lg bg-emerald-600 px-4 py-2 text-white transition">
                            Écrire un avis
                        </button>
                    </div>

                    <!-- Formulaire d'avis -->
                    <div v-if="showReviewForm" class="mb-6 rounded-lg border p-4 bg-white dark:bg-gray-900">
                        <h3 class="mb-4 text-lg font-medium">Votre avis</h3>
                        <form @submit.prevent="submitReview" class="space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium">Note</label>
                                <div class="flex items-center gap-2">
                                    <button v-for="rating in 5" :key="rating" @click="newReview.rating = rating"
                                        :class="['text-2xl', rating <= newReview.rating ? 'text-yellow-400' : 'text-gray-300']">
                                        ★
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Commentaire</label>
                                <textarea v-model="newReview.comment" rows="4"
                                    class="w-full rounded-lg border p-2"></textarea>
                            </div>
                            <div class="flex justify-end gap-4">
                                <button type="button" @click="showReviewForm = false"
                                    class="rounded-lg border px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="hover:bg-emerald-700 rounded-lg bg-emerald-600 px-4 py-2 text-white transition">Publier</button>
                            </div>
                        </form>
                    </div>

                    <!-- Liste des avis -->
                    <div class="space-y-6">
                        <div v-for="review in reviews" :key="review.id" class="border-b pb-6 last:border-0">
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <img :src="review.user.avatar" :alt="review.user.name"
                                        class="mr-4 h-10 w-10 rounded-full object-cover" />
                                    <div>
                                        <h4 class="font-medium">{{ review.user.name }}</h4>
                                        <div class="flex items-center">
                                            <StarIcon class="h-4 w-4 text-yellow-400" />
                                            <span class="ml-1 text-sm">{{ review.rating }}</span>
                                            <span class="ml-2 text-sm text-gray-500">
                                                {{ formatDate(review.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">{{ review.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut visible pour admin/proprio -->
            <div v-if="userIsOwnerOrAdmin" class="mt-6">
                <span class="text-xs px-2 py-1 rounded-full" :class="{
                    'bg-emerald-100 text-emerald-700': shop.status === 'active',
                    'bg-yellow-100 text-yellow-700': shop.status === 'pending',
                    'bg-red-100 text-red-700': shop.status === 'rejected'
                }">
                    {{ shop.status }}
                </span>
                <!-- Bouton gérer la boutique pour le propriétaire -->
                <Link v-if="userIsOwner" :href="route('shops.edit', shop.slug)"
                    class="ml-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white">
                Gérer la boutique
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    EnvelopeIcon,
    HeartIcon,
    ListBulletIcon,
    MapPinIcon,
    PhoneIcon,
    Squares2X2Icon,
    StarIcon,
} from '@heroicons/vue/24/outline';
import { router, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    shop: any;
    products: any[];
    categories: any[];
    reviews: any[];
}>();

const user = usePage().props.auth?.user;
const userIsOwner = user && props.shop.user_id === user.id;
const userIsOwnerOrAdmin = user && (props.shop.user_id === user.id || user.role === 'admin');

// État
const activeTab = ref('products');
const viewMode = ref('grid');
const sortBy = ref('newest');
const categoryFilter = ref('');
const currentPage = ref(1);
const showReviewForm = ref(false);
const newReview = ref({
    rating: 0,
    comment: '',
});

const tabs = [
    { id: 'products', name: 'Produits' },
    { id: 'about', name: 'À propos' },
    { id: 'reviews', name: 'Avis' },
];

// Fonctions
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const navigateToProduct = (productId: number) => {
    router.visit(route('products.show', productId));
};

const addToCart = (product: any) => {
    router.post(route('cart.add', product.id), {
        quantity: 1,
    });
};

const addToWishlist = (productId: number) => {
    router.post(route('wishlist.add', productId));
};

const submitReview = () => {
    router.post(route('shops.reviews.store', props.shop.id), newReview.value, {
        onSuccess: () => {
            showReviewForm.value = false;
            newReview.value = { rating: 0, comment: '' };
        },
    });
};

// Filtrage et tri des produits
const filteredProducts = computed(() => {
    let result = [...props.products];

    // Filtre par catégorie
    if (categoryFilter.value) {
        result = result.filter((product) => product.category_id === Number(categoryFilter.value));
    }

    // Tri
    switch (sortBy.value) {
        case 'price_asc':
            result.sort((a, b) => a.price - b.price);
            break;
        case 'price_desc':
            result.sort((a, b) => b.price - a.price);
            break;
        case 'rating':
            result.sort((a, b) => b.rating - a.rating);
            break;
        default:
            // Plus récents par défaut
            break;
    }

    return result;
});
</script>
