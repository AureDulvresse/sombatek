<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { EyeIcon, HeartIcon, ShoppingCartIcon, SparklesIcon, X, BadgeCheck } from 'lucide-vue-next';
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
const imageLoaded = ref(false);

const page = usePage();
const wishlistStore = useWishlistStore();
const isWishlist = ref(false);
const isLoading = ref(true);

const form = useForm({
    product_id: props.product.id,
    quantity: quantity.value,
});

const stockStatus = computed(() => {
    if (props.product.stock <= 0) return {
        text: 'Rupture de stock',
        color: 'text-red-500',
        bgColor: 'bg-red-50 border-red-200',
        dotColor: 'bg-red-500'
    };
    if (props.product.stock <= 5) return {
        text: `Plus que ${props.product.stock} en stock`,
        color: 'text-amber-600',
        bgColor: 'bg-amber-50 border-amber-200',
        dotColor: 'bg-amber-500'
    };
    return {
        text: 'En stock',
        color: 'text-emerald-600',
        bgColor: 'bg-emerald-50 border-emerald-200',
        dotColor: 'bg-emerald-500'
    };
});

const isPremiumProduct = computed(() => props.product.composite_score > 0.8);

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

            window.dispatchEvent(new CustomEvent('cart-updated'));

            toast.success(response.data.message || 'Produit ajouté au panier avec succès !', {
                position: POSITION.TOP_RIGHT,
                timeout: 3000,
            });

            setTimeout(() => {
                showSuccessMessage.value = false;
            }, 2500);
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
            window.dispatchEvent(new CustomEvent('wishlist-updated'));

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

const updateSelectedImage = (imagePath: string) => {
    selectedImage.value = imagePath;
};

const onImageLoad = () => {
    imageLoaded.value = true;
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
        <div v-if="isLoading"
            class="relative w-full overflow-hidden rounded-2xl bg-white/80 backdrop-blur-sm shadow-sm border border-gray-100 dark:bg-gray-800/80 dark:border-gray-700">
            <!-- Image skeleton -->
            <div class="relative aspect-[4/5] overflow-hidden">
                <Skeleton class="h-full w-full animate-pulse" />
            </div>

            <!-- Content skeleton -->
            <div class="p-5">
                <!-- Rating and badges skeleton -->
                <div class="mb-3 flex items-center justify-between">
                    <Skeleton class="h-4 w-20 rounded-full" />
                    <Skeleton class="h-5 w-16 rounded-full" />
                </div>

                <!-- Title skeleton -->
                <Skeleton class="mb-3 h-6 w-4/5 rounded-lg" />

                <!-- Price skeleton -->
                <Skeleton class="mb-4 h-8 w-28 rounded-lg" />

                <!-- Stock status skeleton -->
                <Skeleton class="mb-4 h-8 w-36 rounded-full" />

                <!-- Shop info skeleton -->
                <div class="flex items-center border-t border-gray-100 pt-4 dark:border-gray-700">
                    <Skeleton class="mr-3 h-10 w-10 rounded-full" />
                    <div class="flex-1">
                        <Skeleton class="mb-1 h-4 w-32 rounded" />
                        <Skeleton class="h-3 w-20 rounded" />
                    </div>
                </div>

                <!-- Button skeleton -->
                <Skeleton class="mt-4 h-12 w-full rounded-xl" />
            </div>
        </div>

        <!-- Actual product card -->
        <div v-else
            class="product-card relative w-full overflow-hidden rounded-2xl bg-white/90 backdrop-blur-sm border border-gray-100/80 shadow-sm transition-all duration-500 ease-out hover:shadow-2xl hover:shadow-gray-900/10 hover:-translate-y-2 hover:border-gray-200/80 dark:bg-gray-800/90 dark:border-gray-700/80 dark:hover:border-gray-600/80"
            :style="{ animationDelay: `${index * 100}ms` }" @mouseenter="isHovered = true"
            @mouseleave="isHovered = false">
            <!-- Success animation overlay -->
            <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                <div v-if="showSuccessMessage"
                    class="absolute inset-0 z-50 flex items-center justify-center bg-emerald-500/95 backdrop-blur-md rounded-2xl">
                    <div class="text-center text-white animate-bounce">
                        <div
                            class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-lg font-semibold">Ajouté au panier !</p>
                        <p class="text-sm opacity-90">Continuez vos achats</p>
                    </div>
                </div>
            </Transition>

            <!-- Image container -->
            <div class="relative aspect-[4/5] overflow-hidden bg-gray-50 dark:bg-gray-900">
                <img :src="selectedImage" :alt="product.name"
                    class="h-full w-full object-cover transition-all duration-700 ease-out" :class="{
                        'scale-110 brightness-110': isHovered,
                        'opacity-0': !imageLoaded
                    }" @load="onImageLoad" />

                <!-- Image loading placeholder -->
                <div v-if="!imageLoaded"
                    class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                    <div class="h-8 w-8 animate-spin rounded-full border-2 border-gray-300 border-t-emerald-500"></div>
                </div>

                <!-- Gradient overlay on hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent transition-opacity duration-500"
                    :class="{ 'opacity-100': isHovered, 'opacity-0': !isHovered }" />

                <!-- Action buttons -->
                <div class="absolute top-4 right-4 flex flex-col gap-2 transition-all duration-300"
                    :class="{ 'translate-x-0 opacity-100': isHovered, 'translate-x-8 opacity-0': !isHovered }">
                    <button @click="toggleWishlist" :disabled="isAddingToWishlist"
                        class="group/btn relative flex h-10 w-10 items-center justify-center rounded-full bg-white/95 backdrop-blur-sm shadow-lg transition-all duration-300 hover:scale-110 hover:bg-white dark:bg-gray-800/95 dark:hover:bg-gray-700"
                        :class="{ 'text-red-500 bg-red-50': isWishlist }">
                        <HeartIcon v-if="isWishlist" class="h-5 w-5 text-red-500 fill-current" />
                        <HeartIcon v-else
                            class="h-5 w-5 text-gray-600 dark:text-gray-300 group-hover/btn:text-red-500" />

                        <!-- Loading spinner for wishlist -->
                        <div v-if="isAddingToWishlist" class="absolute inset-0 flex items-center justify-center">
                            <div class="h-4 w-4 animate-spin rounded-full border border-gray-300 border-t-red-500">
                            </div>
                        </div>
                    </button>

                    <button @click="openQuickView"
                        class="group/btn flex h-10 w-10 items-center justify-center rounded-full bg-white/95 backdrop-blur-sm shadow-lg transition-all duration-300 hover:scale-110 hover:bg-white dark:bg-gray-800/95 dark:hover:bg-gray-700">
                        <EyeIcon class="h-5 w-5 text-gray-600 dark:text-gray-300 group-hover/btn:text-emerald-500" />
                    </button>
                </div>

                <!-- Quick add to cart button on hover -->
                <div class="absolute bottom-4 left-4 right-4 transition-all duration-300"
                    :class="{ 'translate-y-0 opacity-100': isHovered, 'translate-y-4 opacity-0': !isHovered }">
                    <Button @click="addToCart" :disabled="isAddingToCart || product.stock <= 0"
                        class="w-full rounded-xl bg-white/95 backdrop-blur-sm text-gray-900 shadow-lg transition-all duration-300 hover:bg-white hover:scale-105 disabled:cursor-not-allowed disabled:opacity-50">
                        <ShoppingCartIcon v-if="!isAddingToCart" class="mr-2 h-4 w-4" />
                        <div v-if="isAddingToCart"
                            class="mr-2 h-4 w-4 animate-spin rounded-full border border-gray-400 border-t-gray-900">
                        </div>
                        <span v-if="isAddingToCart">Ajout...</span>
                        <span v-else-if="product.stock <= 0">Rupture</span>
                        <span v-else>Ajouter</span>
                    </Button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-5">
                <!-- Rating and badges -->
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ Number(product.average_rating || 0).toFixed(1) }}
                            </span>
                        </div>
                    </div>

                    <!-- Premium badge -->
                    <div v-if="isPremiumProduct" class="absolute top-3 left-3 z-20">
                        <div
                            class="flex items-center gap-1 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 px-3 py-1.5 text-xs font-semibold text-white shadow-lg">
                            <SparklesIcon class="h-3 w-3" />
                            <span>Premium</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span v-if="product.composite_score > 0.7"
                            class="rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
                            Populaire
                        </span>
                    </div>
                </div>

                <!-- Product name -->
                <div class="h-20 line-clamp-2">
                    <h3
                        class="mb-3 line-clamp-2 text-lg font-semibold text-gray-900 transition-colors duration-300 group-hover:text-emerald-600 dark:text-gray-100 dark:group-hover:text-emerald-400">
                        <Link :href="route('products.show', product.slug)" class="hover:underline">
                        {{ product.name }}
                        </Link>
                    </h3>
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <div v-if="product.isOnSale && product.sale_price">

                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ product.formatted_sale_price }}
                            </span>
                            <span class="text-lg text-gray-500 line-through dark:text-gray-400">
                                {{ product.formatted_price }}
                            </span>
                        </div>
                        <!-- Sale badge -->
                        <div
                            class="w-14 rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800 dark:bg-red-900 dark:text-red-300">
                            -{{ product.discount_percentage }}%
                        </div>
                    </div>

                    <div v-else>
                        <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ product.formatted_price }}
                        </span>
                    </div>
                </div>

                <!-- Stock status -->
                <div class="mb-4">
                    <div class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-sm font-medium"
                        :class="[stockStatus.bgColor, stockStatus.color]">
                        <div class="h-2 w-2 rounded-full" :class="stockStatus.dotColor"></div>
                        <span>{{ stockStatus.text }}</span>
                    </div>
                </div>

                <!-- Shop info -->
                <div class="flex items-center border-t border-gray-100 pt-4 dark:border-gray-700">
                    <div
                        class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 shadow-sm">
                        <span class="text-sm font-bold text-white">
                            {{ product.shop?.name?.charAt(0) || '?' }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-gray-100 line-clamp-1">
                            {{ product.shop?.name || 'Boutique inconnue' }}
                        </p>
                        <div v-if="product.shop?.is_verified"
                            class="flex items-center gap-1 w-18 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                            <BadgeCheck class="h-3 w-3" />
                            <span>Vérifié</span>
                        </div>
                    </div>
                </div>

                <!-- Main CTA Button -->
                <Button @click="addToCart" :disabled="isAddingToCart || product.stock <= 0"
                    class="mt-4 w-full rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:from-emerald-700 hover:to-emerald-800 hover:shadow-emerald-500/25 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100">
                    <div v-if="isAddingToCart" class="flex items-center justify-center gap-2">
                        <div class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></div>
                        <span>Ajout en cours...</span>
                    </div>
                    <div v-else-if="product.stock <= 0" class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Rupture de stock</span>
                    </div>
                    <div v-else class="flex items-center justify-center gap-2">
                        <ShoppingCartIcon class="h-4 w-4" />
                        <span>Ajouter au panier</span>
                    </div>
                </Button>
            </div>
        </div>

        <!-- Quick View Modal -->
        <Teleport to="body">
            <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showQuickView" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                    @click.self="closeQuickView">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-md"></div>

                    <!-- Modal -->
                    <Transition enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="opacity-0 scale-95 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-200 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-4">
                        <div v-if="showQuickView"
                            class="relative z-10 w-full max-w-5xl max-h-[90vh] overflow-auto rounded-3xl bg-white/95 backdrop-blur-xl shadow-2xl dark:bg-gray-800/95">
                            <!-- Close button -->
                            <button @click="closeQuickView"
                                class="absolute top-6 right-6 z-20 rounded-full bg-white/80 p-2 text-gray-600 shadow-lg backdrop-blur-sm transition-all duration-300 hover:bg-white hover:text-gray-900 dark:bg-gray-800/80 dark:text-gray-300 dark:hover:bg-gray-700">
                                <X class="h-6 w-6" />
                            </button>

                            <div class="grid grid-cols-1 gap-8 p-8 lg:grid-cols-2">
                                <!-- Image Gallery -->
                                <div class="relative">
                                    <div class="aspect-square overflow-hidden rounded-2xl bg-gray-50 dark:bg-gray-900">
                                        <img :src="selectedImage" :alt="product.name"
                                            class="h-full w-full object-cover transition-transform duration-500 hover:scale-105" />
                                    </div>

                                    <!-- Thumbnails -->
                                    <div v-if="product.images?.length > 1" class="mt-4 flex gap-3 overflow-x-auto pb-2">
                                        <button v-for="image in product.images" :key="image.id"
                                            @click="updateSelectedImage(image.path)"
                                            class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl border-2 transition-all duration-300"
                                            :class="[
                                                selectedImage === image.path
                                                    ? 'border-emerald-500 ring-2 ring-emerald-200'
                                                    : 'border-gray-200 hover:border-emerald-300 dark:border-gray-600'
                                            ]">
                                            <img :src="image.path" :alt="product.name"
                                                class="h-full w-full object-cover" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="flex flex-col justify-between">
                                    <div>
                                        <!-- Header -->
                                        <div class="mb-6">
                                            <h2 class="mb-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                                                {{ product.name }}
                                            </h2>
                                            <p v-if="product.description" class="text-gray-600 dark:text-gray-400">
                                                {{ product.description }}
                                            </p>
                                        </div>

                                        <!-- Price -->
                                        <div class="mb-6">
                                            <div v-if="product.isOnSale && product.sale_price"
                                                class="flex items-baseline gap-3">
                                                <span class="text-4xl font-bold text-gray-900 dark:text-gray-100">
                                                    {{ product.formatted_sale_price }}
                                                </span>
                                                <span class="text-xl text-gray-500 line-through dark:text-gray-400">
                                                    {{ product.formatted_price }}
                                                </span>
                                                <span
                                                    class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    -{{ product.discount_percentage }}%
                                                </span>
                                            </div>
                                            <div v-else>
                                                <span class="text-4xl font-bold text-gray-900 dark:text-gray-100">
                                                    {{ product.formatted_price }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Rating & Stock -->
                                        <div class="mb-6 space-y-4">
                                            <div class="flex items-center gap-2">
                                                <div class="flex items-center">
                                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <span
                                                        class="ml-2 text-lg font-medium text-gray-700 dark:text-gray-300">
                                                        {{ Number(product.average_rating || 0).toFixed(1) }}
                                                    </span>
                                                </div>
                                                <div class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-sm font-medium"
                                                    :class="[stockStatus.bgColor, stockStatus.color]">
                                                    <div class="h-2 w-2 rounded-full" :class="stockStatus.dotColor">
                                                    </div>
                                                    <span>{{ stockStatus.text }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Shop info -->
                                        <div class="mb-6 flex items-center">
                                            <div
                                                class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 shadow-sm">
                                                <span class="text-lg font-bold text-white">
                                                    {{ product.shop?.name?.charAt(0) || '?' }}
                                                </span>
                                            </div>
                                            <div>

                                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                    {{ product.shop?.name || 'Boutique inconnue' }}
                                                </p>
                                                <div v-if="product.shop?.is_verified"
                                                    class="flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                                    <BadgeCheck class="h-3 w-3" />
                                                    <span>Vérifié</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="space-y-4">
                                        <button @click="addToCart" :disabled="isAddingToCart || product.stock <= 0"
                                            class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-2 text-lg font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:from-emerald-700 hover:to-emerald-800 hover:shadow-emerald-500/25 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100">
                                            <div v-if="isAddingToCart" class="flex items-center justify-center gap-2">
                                                <div
                                                    class="h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white">
                                                </div>
                                                <span>Ajout en cours...</span>
                                            </div>
                                            <div v-else-if="product.stock <= 0"
                                                class="flex items-center justify-center gap-2">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <span>Rupture de stock</span>
                                            </div>
                                            <div v-else class="flex items-center justify-center gap-2">
                                                <ShoppingCartIcon class="h-5 w-5" />
                                                <span>Ajouter au panier</span>
                                            </div>
                                        </button>

                                        <Link :href="route('products.show', product.slug)"
                                            class="block w-full rounded-xl border-2 border-emerald-600 bg-transparent px-8 py-2 text-center text-lg font-semibold text-emerald-600 transition-all duration-300 hover:bg-emerald-600 hover:text-white">
                                        Voir les détails
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>