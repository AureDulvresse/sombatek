<template>
    <div class="min-h-screen bg-gray-50">
        <!-- En-tête de la boutique -->
        <div class="bg-white">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col gap-6 md:flex-row md:items-center">
                    <div class="flex-shrink-0">
                        <img :src="shop.logo" :alt="shop.name" class="h-32 w-32 rounded-full object-cover" />
                    </div>
                    <div class="flex-1">
                        <div class="mb-4 flex items-center justify-between">
                            <h1 class="text-3xl font-bold flex items-center">
                                {{ shop.name }}
                                <span v-if="shop.is_verified"
                                    class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Vérifiée</span>
                            </h1>
                            <button class="rounded-lg border p-2 text-gray-600 hover:bg-gray-50">
                                <HeartIcon class="h-5 w-5" :class="{ 'fill-current text-red-500': isWishlisted }" />
                            </button>
                        </div>
                        <div class="mb-4 flex items-center gap-4">
                            <div class="flex items-center">
                                <StarIcon class="h-5 w-5 text-yellow-400" />
                                <span class="ml-1">{{ shop.rating }}</span>
                                <span class="ml-1 text-gray-600">({{ shop.reviews_count }} avis)</span>
                            </div>
                            <span class="text-gray-600">{{ shop.products_count }} produits</span>
                            <span class="text-gray-600">Membre depuis {{ formatDate(shop.created_at) }}</span>
                        </div>
                        <p class="text-gray-600">{{ shop.description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation de la boutique -->
        <div class="border-b bg-white">
            <div class="container mx-auto px-4">
                <nav class="flex space-x-8">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                        'border-b-2 py-4 text-sm font-medium',
                        activeTab === tab.id
                            ? 'border-primary text-primary'
                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                    ]">
                        {{ tab.name }}
                    </button>
                </nav>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <!-- Onglet Produits -->
            <div v-if="activeTab === 'products'" class="space-y-8">
                <!-- Filtres -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <select v-model="sortBy" class="rounded-lg border p-2">
                            <option value="newest">Plus récents</option>
                            <option value="price_asc">Prix croissant</option>
                            <option value="price_desc">Prix décroissant</option>
                            <option value="rating">Meilleures notes</option>
                        </select>
                        <select v-model="categoryFilter" class="rounded-lg border p-2">
                            <option value="">Toutes les catégories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="viewMode = 'grid'"
                            :class="['p-2', viewMode === 'grid' ? 'text-primary' : 'text-gray-400']">
                            <Squares2X2Icon class="h-5 w-5" />
                        </button>
                        <button @click="viewMode = 'list'"
                            :class="['p-2', viewMode === 'list' ? 'text-primary' : 'text-gray-400']">
                            <ListBulletIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <!-- Grille des produits -->
                <div
                    :class="['grid gap-6', viewMode === 'grid' ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' : 'grid-cols-1']">
                    <div v-for="product in filteredProducts" :key="product.id"
                        :class="['group rounded-lg bg-white p-4 shadow-sm transition-shadow hover:shadow-md', viewMode === 'list' && 'flex gap-4']">
                        <div
                            :class="['relative overflow-hidden rounded-lg', viewMode === 'list' ? 'h-32 w-32 flex-shrink-0' : 'mb-4']">
                            <img :src="product.image" :alt="product.name"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110" />
                            <div class="absolute top-2 right-2">
                                <button class="rounded-full bg-white p-2 text-gray-600 hover:text-primary">
                                    <HeartIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                        <div :class="[viewMode === 'list' && 'flex-1']">
                            <h3 class="mb-2 text-lg font-medium">{{ product.name }}</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-lg font-bold text-primary">{{ product.price }} €</span>
                                    <span v-if="product.oldPrice" class="ml-2 text-sm text-gray-500 line-through"> {{
                                        product.oldPrice }} € </span>
                                </div>
                                <div class="flex items-center">
                                    <StarIcon class="h-4 w-4 text-yellow-400" />
                                    <span class="ml-1 text-sm">{{ product.rating }}</span>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <button @click="addToCart(product)"
                                    class="hover:bg-primary-dark rounded-lg bg-primary px-4 py-2 text-white">
                                    Ajouter au panier
                                </button>
                                <div class="flex gap-2">
                                    <button @click="addToWishlist(product.id)"
                                        class="rounded-lg border p-2 text-gray-600 hover:text-red-500">
                                        <HeartIcon class="h-5 w-5" />
                                    </button>
                                    <button @click="navigateToProduct(product.id)"
                                        class="hover:text-primary-dark text-primary">Voir détails</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    <nav class="flex items-center gap-2">
                        <button class="rounded-lg border p-2 hover:bg-gray-50">
                            <ChevronLeftIcon class="h-5 w-5" />
                        </button>
                        <button v-for="page in 5" :key="page"
                            :class="['rounded-lg px-4 py-2', page === currentPage ? 'bg-primary text-white' : 'hover:bg-gray-50']">
                            {{ page }}
                        </button>
                        <button class="rounded-lg border p-2 hover:bg-gray-50">
                            <ChevronRightIcon class="h-5 w-5" />
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Onglet À propos -->
            <div v-if="activeTab === 'about'" class="space-y-8">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold">À propos de la boutique</h2>
                    <p class="text-gray-600">{{ shop.description }}</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold">Informations</h2>
                    <div class="space-y-4">
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

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold">Horaires d'ouverture</h2>
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

                <a :href="`mailto:${shop.email}`"
                    class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                    Contacter la boutique
                </a>
            </div>

            <!-- Onglet Avis -->
            <div v-if="activeTab === 'reviews'" class="space-y-8">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold">Avis clients</h2>
                            <div class="mt-1 flex items-center">
                                <StarIcon class="h-5 w-5 text-yellow-400" />
                                <span class="ml-1">{{ shop.rating }}</span>
                                <span class="ml-1 text-gray-600">({{ shop.reviews_count }} avis)</span>
                            </div>
                        </div>
                        <button @click="showReviewForm = true"
                            class="hover:bg-primary-dark rounded-lg bg-primary px-4 py-2 text-white">
                            Écrire un avis
                        </button>
                    </div>

                    <!-- Formulaire d'avis -->
                    <div v-if="showReviewForm" class="mb-6 rounded-lg border p-4">
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
                                    class="rounded-lg border px-4 py-2 hover:bg-gray-50">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="hover:bg-primary-dark rounded-lg bg-primary px-4 py-2 text-white">Publier</button>
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
                            <p class="text-gray-600">{{ review.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statut visible pour admin/proprio -->
        <div v-if="userIsOwnerOrAdmin" class="mt-2">
            <span class="text-xs px-2 py-1 rounded-full" :class="{
                'bg-green-100 text-green-700': shop.status === 'active',
                'bg-yellow-100 text-yellow-700': shop.status === 'pending',
                'bg-red-100 text-red-700': shop.status === 'rejected'
            }">
                {{ shop.status }}
            </span>
        </div>

        <!-- Bouton gérer la boutique pour le propriétaire -->
        <Link v-if="userIsOwner" :href="route('shops.edit', shop.slug)"
            class="ml-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
        Gérer la boutique
        </Link>
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
