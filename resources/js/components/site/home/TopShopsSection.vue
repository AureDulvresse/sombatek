<script setup lang="ts">
import { CheckBadgeIcon, StarIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';

defineProps<{
    topShops: Array<any>;
}>();

const getRatingColor = (rating: number) => {
    if (rating >= 4.5) return 'text-yellow-400';
    if (rating >= 4) return 'text-yellow-500';
    if (rating >= 3.5) return 'text-yellow-600';
    return 'text-gray-400';
};
</script>

<script lang="ts">
export default {
    name: 'TopShopsSection',
};
</script>

<template>
    <section class="px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <!-- En-tête de section -->
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Nos Meilleures Boutiques</h2>
                <p class="mt-4 text-lg text-gray-600">Découvrez nos boutiques partenaires les mieux notées</p>
            </div>

            <!-- Grille de boutiques -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(shop, index) in topShops" :key="shop.id"
                    class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <!-- Badge de position -->
                    <div
                        class="absolute top-6 -right-12 rotate-45 transform bg-gradient-to-r from-green-500 to-green-600 px-12 py-1 text-center text-sm font-medium text-white">
                        #{{ index + 1 }}
                    </div>

                    <!-- En-tête de la boutique -->
                    <div class="mb-4 flex items-center gap-4">
                        <div class="relative">
                            <img :src="shop.logo || '/images/shop-placeholder.jpg'" :alt="shop.name"
                                class="h-16 w-16 rounded-xl object-cover" />
                            <div v-if="shop.is_verified" class="absolute -top-1 -right-1 rounded-full bg-green-500 p-1">
                                <CheckBadgeIcon class="h-4 w-4 text-white" />
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ shop.name }}
                            </h3>
                            <div class="flex items-center gap-1">
                                <div class="flex">
                                    <StarIcon v-for="i in 5" :key="i" class="h-4 w-4"
                                        :class="getRatingColor(shop.rating)" />
                                </div>
                                <span class="text-sm text-gray-600">({{ shop.reviews_count }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="mb-4 grid grid-cols-3 gap-4 border-t border-gray-100 pt-4">
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-900">{{ shop.products_count }}</div>
                            <div class="text-xs text-gray-500">Produits</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-900">{{ shop.sales_count }}</div>
                            <div class="text-xs text-gray-500">Ventes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-900">{{ shop.followers_count }}</div>
                            <div class="text-xs text-gray-500">Abonnés</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="mb-4 line-clamp-2 text-sm text-gray-600">
                        {{ shop.description }}
                    </p>

                    <!-- Bouton -->
                    <Link :href="route('shops.show', shop.slug || shop.id)"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200">
                    Voir la boutique
                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    </Link>
                </div>
            </div>

            <!-- Bouton Voir plus -->
            <div class="mt-12 text-center">
                <Link :href="route('shops.index')"
                    class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-6 py-3 text-base font-medium text-white transition-colors hover:bg-green-700">
                Voir toutes les boutiques
                <svg class="-mr-1 ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
