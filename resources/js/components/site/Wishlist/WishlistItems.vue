<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';

interface Product {
    id: number;
    name: string;
    price: number;
    image: string;
    shop: {
        id: number;
        name: string;
    };
}

interface WishlistItem {
    id: number;
    product: Product;
}

const props = defineProps<{
    items: WishlistItem[];
    isFull: boolean;
}>();

const emit = defineEmits<{
    (e: 'remove-item', itemId: number): void;
    (e: 'add-to-cart', productId: number): void;
}>();

const toast = useToast();
const isRemoving = ref(false);
const isAddingToCart = ref(false);

const removeItem = async (itemId: number) => {
    if (isRemoving.value) return;
    isRemoving.value = true;

    try {
        await axios.post(route('wishlists.remove-item'), { item_id: itemId });
        emit('remove-item', itemId);
        toast.success('Article supprimé de la liste');
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de la suppression de l'article");
    } finally {
        isRemoving.value = false;
    }
};

const addToCart = async (productId: number) => {
    if (isAddingToCart.value) return;
    isAddingToCart.value = true;

    try {
        await axios.post(route('cart.add-item'), { product_id: productId, quantity: 1 });
        emit('add-to-cart', productId);
        toast.success('Article ajouté au panier');
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de l'ajout au panier");
    } finally {
        isAddingToCart.value = false;
    }
};
</script>

<template>
    <div class="space-y-4">
        <div v-if="items.length === 0" class="rounded-lg border border-gray-200 bg-white p-6 text-center dark:border-gray-700 dark:bg-gray-800">
            <p class="text-gray-600 dark:text-gray-400">Cette liste est vide</p>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="item in items"
                :key="item.id"
                class="group relative rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="aspect-w-1 aspect-h-1 mb-4 overflow-hidden rounded-lg">
                    <img :src="item.product.image" :alt="item.product.name" class="h-full w-full object-cover object-center" />
                </div>

                <div class="mb-2">
                    <Link
                        :href="route('products.show', item.product.id)"
                        class="text-lg font-medium text-gray-900 hover:text-green-600 dark:text-white dark:hover:text-green-400"
                    >
                        {{ item.product.name }}
                    </Link>
                </div>

                <div class="mb-2">
                    <Link
                        :href="route('shops.show', item.product.shop.id)"
                        class="text-sm text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400"
                    >
                        {{ item.product.shop.name }}
                    </Link>
                </div>

                <div class="mb-4">
                    <span class="text-lg font-bold text-gray-900 dark:text-white"> {{ item.product.price.toFixed(2) }} € </span>
                </div>

                <div class="flex space-x-2">
                    <button
                        @click="addToCart(item.product.id)"
                        :disabled="isAddingToCart"
                        class="flex-1 rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    >
                        <span v-if="isAddingToCart" class="flex items-center justify-center">
                            <svg class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Ajout...
                        </span>
                        <span v-else>Ajouter au panier</span>
                    </button>
                    <button
                        @click="removeItem(item.id)"
                        :disabled="isRemoving"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isFull" class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-900">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-200">La limite d'articles de cette liste a été atteinte.</p>
                </div>
            </div>
        </div>
    </div>
</template>
