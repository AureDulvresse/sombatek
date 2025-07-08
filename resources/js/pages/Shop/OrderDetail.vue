<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{ order: any }>();
const order = ref(props.order);

const handleStatus = (status: string) => {
    if (confirm(`Changer le statut de la commande en « ${status} » ?`)) {
        router.post(route('shop.orders.update-status', order.value.id), { status });
    }
};
</script>

<template>
    <Head :title="`Commande #${order.id}`" />
    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-6">Commande #{{ order.id }}</h1>
            <div class="mb-4">
                <span class="font-semibold">Statut :</span>
                <span :class="{
                    'text-yellow-600': order.status === 'en attente',
                    'text-blue-600': order.status === 'en cours',
                    'text-green-600': order.status === 'livrée',
                    'text-red-600': order.status === 'annulée',
                }">{{ order.status }}</span>
            </div>
            <div class="mb-4">
                <span class="font-semibold">Client :</span> {{ order.customer?.name || 'N/A' }}<br />
                <span class="font-semibold">Email :</span> {{ order.customer?.email || 'N/A' }}
            </div>
            <div class="mb-4">
                <span class="font-semibold">Adresse de livraison :</span><br />
                <span v-if="order.address">{{ order.address.line1 }}, {{ order.address.city }}, {{ order.address.zip }}</span>
                <span v-else>N/A</span>
            </div>
            <div class="mb-4">
                <span class="font-semibold">Produits :</span>
                <ul class="list-disc ml-6">
                    <li v-for="item in order.items" :key="item.id">
                        {{ item.product?.name || 'Produit supprimé' }} x {{ item.quantity }} — {{ item.price }} €
                    </li>
                </ul>
            </div>
            <div class="mb-4 font-bold">Total : {{ order.total }} €</div>
            <div class="flex gap-2 mt-6">
                <button v-if="order.status === 'en attente'" @click="handleStatus('en cours')" class="bg-blue-600 text-white px-4 py-2 rounded">Préparer</button>
                <button v-if="order.status === 'en cours'" @click="handleStatus('livrée')" class="bg-green-600 text-white px-4 py-2 rounded">Marquer livrée</button>
                <button v-if="order.status !== 'annulée' && order.status !== 'livrée'" @click="handleStatus('annulée')" class="bg-red-600 text-white px-4 py-2 rounded">Annuler</button>
                <Link :href="route('shop.orders.index')" class="ml-4 text-primary underline">Retour à la liste</Link>
            </div>
        </div>
    </AppLayout>
</template> 