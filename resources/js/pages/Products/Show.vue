<template>
    <SiteLayout :title="product.name">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8 dark:from-gray-900 dark:to-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Galerie d'images -->
                    <div class="space-y-4">
                        <div class="overflow-hidden rounded-2xl bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:bg-gray-800">
                            <img :src="selectedImage || product.default_image" :alt="product.name" class="h-[500px] w-full object-cover" />
                        </div>
                        <div class="grid grid-cols-4 gap-4">
                            <div
                                v-for="image in product.images"
                                :key="image.id"
                                class="cursor-pointer overflow-hidden rounded-xl bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:bg-gray-800"
                                @click="selectedImage = image.path"
                                :class="{ 'ring-2 ring-emerald-500': selectedImage === image.path }"
                            >
                                <img :src="image.path" :alt="product.name" class="h-24 w-full object-cover" />
                            </div>
                        </div>
                    </div>

                    <!-- Informations produit -->
                    <div class="space-y-6">
                        <div class="rounded-2xl bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-lg dark:bg-gray-800">
                            <h1 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white">{{ product.name }}</h1>

                            <p class="mb-4 text-gray-600 dark:text-gray-400">{{ product.description }}</p>

                            <!-- Prix et réduction -->
                            <div class="mb-6">
                                <div v-if="product.isOnSale" class="flex items-center gap-3">
                                    <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ product.formatted_sale_price }}</span>
                                    <span class="text-xl text-gray-500 line-through dark:text-gray-400">{{ product.formatted_price }}</span>
                                    <span
                                        class="rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 dark:bg-red-900 dark:text-red-300"
                                    >
                                        -{{ product.discount_percentage }}%
                                    </span>
                                </div>
                                <div v-else>
                                    <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ product.formatted_price }}</span>
                                </div>
                            </div>

                            <!-- Note moyenne -->
                            <div class="mb-6 flex items-center gap-3">
                                <div class="flex items-center">
                                    <template v-for="n in 5" :key="n">
                                        <svg
                                            class="h-6 w-6"
                                            :class="n <= Math.round(product.average_rating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                            />
                                        </svg>
                                    </template>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">({{ product.reviews_count }} avis)</span>
                            </div>

                            <p class="mb-8 text-gray-600 dark:text-gray-300">{{ product.description }}</p>

                            <!-- Variantes du produit -->
                            <div v-if="product.variations" class="mb-8 space-y-6">
                                <div v-for="(variation, index) in product.variations" :key="index" class="space-y-3">
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ variation.name }}</h3>
                                    <div class="flex flex-wrap gap-3">
                                        <button
                                            v-for="option in variation.options"
                                            :key="option.value"
                                            @click="selectVariation(variation.name, option)"
                                            class="rounded-xl border px-4 py-2 text-sm font-medium transition-all duration-300"
                                            :class="{
                                                'border-emerald-500 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-900/30 dark:text-emerald-400':
                                                    isVariationSelected(variation.name, option),
                                                'border-gray-300 text-gray-700 hover:border-emerald-500 hover:text-emerald-700 dark:border-gray-600 dark:text-gray-300 dark:hover:border-emerald-400 dark:hover:text-emerald-400':
                                                    !isVariationSelected(variation.name, option),
                                            }"
                                        >
                                            {{ option.value }}
                                            <span v-if="option.price_adjustment > 0" class="ml-1 text-xs">
                                                (+{{ formatPrice(option.price_adjustment) }})
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Sélecteur de quantité -->
                            <div class="mb-8">
                                <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Quantité</label>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="quantity > 1 && quantity--"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition-all duration-300 hover:border-gray-400 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                            :disabled="quantity <= 1"
                                        >
                                            <span class="text-lg">-</span>
                                        </button>
                                        <input
                                            type="number"
                                            v-model="quantity"
                                            class="w-16 rounded-lg border border-gray-300 bg-white p-2 text-center text-gray-900 transition-all duration-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                            min="1"
                                            :max="product.stock"
                                        />
                                        <button
                                            @click="quantity < product.stock && quantity++"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition-all duration-300 hover:border-gray-400 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                            :disabled="quantity >= product.stock"
                                        >
                                            <span class="text-lg">+</span>
                                        </button>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ product.stock }} unité{{ product.stock > 1 ? 's' : '' }} disponible{{ product.stock > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex gap-4">
                                <button
                                    @click="addToCart"
                                    class="flex-1 transform rounded-xl bg-emerald-600 px-6 py-3 text-sm font-medium text-white transition-all duration-300 hover:scale-105 hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="!canAddToCart"
                                >
                                    Ajouter au panier
                                </button>
                                <button
                                    @click="buyNow"
                                    class="flex-1 transform rounded-xl border border-emerald-600 bg-white px-6 py-3 text-sm font-medium text-emerald-600 transition-all duration-300 hover:scale-105 hover:bg-emerald-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-emerald-400 dark:bg-gray-800 dark:text-emerald-400 dark:hover:bg-gray-700"
                                    :disabled="!canAddToCart"
                                >
                                    Acheter maintenant
                                </button>
                                <button
                                    @click="addToWishlist"
                                    class="transform rounded-xl border border-gray-300 bg-white p-3 text-gray-600 transition-all duration-300 hover:scale-105 hover:border-red-500 hover:text-red-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-400 dark:hover:text-red-400"
                                >
                                    <HeartIcon class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="rounded-2xl bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-lg dark:bg-gray-800">
                            <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Informations supplémentaires</h2>
                            <div class="space-y-6">
                                <div>
                                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Livraison</h3>
                                    <p class="text-gray-600 dark:text-gray-300">Livraison gratuite à partir de 50€ d'achat</p>
                                </div>
                                <div>
                                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Retours</h3>
                                    <p class="text-gray-600 dark:text-gray-300">30 jours pour changer d'avis</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produits suggérés -->
                <div class="mt-16">
                    <h2 class="mb-8 text-2xl font-bold text-gray-900 dark:text-white">Produits similaires</h2>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <ProductCard
                            v-for="suggestedProduct in suggestedProducts"
                            :key="suggestedProduct.id"
                            :product="suggestedProduct"
                            :index="0"
                            @click="navigateToProduct(suggestedProduct.id)"
                            @add-to-cart="addToCart(suggestedProduct)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>

<script setup lang="ts">
import ProductCard from '@/components/site/ProductCard.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { HeartIcon } from '@heroicons/vue/24/outline';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Variation {
    name: string;
    options: Array<{
        value: string;
        price_adjustment: number;
    }>;
}

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
    variations: Variation[] | null;
    average_rating: number;
    reviews_count: number;
    isOnSale: boolean;
    discount_percentage: number;
    shop: {
        id: number;
        name: string;
        slug: string;
        logo: string;
        is_verified: boolean;
    };
}

interface Props {
    product: Product;
    suggestedProducts: Product[];
}

const props = defineProps<Props>();

const quantity = ref(1);
const selectedImage = ref<string | null>(null);
const selectedVariations = ref<Record<string, any>>({});

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(price);
};

const selectVariation = (variationName: string, option: any) => {
    selectedVariations.value[variationName] = option;
};

const isVariationSelected = (variationName: string, option: any) => {
    return selectedVariations.value[variationName]?.value === option.value;
};

const canAddToCart = computed(() => {
    if (!props.product.variations) return true;

    // Vérifier si toutes les variantes requises sont sélectionnées
    return props.product.variations.every((variation : Variation) => selectedVariations.value[variation.name] !== undefined);
});

const addToCart = (product: Product) => {
    if (!canAddToCart.value) return;

    router.post(route('cart.add', product.id), {
        quantity: quantity.value,
        variations: selectedVariations.value,
    });
};

const buyNow = () => {
    addToCart(props.product);
    router.visit(route('checkout'));
};

const addToWishlist = () => {
    router.post(route('wishlist.add', props.product.id));
};

const navigateToProduct = (productId: number) => {
    router.visit(route('products.show', productId));
};
</script>

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
