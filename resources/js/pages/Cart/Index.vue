<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import { Button } from '@/components/ui/button';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { useCartStore } from '@/stores/cart';
import { BreadcrumbItem } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

interface CartItem {
    id: number;
    product_id: number;
    name: string;
    default_image: string;
    price: number;
    quantity: number;
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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Panier',
        href: '/cart',
    },
];

const props = defineProps<{
    isOpen: boolean;
    cart: Cart;
}>();

const emit = defineEmits<{
    (e: 'update'): void;
}>();

const toast = useToast();
const cartStore = useCartStore();
const isUpdating = ref(false);
const isRemoving = ref(false);
const promotionCode = ref('');

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

const calculateSubtotal = () => {
    if (!props.cart?.items) return 0;
    return props.cart.items.reduce((total: number, item: CartItem) => {
        const itemTotal = item.price * item.quantity;
        return total + (isNaN(itemTotal) ? 0 : itemTotal);
    }, 0);
};

const calculateTotal = () => {
    const subtotal = calculateSubtotal();
    const discount = props.cart?.discount_total || 0;
    const promotionDiscount = props.cart?.promotion_discount || 0;
    return subtotal - discount - promotionDiscount;
};

const updateQuantity = async (itemId: number, quantity: number) => {
    if (isUpdating.value) return;
    isUpdating.value = true;

    try {
        await cartStore.updateQuantity(itemId, quantity);
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
        await cartStore.removeItem(itemId);
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de la suppression de l'article");
    } finally {
        isRemoving.value = false;
    }
};

const applyPromotion = async () => {
    if (!promotionCode.value) {
        toast.error('Veuillez entrer un code promotion');
        return;
    }

    try {
        const response = await axios.post(route('cart.apply-promotion'), {
            code: promotionCode.value,
        });

        if (response.data.success) {
            toast.success(response.data.message);
            emit('update');
        }
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de l'application du code promotion");
    }
};

const removePromotion = async () => {
    try {
        const response = await axios.post(route('cart.remove-promotion'));

        if (response.data.success) {
            toast.success(response.data.message);
            emit('update');
        }
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la suppression du code promotion');
    }
};

const proceedToCheckout = () => {
    router.visit(route('checkout'));
};

</script>

<template>
    <SiteLayout title="Mon panier">
        <!-- Bannière de page -->
        <PageBanner title="Mon panier" description="Découvrez notre sélection de produits de qualité"
            :breadcrumbs="breadcrumbs" />

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div v-if="cartStore.items.length === 0" class="text-center">
                <p class="text-gray-500 dark:text-gray-400">Votre panier est vide</p>
                <Link :href="route('products.index')"
                    class="mt-4 inline-block rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                Continuer mes achats
                </Link>
            </div>

            <div v-else class="flow-root">
                <ul role="list" class="-my-6 divide-y divide-gray-200 dark:divide-gray-700">
                    <li v-for="item in cartStore.items" :key="item.id" class="flex py-6">
                        <div
                            class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-gray-700">
                            <img :src="item.default_image" :alt="item.name"
                                class="h-full w-full object-cover object-center" />
                        </div>

                        <div class="ml-4 flex flex-1 flex-col">
                            <div>
                                <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                    <h3>{{ item.name }}</h3>
                                    <p class="ml-4">{{ formatPrice(item.price * item.quantity) }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatPrice(item.price) }} l'unité
                                </p>
                            </div>
                            <div class="flex flex-1 items-end justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <button @click="updateQuantity(item.id, item.quantity - 1)"
                                        :disabled="isUpdating || item.quantity <= 1"
                                        class="rounded-lg border border-gray-300 bg-white p-1 text-gray-500 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center text-gray-900 dark:text-white">
                                        {{ item.quantity }}
                                    </span>
                                    <button @click="updateQuantity(item.id, item.quantity + 1)" :disabled="isUpdating"
                                        class="rounded-lg border border-gray-300 bg-white p-1 text-gray-500 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <button type="button" @click="removeItem(item.id)" :disabled="isRemoving"
                                    class="font-medium text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-6 sm:px-6 dark:border-gray-700">
            <!-- Code promotion -->
            <div v-if="!cartStore.promotion_code" class="mb-4">
                <div class="flex">
                    <input type="text" v-model="promotionCode" placeholder="Code promotion"
                        class="block w-full rounded-l-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500" />
                    <button @click="applyPromotion"
                        class="rounded-r-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        Appliquer
                    </button>
                </div>
            </div>

            <div v-else class="mb-4">
                <div class="flex items-center justify-between rounded-lg bg-green-50 p-3 dark:bg-green-900">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                Code appliqué : {{ cartStore.promotion_code }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400">
                                Réduction : {{ formatPrice(cartStore.promotion_discount) }}
                            </p>
                        </div>
                    </div>
                    <button @click="removePromotion"
                        class="rounded-full p-1 text-green-600 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                    <p>Sous-total</p>
                    <p>{{ formatPrice(calculateSubtotal()) }}</p>
                </div>
                <div v-if="cartStore.discount_total > 0"
                    class="flex justify-between text-sm text-green-600 dark:text-green-400">
                    <p>Réduction</p>
                    <p>-{{ formatPrice(cartStore.discount_total) }}</p>
                </div>
                <div v-if="cartStore.promotion_discount > 0"
                    class="flex justify-between text-sm text-green-600 dark:text-green-400">
                    <p>Code promo ({{ cartStore.promotion_code }})</p>
                    <p>-{{ formatPrice(cartStore.promotion_discount) }}</p>
                </div>
                <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                    <p>Total</p>
                    <p>{{ formatPrice(calculateTotal()) }}</p>
                </div>
            </div>

            <div class="mt-6">
                <Button @click="proceedToCheckout()"
                    class="flex items-center justify-center rounded-md border border-transparent bg-green-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-green-700">
                    Proceder au checkout
                </Button>
            </div>

        </div>
    </SiteLayout>
</template>