<script setup lang="ts">
import { useWishlistStore } from '@/stores/wishlist';
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import type { Component } from 'vue';
import { markRaw, ref } from 'vue';
import { useToast } from 'vue-toastification';

interface WishlistItem {
    id: number;
    product_id: number;
    name: string;
    default_image: string;
    price: number;
    sale_price: number | null;
    formatted_price: string;
    formatted_sale_price: string | null;
    isOnSale: boolean;
    discount_percentage: number;
    stock: number;
}

defineProps<{
    isOpen: boolean;
    wishlists: WishlistItem[];
    total: number;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'update'): void;
}>();

const toast = useToast();
const isRemoving = ref(false);
const isAddingToCart = ref(false);
const isClearing = ref(false);

const wishlistStore = useWishlistStore();

const formatPrice = (price: number) => {
    if (isNaN(price) || price === null || price === undefined) {
        return '0,00 €';
    }
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(price);
};

const removeItem = async (itemId: number) => {
    if (isRemoving.value) return;
    isRemoving.value = true;

    try {
        await wishlistStore.removeItem(itemId);
        window.dispatchEvent(new CustomEvent('wishlist-updated'));
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de la suppression de l'article");
    } finally {
        isRemoving.value = false;
    }
};

const addToCart = async (item: WishlistItem) => {
    if (isAddingToCart.value) return;
    isAddingToCart.value = true;

    try {
        const response = await axios.post(route('cart.add-item'), {
            product_id: item.product_id,
            quantity: 1,
        });

        if (response.data.success) {
            emit('update');
            toast.success('Produit ajouté au panier');
        }
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de l'ajout au panier");
    } finally {
        isAddingToCart.value = false;
    }
};

const clearWishlist = async () => {
    if (isClearing.value) return;
    isClearing.value = true;

    try {
        await wishlistStore.clear();
        window.dispatchEvent(new CustomEvent('wishlist-updated'));
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la suppression de la wishlist');
    } finally {
        isClearing.value = false;
    }
};

const TransitionRootComponent = markRaw(TransitionRoot);
</script>

<template>
    <TransitionRootComponent as="template" :show="isOpen">
        <Dialog as="div" class="relative z-50" @close="emit('close')">
            <TransitionChild
                as="template"
                enter="ease-in-out duration-500"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in-out duration-500"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-500"
                            enter-from="translate-x-full"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500"
                            leave-from="translate-x-0"
                            leave-to="translate-x-full"
                        >
                            <DialogPanel class="pointer-events-auto w-screen max-w-md">
                                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl dark:bg-gray-900">
                                    <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                                        <div class="flex items-start justify-between">
                                            <Dialog.Title class="text-lg font-medium text-gray-900 dark:text-white">Mes favoris</Dialog.Title>
                                            <div class="ml-3 flex h-7 items-center">
                                                <button
                                                    type="button"
                                                    class="relative -m-2 p-2 text-gray-400 hover:text-gray-500"
                                                    @click="emit('close')"
                                                >
                                                    <span class="absolute -inset-0.5" />
                                                    <span class="sr-only">Fermer le panneau</span>
                                                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-8">
                                            <div v-if="wishlists.length === 0" class="text-center">
                                                <p class="text-gray-500 dark:text-gray-400">Votre liste de favoris est vide</p>
                                                <Link
                                                    :href="route('products.index')"
                                                    class="mt-4 inline-block rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                                                >
                                                    Découvrir des produits
                                                </Link>
                                            </div>

                                            <div v-else class="flow-root">
                                                <ul role="list" class="-my-6 divide-y divide-gray-200 dark:divide-gray-700">
                                                    <li v-for="item in wishlists" :key="item.id" class="flex py-6">
                                                        <div
                                                            class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-gray-700"
                                                        >
                                                            <img
                                                                :src="item.default_image"
                                                                :alt="item.name"
                                                                class="h-full w-full object-cover object-center"
                                                            />
                                                        </div>

                                                        <div class="ml-4 flex flex-1 flex-col">
                                                            <div>
                                                                <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                                                    <h3>
                                                                        <Link
                                                                            :href="route('products.show', item.product_id)"
                                                                            class="hover:text-emerald-600"
                                                                        >
                                                                            {{ item.name }}
                                                                        </Link>
                                                                    </h3>
                                                                    <p class="ml-4">
                                                                        <span v-if="item.isOnSale" class="text-red-600">
                                                                            {{ item.formatted_sale_price }}
                                                                        </span>
                                                                        <span v-else>{{ item.formatted_price }}</span>
                                                                    </p>
                                                                </div>
                                                                <p
                                                                    v-if="item.isOnSale"
                                                                    class="mt-1 text-sm text-gray-500 line-through dark:text-gray-400"
                                                                >
                                                                    {{ item.formatted_price }}
                                                                </p>
                                                            </div>
                                                            <div class="flex flex-1 items-end justify-between text-sm">
                                                                <div class="flex items-center space-x-4">
                                                                    <button
                                                                        @click="addToCart(item)"
                                                                        :disabled="isAddingToCart || item.stock <= 0"
                                                                        class="rounded-lg bg-emerald-600 px-3 py-1 text-sm font-medium text-white transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                                                    >
                                                                        <span v-if="isAddingToCart">Ajout en cours...</span>
                                                                        <span v-else-if="item.stock <= 0">Rupture de stock</span>
                                                                        <span v-else>Ajouter au panier</span>
                                                                    </button>
                                                                    <button
                                                                        v-if="wishlists.length > 0"
                                                                        @click="clearWishlist"
                                                                        :disabled="isClearing"
                                                                        class="rounded-lg bg-red-600 px-3 py-1 text-sm font-medium text-white transition-colors hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                                                    >
                                                                        <span v-if="isClearing">Suppression...</span>
                                                                        <span v-else>Vider la liste</span>
                                                                    </button>
                                                                </div>

                                                                <button
                                                                    type="button"
                                                                    @click="removeItem(item.id)"
                                                                    :disabled="isRemoving"
                                                                    class="font-medium text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                                                                >
                                                                    Supprimer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 px-4 py-6 sm:px-6 dark:border-gray-700">
                                        <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                            <p>Total</p>
                                            <p>{{ formatPrice(total) }}</p>
                                        </div>
                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Frais de livraison calculés à la caisse.</p>
                                        <div class="mt-6">
                                            <Link
                                                :href="route('wishlist.index')"
                                                class="flex items-center justify-center rounded-md border border-transparent bg-green-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-green-700"
                                            >
                                                Voir tous mes favoris
                                            </Link>
                                        </div>
                                        <div class="mt-6 flex justify-center text-center text-sm text-gray-500 dark:text-gray-400">
                                            <p>
                                                ou
                                                <button type="button" class="font-medium text-green-600 hover:text-green-500" @click="emit('close')">
                                                    Continuer mes achats
                                                    <span aria-hidden="true"> &rarr;</span>
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRootComponent>
</template>

<script lang="ts">
export default {
    name: 'WishlistSheet',
} as Component;
</script>
