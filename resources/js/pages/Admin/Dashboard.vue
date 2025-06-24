<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  CategoryScale,
  PointElement
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps<{
    stats: {
        users: number;
        shops: number;
        products: number;
        orders: number;
        revenue: number;
        pendingShops: number; // Boutiques en attente de validation
        lowStockProducts: number; // Produits en stock faible
        monthlyStats: {
            labels: string[];
            orders: number[];
            revenue: number[];
        };
    };
    recentUsers: Array<any>;
    recentShops: Array<any>;
    recentOrders: Array<any>;
    alerts: Array<{
        type: string;
        message: string;
        action?: string;
        link?: string;
    }>;
}>();

const chartData = {
  labels: props.stats.monthlyStats.labels,
  datasets: [
    {
      label: 'Commandes',
      data: props.stats.monthlyStats.orders,
      borderColor: '#4F46E5',
      tension: 0.4
    },
    {
      label: 'Chiffre d\'affaires',
      data: props.stats.monthlyStats.revenue,
      borderColor: '#10B981',
      tension: 0.4
    }
  ]
}

const chartOptions = {
  responsive: true,
  interaction: {
    intersect: false,
    mode: 'index'
  }
}
</script>

<template>
    <Head title="Admin - Dashboard" />
    <AppLayout>
        <div class="container mx-auto py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Tableau de bord administrateur</h1>
                <!-- Actions rapides -->
                <div class="flex gap-4">
                    <Link
                        :href="route('shops.pending')"
                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark"
                    >
                        Boutiques en attente ({{ stats.pendingShops }})
                    </Link>
                    <Link
                        :href="route('products.low-stock')"
                        class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
                    >
                        Stock faible ({{ stats.lowStockProducts }})
                    </Link>
                </div>
            </div>

            <!-- Alertes -->
            <div v-if="alerts && alerts.length" class="mb-8">
                <div v-for="(alert, index) in alerts" :key="index"
                    class="mb-4 p-4 rounded-lg"
                    :class="{
                        'bg-red-100 text-red-700': alert.type === 'error',
                        'bg-yellow-100 text-yellow-700': alert.type === 'warning',
                        'bg-blue-100 text-blue-700': alert.type === 'info'
                    }"
                >
                    <div class="flex justify-between items-center">
                        <p>{{ alert.message }}</p>
                        <Link v-if="alert.link" :href="alert.link" class="underline">
                            {{ alert.action }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-5 mb-8">
                <div class="rounded-lg bg-white p-6 shadow text-center">
                    <div class="text-2xl font-bold">{{ stats.users }}</div>
                    <div class="text-gray-500">Utilisateurs</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow text-center">
                    <div class="text-2xl font-bold">{{ stats.shops }}</div>
                    <div class="text-gray-500">Boutiques</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow text-center">
                    <div class="text-2xl font-bold">{{ stats.products }}</div>
                    <div class="text-gray-500">Produits</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow text-center">
                    <div class="text-2xl font-bold">{{ stats.orders }}</div>
                    <div class="text-gray-500">Commandes</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow text-center">
                    <div class="text-2xl font-bold">{{ stats.revenue.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }) }}</div>
                    <div class="text-gray-500">Chiffre d'affaires</div>
                </div>
            </div>

            <!-- Graphique -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-lg font-bold mb-4">Évolution des ventes</h2>
                <Line :data="chartData" :options="chartOptions" />
            </div>

            <!-- Listes récentes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold">Derniers utilisateurs</h2>
                        <Link :href="route('users.index')" class="text-primary hover:underline">Voir tout</Link>
                    </div>
                    <ul>
                        <li v-for="user in recentUsers" :key="user.id" class="mb-2">
                            {{ user.name }} <span class="text-gray-400">({{ user.email }})</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold">Dernières boutiques</h2>
                        <Link :href="route('shops.index')" class="text-primary hover:underline">Voir tout</Link>
                    </div>
                    <ul>
                        <li v-for="shop in recentShops" :key="shop.id" class="mb-2">
                            {{ shop.name }} <span class="text-gray-400">({{ shop.email }})</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold">Dernières commandes</h2>
                        <Link :href="route('orders.index')" class="text-primary hover:underline">Voir tout</Link>
                    </div>
                    <ul>
                        <li v-for="order in recentOrders" :key="order.id" class="mb-2">
                            #{{ order.id }} - {{ order.total }} € - {{ order.status }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
