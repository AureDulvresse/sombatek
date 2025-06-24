<script setup lang="ts">
import { computed, ref } from 'vue';
import ProductCard from '../ProductCard.vue';
import { Link } from '@inertiajs/vue3';

interface Product {
    id: number;
    category?: {
        slug: string;
    };
    is_seasonal?: boolean;
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
    { id: 'all', name: 'Toutes les offres' },
    { id: 'electronics', name: 'Électronique' },
    { id: 'fashion', name: 'Mode' },
    { id: 'home', name: 'Maison' },
    { id: 'beauty', name: 'Beauté' },
];

const filteredProducts = computed(() => {
    if (activeTab.value === 'all') {
        return props.products.filter((product: Product) => product.is_seasonal);
    }
    return props.products.filter((product: Product) => product.is_seasonal && product.category?.slug === activeTab.value);
});
</script>

<template>
    <section class="bg-white py-16 dark:bg-gray-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- En-tête avec bannière promotionnelle -->
            <div class="mb-12 overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-8 text-white shadow-xl">
                <div class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl font-bold sm:text-4xl">Offres Saisonnières</h2>
                        <p class="mt-2 text-lg text-emerald-100">Profitez des meilleures offres du moment</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="rounded-full bg-white/20 p-4 backdrop-blur-sm">
                            <span class="text-2xl font-bold">-50%</span>
                        </div>
                        <div class="rounded-full bg-white/20 p-4 backdrop-blur-sm">
                            <span class="text-2xl font-bold">-30%</span>
                        </div>
                    </div>
                </div>
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

            <!-- Bouton Voir plus -->
            <div class="mt-12 text-center">
                <Link
                    :href="route('products.index')"
                    class="inline-flex items-center rounded-lg border border-transparent bg-emerald-600 px-6 py-3 text-base font-medium text-white transition-colors hover:bg-emerald-700"
                >
                    Voir toutes les offres saisonnières
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </Link>
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
    name: 'SeasonalDealsSection',
};
</script>
