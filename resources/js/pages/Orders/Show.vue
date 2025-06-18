<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <!-- En-tête -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Commande #{{ order.id }}</h1>
                    <p class="mt-2 text-gray-600">Passée le {{ formatDate(order.created_at) }}</p>
                </div>
                <div class="text-right">
                    <span :class="['inline-block rounded-full px-4 py-1 text-sm font-medium', statusClasses[order.status]]">
                        {{ order.status }}
                    </span>
                </div>
            </div>

            <!-- Suivi de commande -->
            <div class="mb-8 rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-6 text-xl font-bold">Suivi de commande</h2>
                <div class="relative">
                    <div class="absolute top-0 left-4 h-full w-0.5 bg-gray-200"></div>
                    <div class="space-y-8">
                        <div v-for="(step, index) in trackingSteps" :key="index" class="relative flex items-start">
                            <div
                                :class="[
                                    'z-10 flex h-8 w-8 items-center justify-center rounded-full',
                                    index <= currentStepIndex ? 'bg-primary text-white' : 'bg-gray-200',
                                ]"
                            >
                                <svg v-if="index < currentStepIndex" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span v-else>{{ index + 1 }}</span>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium">{{ step.title }}</p>
                                <p class="text-sm text-gray-500">{{ step.description }}</p>
                                <p v-if="step.date" class="mt-1 text-sm text-gray-500">{{ formatDate(step.date) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Détails de la commande -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-xl font-bold">Détails de la commande</h2>
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
                        <h2 class="mb-4 text-xl font-bold">Informations de paiement</h2>
                        <div class="space-y-2">
                            <p class="font-medium">Méthode de paiement</p>
                            <p>{{ order.payment.method }}</p>
                            <p class="text-sm text-gray-500">Transaction #{{ order.payment.transaction_id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end gap-4">
                <button @click="downloadInvoice" class="rounded-lg border border-primary px-6 py-2 text-primary hover:bg-primary hover:text-white">
                    Télécharger la facture
                </button>
                <button v-if="canCancel" @click="cancelOrder" class="rounded-lg bg-red-500 px-6 py-2 text-white hover:bg-red-600">
                    Annuler la commande
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    order: {
        id: number;
        created_at: string;
        status: string;
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
        payment: {
            method: string;
            transaction_id: string;
        };
    };
}>();

const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const trackingSteps = [
    {
        title: 'Commande confirmée',
        description: 'Votre commande a été reçue',
        date: props.order.created_at,
    },
    {
        title: 'En préparation',
        description: 'Votre commande est en cours de préparation',
        date: props.order.status === 'processing' ? new Date().toISOString() : null,
    },
    {
        title: 'Expédiée',
        description: 'Votre commande a été expédiée',
        date: props.order.status === 'shipped' ? new Date().toISOString() : null,
    },
    {
        title: 'Livrée',
        description: 'Votre commande a été livrée',
        date: props.order.status === 'delivered' ? new Date().toISOString() : null,
    },
];

const currentStepIndex = computed(() => {
    const statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
    return statusOrder.indexOf(props.order.status);
});

const canCancel = computed(() => {
    return ['pending', 'processing'].includes(props.order.status);
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const downloadInvoice = () => {
    window.open(route('orders.invoice', props.order.id), '_blank');
};

const cancelOrder = () => {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        router.put(route('orders.cancel', props.order.id));
    }
};
</script>
