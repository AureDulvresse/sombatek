<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import PromotionForm from './PromotionForm.vue';
import axios from 'axios';

interface CartItem {
    id: number;
    quantity: number;
    product: {
        id: number;
        name: string;
        price: number;
        image: string;
        shop: {
            id: number;
            name: string;
        };
    };
    options: Record<string, string>;
}

interface Cart {
    id: number;
    items: CartItem[];
    subtotal: number;
    total: number;
    discount_total: number;
    promotion_code: string | null;
    promotion_discount: number;
}

defineProps<{
    cart: Cart;
}>();

const toast = useToast();
const isUpdating = ref(false);
const isRemoving = ref(false);

useForm({
    quantity: 1,
});

/**
 * Met à jour la quantité d'un article dans le panier.
 *
 * @param {number} itemId - L'ID de l'article.
 * @param {number} quantity - La nouvelle quantité.
 * @returns {Promise<void>}
 */
const updateQuantity = async (itemId: number, quantity: number) => {
    if (isUpdating.value) return;
    isUpdating.value = true;

    try {
        await axios.post(route('cart.update-quantity'), {
            item_id: itemId,
            quantity: quantity,
        });
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la mise à jour de la quantité');
    } finally {
        isUpdating.value = false;
    }
};

const removeItem = async (itemId: number) => {
    if (isRemoving.value) return;
    isRemoving.value = true;

    try {
        await axios.post(route('cart.remove-item'), { item_id: itemId });
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de la suppression de l'article");
    } finally {
        isRemoving.value = false;
    }
};

const clearCart = async () => {
    if (!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) return;

    try {
        await axios.post(route('cart.clear'));
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors du vidage du panier');
    }
};

const handlePromotionApplied = (code: string) => {
    toast.success(`Code promo "${code}" appliqué avec succès`);
    window.location.reload();
};

const handlePromotionRemoved = () => {
    toast.success(`Code promo supprimé avec succès`);
    window.location.reload();
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mon panier</h2>
            <button
                v-if="cart.items.length > 0"
                @click="clearCart"
                class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 focus:ring-4 focus:ring-red-200 focus:outline-none dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900 dark:focus:ring-red-800"
            >
                Vider le panier
            </button>
        </div>

        <div v-if="cart.items.length === 0" class="rounded-lg border border-gray-200 bg-white p-6 text-center dark:border-gray-700 dark:bg-gray-800">
            <p class="text-gray-600 dark:text-gray-400">Votre panier est vide</p>
            <Link
                :href="route('products.index')"
                class="mt-4 inline-block rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
                Continuer mes achats
            </Link>
        </div>

        <div v-else class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <div
                        v-for="item in cart.items"
                        :key="item.id"
                        class="flex items-center space-x-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg">
                            <img :src="item.product.image" :alt="item.product.name" class="h-full w-full object-cover object-center" />
                        </div>

                        <div class="flex flex-1 flex-col">
                            <div class="flex justify-between">
                                <div>
                                    <Link
                                        :href="route('products.show', item.product.id)"
                                        class="text-lg font-medium text-gray-900 hover:text-green-600 dark:text-white dark:hover:text-green-400"
                                    >
                                        {{ item.product.name }}
                                    </Link>
                                    <Link
                                        :href="route('shops.show', item.product.shop.id)"
                                        class="mt-1 block text-sm text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400"
                                    >
                                        {{ item.product.shop.name }}
                                    </Link>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ (item.product.price * item.quantity).toFixed(2) }} €
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ item.product.price.toFixed(2) }} € l'unité</p>
                                </div>
                            </div>

                            <div v-if="Object.keys(item.options).length > 0" class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Options :
                                    <span
                                        v-for="(value, key) in item.options"
                                        :key="key"
                                        class="ml-1 inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                    >
                                        {{ key }}: {{ value }}
                                    </span>
                                </p>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="updateQuantity(item.id, item.quantity - 1)"
                                        :disabled="isUpdating || item.quantity <= 1"
                                        class="rounded-lg border border-gray-300 bg-white p-1 text-gray-500 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center text-gray-900 dark:text-white">
                                        {{ item.quantity }}
                                    </span>
                                    <button
                                        @click="updateQuantity(item.id, item.quantity + 1)"
                                        :disabled="isUpdating"
                                        class="rounded-lg border border-gray-300 bg-white p-1 text-gray-500 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                                <button
                                    @click="removeItem(item.id)"
                                    :disabled="isRemoving"
                                    class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50 focus:ring-4 focus:ring-red-200 focus:outline-none dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900 dark:focus:ring-red-800"
                                >
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <PromotionForm
                    :promotion-code="cart.promotion_code"
                    :promotion-discount="cart.promotion_discount"
                    @promotion-applied="handlePromotionApplied"
                    @promotion-removed="handlePromotionRemoved"
                />

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Résumé de la commande</h3>

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Sous-total</span>
                            <span class="text-gray-900 dark:text-white">{{ cart.subtotal.toFixed(2) }} €</span>
                        </div>

                        <div v-if="cart.discount_total > 0" class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Réduction</span>
                            <span class="text-green-600 dark:text-green-400"> -{{ cart.discount_total.toFixed(2) }} € </span>
                        </div>

                        <div v-if="cart.promotion_discount > 0" class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400"> Code promo ({{ cart.promotion_code }}) </span>
                            <span class="text-green-600 dark:text-green-400"> -{{ cart.promotion_discount.toFixed(2) }} € </span>
                        </div>

                        <div class="border-t border-gray-200 pt-2 dark:border-gray-700">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900 dark:text-white">Total</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white"> {{ cart.total.toFixed(2) }} € </span>
                            </div>
                        </div>
                    </div>

                    <Link
                        :href="route('checkout.index')"
                        class="mt-6 block w-full rounded-lg bg-green-600 px-4 py-3 text-center text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    >
                        Passer la commande
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
