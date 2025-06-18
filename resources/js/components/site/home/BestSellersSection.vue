<script setup lang="ts">
import { computed, ref } from 'vue';
import ProductCard from '../ProductCard.vue';

interface Product {
    id: number;
    category?: {
        slug: string;
    };
}

const props = withDefaults(
    defineProps<{
        products?: Array<Product>;
    }>(),
    {
        products: () => [],
    },
);

const activeTab = ref('all');

const tabs = [
    { id: 'all', name: 'Tous les produits' },
    { id: 'electronics', name: 'Électronique' },
    { id: 'fashion', name: 'Mode' },
    { id: 'home', name: 'Maison' },
    { id: 'beauty', name: 'Beauté' },
];

const filteredProducts = computed(() => {
    if (activeTab.value === 'all') {
        return props.products;
    }
    return props.products.filter((product: Product) => product.category?.slug === activeTab.value);
});
</script>

<template>
    <section class="bg-white py-16 dark:bg-gray-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">Meilleures Ventes</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Découvrez nos produits les plus populaires</p>
            </div>

            <!-- Filtres -->
            <div class="mb-8 flex flex-wrap justify-center gap-4">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="rounded-full px-4 py-2 text-sm font-medium transition-all duration-300"
                    :class="[
                        activeTab === tab.id
                            ? 'bg-emerald-600 text-white shadow-lg'
                            : 'bg-white text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700',
                    ]"
                >
                    {{ tab.name }}
                </button>
            </div>

            <!-- Grille de produits -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="(product, index) in filteredProducts"
                    :key="product.id"
                    class="animate-fade-in-up"
                    :style="{ animationDelay: `${index * 150}ms` }"
                >
                    <ProductCard :product="product" :index="index" />
                </div>
            </div>

            <!-- Statistiques -->
            <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 p-6 dark:from-emerald-900/20 dark:to-teal-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Satisfaction Client</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">98%</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 p-3 dark:bg-emerald-900/30">
                            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 p-6 dark:from-blue-900/20 dark:to-indigo-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Produits Vendus</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">10k+</p>
                        </div>
                        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 p-6 dark:from-purple-900/20 dark:to-pink-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Support Client</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">24/7</p>
                        </div>
                        <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/30">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 p-6 dark:from-amber-900/20 dark:to-orange-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Garantie Qualité</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">100%</p>
                        </div>
                        <div class="rounded-full bg-amber-100 p-3 dark:bg-amber-900/30">
                            <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton Voir plus -->
            <div class="mt-12 text-center">
                <a
                    href="#"
                    class="inline-flex items-center rounded-lg border border-transparent bg-emerald-600 px-6 py-3 text-base font-medium text-white transition-colors hover:bg-emerald-700"
                >
                    Voir tous les meilleurs vendeurs
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script lang="ts">
export default {
    name: 'BestSellersSection',
};
</script>
