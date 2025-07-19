<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mb-8 rounded-full bg-green-100 p-4">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="mb-4 text-3xl font-bold">Commande confirmée !</h1>
            <p class="mb-8 text-gray-600">Merci pour votre commande. Votre numéro de commande est #{{ order.id }}</p>
        </div>

        <div class="mx-auto max-w-4xl">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Détails de la commande -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold">Détails de la commande</h2>
                    <div class="space-y-4">
                        <div v-for="item in order.items" :key="item.id" class="flex items-center gap-4">
                            <img :src="item.product.image" :alt="item.product.name" class="h-16 w-16 rounded-lg object-cover" />
                            <div class="flex-1">
                                <h3 class="font-medium">{{ item.product.name }}</h3>
                                <p class="text-sm text-gray-500">Quantité: {{ item.quantity }}</p>
                            </div>
                            <p class="font-medium">{{ (item.product.price * item.quantity).toFixed(2) }} €</p>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between py-2">
                                <span>Sous-total</span>
                                <span>{{ order.subtotal }} €</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Livraison</span>
                                <span>Gratuite</span>
                            </div>
                            <div class="flex justify-between border-t pt-2 font-bold">
                                <span>Total</span>
                                <span>{{ order.total }} €</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de livraison -->
                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold">Adresse de livraison</h2>
                        <div class="space-y-2">
                            <p class="font-medium">{{ order.shipping.firstName }} {{ order.shipping.lastName }}</p>
                            <p>{{ order.shipping.address }}</p>
                            <p>{{ order.shipping.postalCode }} {{ order.shipping.city }}</p>
                            <p>{{ order.shipping.country }}</p>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold">Statut de la commande</h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="rounded-full bg-primary p-2">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">Commande confirmée</p>
                                    <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="rounded-full bg-gray-200 p-2">
                                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">En préparation</p>
                                    <p class="text-sm text-gray-500">En attente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <Link :href="route('orders.show', order.id)" class="hover:bg-primary-dark inline-block rounded-lg bg-primary px-6 py-3 text-white">
                    Suivre ma commande
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    order: {
        id: number;
        created_at: string;
        subtotal: number;
        total: number;
        items: Array<{
            id: number;
            quantity: number;
            product: {
                id: number;
                name: string;
                price: number;
                image: string;
            };
        }>;
        shipping: {
            firstName: string;
            lastName: string;
            address: string;
            postalCode: string;
            city: string;
            country: string;
        };
    };
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
