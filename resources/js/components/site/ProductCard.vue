<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { CheckBadgeIcon, EyeIcon, HeartIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';
import { POSITION, useToast } from 'vue-toastification';
import { useWishlistStore } from '../../stores/wishlist';
import { Button } from '../ui/button';

interface Product {
    id: number;
    name: string;
    slug: string;
    description?: string;
    default_image: string;
    images: Array<{
        id: number;
        path: string;
        is_default: boolean;
        order: number;
    }>;
    price: number;
    sale_price: number | null;
    formatted_price: string;
    formatted_sale_price: string | null;
    average_rating: number;
    shop: {
        name: string;
        is_verified: boolean;
    };
    isOnSale: boolean;
    discount_percentage: number;
    stock: number;
    composite_score: number;
}

const props = defineProps<{
    product: Product;
    index: number;
}>();

const toast = useToast();
const isAddingToCart = ref(false);
const showSuccessMessage = ref(false);
const quantity = ref(1);
const isHovered = ref(false);
const showQuickView = ref(false);
const isAddingToWishlist = ref(false);
const selectedImage = ref(props.product.default_image || props.product.images[0]?.path);

const page = usePage();
const wishlistStore = useWishlistStore();
const isWishlist = ref(false);
const isLoading = ref(true);

const form = useForm({
    product_id: props.product.id,
    quantity: quantity.value,
});

const stockStatus = computed(() => {
    if (props.product.stock <= 0) return { text: 'Rupture de stock', color: 'text-red-500' };
    if (props.product.stock <= 5) return { text: 'Plus que ' + props.product.stock + ' en stock', color: 'text-orange-500' };
    return { text: 'En stock', color: 'text-green-500' };
});

const incrementQuantity = () => {
    if (quantity.value < props.product.stock) {
        quantity.value++;
        form.quantity = quantity.value;
    }
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
        form.quantity = quantity.value;
    }
};

const addToCart = async () => {
    if (props.product.stock <= 0) {
        toast.error("Ce produit n'est plus disponible en stock.", {
            position: POSITION.TOP_RIGHT,
            timeout: 3000,
        });
        return;
    }

    isAddingToCart.value = true;
    showSuccessMessage.value = false;

    try {
        const response = await axios.post(route('cart.add-item'), {
            product_id: props.product.id,
            quantity: quantity.value,
        });

        if (response.data.success) {
            showSuccessMessage.value = true;
            quantity.value = 1;
            form.quantity = 1;

            // Émettre l'événement de mise à jour du panier
            window.dispatchEvent(new CustomEvent('cart-updated'));

            toast.success(response.data.message || 'Produit ajouté au panier avec succès !', {
                position: POSITION.TOP_RIGHT,
                timeout: 3000,
            });
            setTimeout(() => {
                showSuccessMessage.value = false;
            }, 3000);
        }
    } catch (error: any) {
        let errorMessage = "Une erreur est survenue lors de l'ajout au panier.";

        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        } else if (error.response?.data?.errors) {
            errorMessage = Object.values(error.response.data.errors).flat().join('\n');
        }

        toast.error(errorMessage, {
            position: POSITION.TOP_RIGHT,
            timeout: 5000,
        });

        if (errorMessage.includes('Stock insuffisant')) {
            quantity.value = 1;
            form.quantity = 1;
        }
    } finally {
        isAddingToCart.value = false;
    }
};

const isInWishlist = computed(() => {
    return wishlistStore.isInWishlist(props.product.id);
});

onMounted(async () => {
    try {
        await wishlistStore.fetchWishlist();
        isWishlist.value = isInWishlist.value;
    } catch (error) {
        console.error('Erreur lors du chargement de la wishlist:', error);
    } finally {
        isLoading.value = false;
    }
});

watch(
    isInWishlist,
    (newValue) => {
        isWishlist.value = newValue;
    },
    { immediate: true },
);

const toggleWishlist = async () => {
    if (!page.props.auth?.user) {
        window.location.href = route('login');
        return;
    }

    isAddingToWishlist.value = true;
    try {
        const response = await wishlistStore.toggleItem(props.product.id);
        if (response.success) {
            isWishlist.value = response.isInWishlist;
            // Émettre l'événement de mise à jour de la wishlist
            window.dispatchEvent(new CustomEvent('wishlist-updated'));

            // Afficher un message de succès
            toast.success(response.isInWishlist ? 'Produit ajouté aux favoris' : 'Produit retiré des favoris', {
                position: POSITION.TOP_RIGHT,
                timeout: 3000,
            });
        }
    } catch (error) {
        console.error('Erreur lors de la modification de la wishlist:', error);
        toast.error('Erreur lors de la modification des favoris', {
            position: POSITION.TOP_RIGHT,
            timeout: 3000,
        });
    } finally {
        isAddingToWishlist.value = false;
    }
};

const openQuickView = () => {
    showQuickView.value = true;
    document.body.style.overflow = 'hidden';
};

const closeQuickView = () => {
    showQuickView.value = false;
    document.body.style.overflow = '';
};

const goToProductDetail = () => {
    window.location.href = route('products.show', props.product.slug);
};

const updateSelectedImage = (imagePath: string) => {
    selectedImage.value = imagePath;
};
</script>

<script lang="ts">
export default {
    name: 'ProductCard',
};
</script>

<template>
    <div class="group relative">
        <!-- Skeleton loader -->
        <div v-if="isLoading" class="relative w-full transform overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
            <!-- Image skeleton -->
            <div class="relative aspect-square overflow-hidden">
                <Skeleton class="h-full w-full" />
            </div>

            <!-- Content skeleton -->
            <div class="p-4">
                <!-- Rating and badges skeleton -->
                <div class="mb-2 flex items-center justify-between">
                    <Skeleton class="h-4 w-16" />
                    <Skeleton class="h-4 w-20" />
                </div>

                <!-- Title skeleton -->
                <Skeleton class="mb-2 h-6 w-3/4" />

                <!-- Price skeleton -->
                <Skeleton class="mb-3 h-8 w-24" />

                <!-- Stock status skeleton -->
                <Skeleton class="mb-3 h-4 w-32" />

                <!-- Shop info skeleton -->
                <div class="flex items-center border-t border-gray-100 pt-3 dark:border-gray-700">
                    <Skeleton class="mr-3 h-8 w-8 rounded-full" />
                    <Skeleton class="h-4 w-32" />
                </div>

                <!-- Button skeleton -->
                <Skeleton class="mt-3 h-10 w-full" />
            </div>
        </div>

        <!-- Actual product card -->
        <div
            class="relative w-full transform overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-800"
            :style="{ animationDelay: `${index * 150}ms` }"
            @mouseenter="isHovered = true"
            @mouseleave="isHovered = false"
        >
            <!-- Message de succès -->
            <div v-if="showSuccessMessage" class="absolute inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
                <div class="rounded-lg bg-white p-4 text-center shadow-lg dark:bg-gray-800">
                    <svg class="mx-auto mb-2 h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Produit ajouté au panier !</p>
                </div>
            </div>

            <!-- Image container avec overlay -->
            <div class="relative aspect-square overflow-hidden">
                <img
                    :src="selectedImage"
                    :alt="product.name"
                    class="h-full w-full object-cover transition-transform duration-700"
                    :class="{ 'scale-110': isHovered }"
                />

                <!-- Overlay gradient -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 transition-opacity duration-300"
                    :class="{ 'opacity-100': isHovered }"
                ></div>

                <!-- Quick action buttons -->
                <div
                    class="absolute top-4 right-4 flex flex-col space-y-2 transition-all duration-300"
                    :class="{ 'translate-x-0 opacity-100': isHovered, 'translate-x-4 opacity-0': !isHovered }"
                >
                    <button
                        @click="toggleWishlist"
                        :disabled="isAddingToWishlist"
                        class="transform rounded-full bg-white/90 p-2 shadow-lg backdrop-blur-sm transition-all duration-300 hover:scale-110 hover:bg-white dark:bg-gray-800/90 dark:hover:bg-gray-700"
                        :class="{ 'text-red-500': isWishlist }"
                    >
                        <HeartIcon class="h-4 w-4" :class="{ 'fill-current': isWishlist }" />
                    </button>
                    <button
                        @click="openQuickView"
                        class="transform rounded-full bg-white/90 p-2 shadow-lg backdrop-blur-sm transition-all duration-300 hover:scale-110 hover:bg-white dark:bg-gray-800/90 dark:hover:bg-gray-700"
                    >
                        <EyeIcon class="h-4 w-4 text-gray-600 dark:text-gray-300" />
                    </button>
                </div>

                <!-- Sale badge -->
                <div v-if="product.isOnSale" class="absolute top-4 left-4 transform transition-all duration-300" :class="{ 'scale-110': isHovered }">
                    <span class="animate-pulse rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-lg">
                        -{{ product.discount_percentage }}%
                    </span>
                </div>
            </div>

            <!-- Content container -->
            <div class="p-4">
                <!-- Rating and Popular badge -->
                <div class="mb-2 flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                            />
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ Number(product.average_rating || 0).toFixed(1) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            v-if="product.composite_score > 0.7"
                            class="rounded-full bg-gradient-to-r from-green-500 to-green-600 px-2.5 py-0.5 text-xs font-medium text-white shadow-sm"
                        >
                            Populaire
                        </span>
                        <span
                            v-if="product.isOnSale"
                            class="rounded-full bg-gradient-to-r from-red-500 to-red-600 px-2.5 py-0.5 text-xs font-medium text-white shadow-sm"
                        >
                            -{{ product.discount_percentage }}%
                        </span>
                    </div>
                </div>

                <!-- Product name -->
                <h3
                    class="mb-2 line-clamp-2 font-medium text-gray-900 transition-colors duration-300 group-hover:text-emerald-600 dark:text-gray-100 dark:group-hover:text-emerald-400"
                >
                    <Link :href="route('products.show', product.slug)">
                        {{ product.name }}
                    </Link>
                </h3>

                <!-- Price -->
                <div class="mb-3">
                    <div v-if="product.isOnSale && product.sale_price" class="flex items-center gap-2">
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ product.formatted_sale_price }}</span>
                        <span class="text-sm text-gray-500 line-through dark:text-gray-400">{{ product.formatted_price }}</span>
                    </div>
                    <div v-else class="flex items-center">
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ product.formatted_price }}</span>
                    </div>
                </div>

                <!-- Stock status -->
                <div class="mb-3 flex items-center gap-2">
                    <div
                        class="h-2 w-2 rounded-full"
                        :class="{
                            'bg-red-500': product.stock <= 0,
                            'bg-orange-500': product.stock > 0 && product.stock <= 5,
                            'bg-green-500': product.stock > 5,
                        }"
                    ></div>
                    <p class="text-sm" :class="stockStatus.color">
                        {{ stockStatus.text }}
                    </p>
                </div>

                <!-- Shop info -->
                <div class="flex items-center border-t border-gray-100 pt-3 dark:border-gray-700">
                    <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-emerald-500 to-teal-600">
                        <span class="text-xs font-bold text-white">{{ product.shop?.name?.charAt(0) || '?' }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ product.shop?.name || 'Boutique inconnue' }}</p>
                            <div
                                v-if="product.shop?.is_verified"
                                class="flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300"
                            >
                                <CheckBadgeIcon class="h-3 w-3" />
                                <span>Vérifié</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add to cart button -->
                <Button
                    @click="addToCart"
                    :disabled="isAddingToCart || product.stock <= 0"
                    class="mt-3 w-full transform rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-2 text-sm font-medium text-white transition-all duration-300 hover:scale-105 hover:from-emerald-700 hover:to-emerald-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span v-if="isAddingToCart" class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        Ajout en cours...
                    </span>
                    <span v-else-if="product.stock <= 0" class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                        Rupture de stock
                    </span>
                    <span v-else class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                        Ajouter au panier
                    </span>
                </button>
            </div>
        </div>

        <!-- Modal d'aperçu rapide -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div v-if="showQuickView" class="fixed inset-0 z-[100] flex items-center justify-center" @click.self="closeQuickView">
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

                    <!-- Modal content -->
                    <div class="relative z-10 w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                        <!-- Close button -->
                        <button
                            @click="closeQuickView"
                            class="absolute top-4 right-4 rounded-full bg-gray-100 p-2 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <XMarkIcon class="h-6 w-6" />
                        </button>

                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <!-- Image Gallery -->
                            <div class="relative">
                                <div class="aspect-square overflow-hidden rounded-xl">
                                    <img
                                        :src="selectedImage"
                                        :alt="product.name"
                                        class="h-full w-full object-cover transition-transform duration-500 hover:scale-110"
                                    />
                                </div>
                                <!-- Thumbnails -->
                                <div v-if="product.images?.length > 1" class="mt-4 flex space-x-2 overflow-x-auto pb-2">
                                    <button
                                        v-for="image in product.images"
                                        :key="image.id"
                                        @click="updateSelectedImage(image.path)"
                                        class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg border-2 transition-all duration-300"
                                        :class="[selectedImage === image.path ? 'border-emerald-500' : 'border-transparent hover:border-emerald-300']"
                                    >
                                        <img :src="image.path" :alt="product.name" class="h-full w-full object-cover" />
                                    </button>
                                </div>
                            </div>

                            <!-- Product info -->
                            <div class="flex flex-col justify-between">
                                <div>
                                    <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ product.name }}</h2>

                                    <div class="text-xl font-medium text-foreground">
                                        <p>{{ product.description ?? "" }}</p>
                                    </div>

                                    <!-- Price -->
                                    <div class="mb-4">
                                        <div v-if="product.isOnSale && product.sale_price" class="flex items-center gap-2">
                                            <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{
                                                product.formatted_sale_price
                                            }}</span>
                                            <span class="text-lg text-gray-500 line-through dark:text-gray-400">{{ product.formatted_price }}</span>
                                            <span
                                                class="rounded-full bg-red-100 px-2 py-0.5 text-sm font-medium text-red-800 dark:bg-red-900 dark:text-red-300"
                                            >
                                                -{{ product.discount_percentage }}%
                                            </span>
                                        </div>
                                        <div v-else class="flex items-center">
                                            <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ product.formatted_price }}</span>
                                        </div>
                                    </div>

                                    <!-- Rating -->
                                    <div class="mb-4 flex items-center space-x-2">
                                        <div class="flex items-center space-x-1">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                                />
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-400">{{ Number(product.average_rating || 0).toFixed(1) }}</span>
                                        </div>
                                    </div>

                                    <!-- Stock status -->
                                    <p class="mb-4 text-sm" :class="stockStatus.color">
                                        {{ stockStatus.text }}
                                    </p>

                                    <!-- Shop info -->
                                    <div class="mb-6 flex items-center">
                                        <div
                                            class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-emerald-500 to-teal-600"
                                        >
                                            <span class="text-sm font-bold text-white">{{ product.shop?.name?.charAt(0) || '?' }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ product.shop?.name || 'Boutique inconnue' }}
                                            </p>
                                            <p v-if="product.shop?.is_verified" class="text-xs text-gray-500 dark:text-gray-400">
                                                Boutique certifiée
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col space-y-4">
                                    <!-- Quantity selector -->
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-2">
                                            <Button
                                                @click="decrementQuantity"
                                                :disabled="quantity <= 1 || product.stock <= 0"
                                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-all duration-300 hover:border-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                            >
                                                <span class="text-lg">-</span>
                                            </Button>
                                            <span class="w-12 text-center text-lg font-medium text-gray-900 dark:text-gray-100">{{ quantity }}</span>
                                            <Button
                                                @click="incrementQuantity"
                                                :disabled="quantity >= product.stock"
                                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-all duration-300 hover:border-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                            >
                                                <span class="text-lg">+</span>
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="flex space-x-4">
                                        <Button
                                            @click="addToCart"
                                            :disabled="isAddingToCart || product.stock <= 0"
                                            class="flex-1 transform rounded-lg bg-emerald-600 px-6 py-3 font-medium text-white transition-all duration-300 hover:scale-105 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <span v-if="isAddingToCart" class="flex items-center justify-center gap-2">
                                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                                                    <circle
                                                        class="opacity-25"
                                                        cx="12"
                                                        cy="12"
                                                        r="10"
                                                        stroke="currentColor"
                                                        stroke-width="4"
                                                        fill="none"
                                                    ></circle>
                                                    <path
                                                        class="opacity-75"
                                                        fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                    ></path>
                                                </svg>
                                                Ajout en cours...
                                            </span>
                                            <span v-else-if="product.stock <= 0">Rupture de stock</span>
                                            <span v-else>Ajouter au panier</span>
                                        </Button>
                                        <Button
                                            @click="goToProductDetail"
                                            class="flex-1 transform rounded-lg border border-gray-200 bg-white px-6 py-3 font-medium text-gray-900 transition-all duration-300 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                        >
                                            Voir les détails
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animation de survol pour les boutons d'action */
@keyframes slideIn {
    from {
        transform: translateX(1rem);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Animation de pulsation pour le badge de réduction */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

/* Animation pour le modal */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-content-enter-active,
.modal-content-leave-active {
    transition: all 0.3s ease;
}

.modal-content-enter-from,
.modal-content-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* Animation pour le skeleton */
@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}
</style>
