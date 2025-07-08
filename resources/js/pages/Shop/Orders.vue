<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

// Les commandes sont passées en props via Inertia
const props = defineProps<{ orders: any[] }>();
const orders = ref(props.orders);
const user = usePage().props.auth.user;

const handleStatus = (orderId: number, status: string) => {
    if (confirm(`Changer le statut de la commande #${orderId} en « ${status} » ?`)) {
        router.post(route('shop.orders.update-status', orderId), { status });
    }
};
</script>

<template>
    <Head title="Commandes boutique" />
    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8">Commandes reçues</h1>
            <div v-if="orders.length === 0" class="text-center text-gray-500 py-12">
                Aucune commande trouvée.
            </div>
            <div v-else>
                <table class="w-full border rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">ID</th>
                            <th class="p-2">Client</th>
                            <th class="p-2">Montant</th>
                            <th class="p-2">Statut</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="o in orders" :key="o.id" class="border-b">
                            <td class="p-2 font-semibold">#{{ o.id }}</td>
                            <td class="p-2">{{ o.customer_name }}</td>
                            <td class="p-2">{{ o.total }} €</td>
                            <td class="p-2">
                                <span :class="{
                                    'text-yellow-600': o.status === 'en attente',
                                    'text-blue-600': o.status === 'en cours',
                                    'text-green-600': o.status === 'livrée',
                                    'text-red-600': o.status === 'annulée',
                                }">{{ o.status }}</span>
                            </td>
                            <td class="p-2 flex gap-2">
                                <button v-if="o.status === 'en attente'" @click="handleStatus(o.id, 'en cours')" class="text-blue-600 hover:underline">Préparer</button>
                                <button v-if="o.status === 'en cours'" @click="handleStatus(o.id, 'livrée')" class="text-green-600 hover:underline">Marquer livrée</button>
                                <button v-if="o.status !== 'annulée' && o.status !== 'livrée'" @click="handleStatus(o.id, 'annulée')" class="text-red-600 hover:underline">Annuler</button>
                                <Link :href="route('shop.orders.show', o.id)" class="text-primary hover:underline">Détail</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template> 