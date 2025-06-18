<template>
    <SiteLayout title="Mon Panier">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="mb-8 text-3xl font-bold text-gray-900 dark:text-white">Mon Panier</h1>

            <div v-if="cart.items.length === 0" class="rounded-lg bg-white p-8 text-center shadow-sm dark:bg-gray-800">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                    />
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Votre panier est vide</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Découvrez nos produits et commencez vos achats</p>
                <Link
                    :href="route('products.index')"
                    class="mt-6 inline-block rounded-lg bg-emerald-600 px-6 py-3 text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                >
                    Continuer mes achats
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Liste des produits -->
                <div class="lg:col-span-2">
                    <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div v-for="item in cart.items" :key="item.id" class="p-6">
                                <div class="flex items-center gap-6">
                                    <!-- Image du produit -->
                                    <Link
                                        :href="route('products.show', item.product.slug)"
                                        class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg"
                                    >
                                        <img :src="item.product.default_image" :alt="item.product.name" class="h-full w-full object-cover" />
                                    </Link>

                                    <!-- Informations du produit -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <Link
                                                    :href="route('products.show', item.product.slug)"
                                                    class="text-lg font-medium text-gray-900 hover:text-emerald-600 dark:text-white dark:hover:text-emerald-400"
                                                >
                                                    {{ item.product.name }}
                                                </Link>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Boutique :
                                                    <Link
                                                        :href="route('shops.show', item.product.shop.slug)"
                                                        class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300"
                                                    >
                                                        {{ item.product.shop.name }}
                                                    </Link>
                                                </p>
                                            </div>
                                            <button
                                                @click="removeItem(item.id)"
                                                class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400"
                                                :disabled="isRemoving"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>

                                        <div class="mt-4 flex items-center justify-between">
                                            <!-- Sélecteur de quantité -->
                                            <div class="flex items-center space-x-3">
                                                <button
                                                    @click="updateQuantity(item.id, item.quantity - 1)"
                                                    :disabled="item.quantity <= 1 || isUpdating"
                                                    class="rounded-lg border border-gray-300 p-1 text-gray-600 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                                >
                                                    <span class="sr-only">Diminuer la quantité</span>
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <span class="text-gray-900 dark:text-white">{{ item.quantity }}</span>
                                                <button
                                                    @click="updateQuantity(item.id, item.quantity + 1)"
                                                    :disabled="item.quantity >= item.product.stock || isUpdating"
                                                    class="rounded-lg border border-gray-300 p-1 text-gray-600 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700"
                                                >
                                                    <span class="sr-only">Augmenter la quantité</span>
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Prix -->
                                            <div class="text-right">
                                                <p class="text-lg font-medium text-gray-900 dark:text-white">
                                                    {{ item.product.formatted_sale_price || item.product.formatted_price }}
                                                </p>
                                                <p v-if="item.product.sale_price" class="text-sm text-gray-500 line-through dark:text-gray-400">
                                                    {{ item.product.formatted_price }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-gray-200 p-6 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <button
                                    @click="clearCart"
                                    class="text-sm text-gray-600 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400"
                                >
                                    Vider le panier
                                </button>
                                <Link
                                    :href="route('products.index')"
                                    class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300"
                                >
                                    Continuer mes achats
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Résumé de la commande -->
                <div class="lg:col-span-1">
                    <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Résumé de la commande</h2>

                        <div class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Sous-total</span>
                                <span class="text-gray-900 dark:text-white">{{ cart.formatted_subtotal }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Frais de livraison</span>
                                <span class="text-gray-900 dark:text-white">{{ cart.formatted_shipping_cost }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">TVA</span>
                                <span class="text-gray-900 dark:text-white">{{ cart.formatted_tax }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-medium text-gray-900 dark:text-white">Total</span>
                                    <span class="text-lg font-medium text-gray-900 dark:text-white">{{ cart.formatted_total }}</span>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="proceedToCheckout"
                            class="mt-6 w-full rounded-lg bg-emerald-600 px-6 py-3 text-center text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                        >
                            Passer la commande
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>

<script setup lang="ts">
import SiteLayout from '@/layouts/SiteLayout.vue';
import { TrashIcon } from '@heroicons/vue/24/outline';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface CartItem {
    id: number;
    quantity: number;
    product: {
        id: number;
        name: string;
        slug: string;
        price: number;
        sale_price: number | null;
        formatted_price: string;
        formatted_sale_price: string | null;
        default_image: string;
        stock: number;
        shop: {
            id: number;
            name: string;
            slug: string;
        };
    };
}

interface Props {
    cart: {
        id: number;
        items: CartItem[];
        subtotal: number;
        formatted_subtotal: string;
        total: number;
        formatted_total: string;
        shipping_cost: number;
        formatted_shipping_cost: string;
        tax: number;
        formatted_tax: string;
    };
}

const props = defineProps<Props>();

const isUpdating = ref(false);
const isRemoving = ref(false);

const updateQuantity = async (itemId: number, quantity: number) => {
    if (isUpdating.value) return;
    isUpdating.value = true;

    try {
        await router.post(route('cart.update-quantity'), {
            item_id: itemId,
            quantity: quantity,
        });
    } catch (error) {
        console.error('Erreur lors de la mise à jour de la quantité:', error);
    } finally {
        isUpdating.value = false;
    }
};

const removeItem = async (itemId: number) => {
    if (isRemoving.value) return;
    isRemoving.value = true;

    try {
        await router.post(route('cart.remove-item'), {
            item_id: itemId,
        });
    } catch (error) {
        console.error('Erreur lors de la suppression du produit:', error);
    } finally {
        isRemoving.value = false;
    }
};

const clearCart = async () => {
    if (!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) return;

    try {
        await router.post(route('cart.clear'));
    } catch (error) {
        console.error('Erreur lors de la suppression du panier:', error);
    }
};

const proceedToCheckout = () => {
    router.visit(route('checkout'));
};
</script>
