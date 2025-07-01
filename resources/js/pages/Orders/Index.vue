<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Order {
    id: number;
    total: number;
    status: string;
    created_at: string;
    items_count: number;
    shop?: {
        name: string;
    };
}

interface Props {
    orders: {
        data: Order[];
        links: any[];
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Commandes',
        href: '/orders',
    },
];

const search = ref('');
const statusFilter = ref('');

const filteredOrders = computed(() => {
    let result = props.orders.data;

    if (search.value.trim()) {
        const searchTerm = search.value.toLowerCase().trim();
        result = result.filter((order: Order) =>
            order.id.toString().includes(searchTerm) ||
            order.shop?.name.toLowerCase().includes(searchTerm)
        );
    }

    if (statusFilter.value) {
        result = result.filter((order: Order) => order.status === statusFilter.value);
    }

    return result;
});

const getStatusClass = (status: string) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
    const labels = {
        pending: 'En attente',
        processing: 'En cours',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée',
    };
    return labels[status as keyof typeof labels] || status;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>

    <Head title="Mes commandes" />
    <SiteLayout title="Mes commandes">
        <PageBanner title="Mes commandes"
            description="Suivez l'état de vos commandes et consultez votre historique d'achats"
            :breadcrumbs="breadcrumbs" />

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header avec filtres -->
            <div class="mb-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Mes Commandes
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ filteredOrders.length }} commande{{ filteredOrders.length > 1 ? 's' : '' }} trouvée{{
                                filteredOrders.length > 1 ? 's' : '' }}
                        </p>
                    </div>

                    <!-- Filtres -->
                    <div class="flex gap-4">
                        <div class="relative">
                            <input v-model="search" type="text" placeholder="Rechercher une commande..."
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 pl-10 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                            <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <select v-model="statusFilter"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                            <option value="">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="processing">En cours</option>
                            <option value="shipped">Expédiée</option>
                            <option value="delivered">Livrée</option>
                            <option value="cancelled">Annulée</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des commandes -->
            <div v-if="filteredOrders.length === 0" class="py-12 text-center">
                <div class="mx-auto max-w-md">
                    <div class="mb-4 text-6xl">📦</div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                        Aucune commande trouvée
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Vous n'avez pas encore passé de commande
                    </p>
                    <Link :href="route('products.index')"
                        class="mt-4 inline-block rounded-lg bg-primary px-6 py-2 text-white hover:bg-primary-dark">
                    Découvrir nos produits
                    </Link>
                </div>
            </div>

            <div v-else class="space-y-4">
                <div v-for="order in filteredOrders" :key="order.id"
                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Commande #{{ order.id }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Passée le {{ formatDate(order.created_at) }}
                                    </p>
                                    <p v-if="order.shop" class="text-sm text-gray-500">
                                        Boutique : {{ order.shop.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ order.total.toFixed(2) }} €
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ order.items_count }} article{{ order.items_count > 1 ? 's' : '' }}
                                </p>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <span :class="[
                                    'inline-flex rounded-full px-3 py-1 text-xs font-medium',
                                    getStatusClass(order.status)
                                ]">
                                    {{ getStatusLabel(order.status) }}
                                </span>

                                <Link :href="route('orders.show', order.id)"
                                    class="text-sm text-primary hover:text-primary-dark">
                                Voir les détails
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="props.orders.links && props.orders.links.length > 1" class="mt-12 flex justify-center">
                <nav class="flex items-center gap-2">
                    <Link v-for="link in props.orders.links" :key="link.label" :href="link.url || '#'" :class="[
                        'px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                        link.active
                            ? 'bg-primary text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'
                    ]">
                    {{ link.label.replace(/(<([^>]+)>)/gi, '') }}
                        </Link>
                </nav>
            </div>
        </div>
    </SiteLayout>
</template>