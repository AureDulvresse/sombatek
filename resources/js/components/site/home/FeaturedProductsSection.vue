<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ProductCard from '../ProductCard.vue';

defineProps({
    products: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="bg-gradient-to-br from-gray-50 to-white py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-4xl font-bold text-gray-900">
                    Produits
                    <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">en vedette</span>
                </h2>
                <p class="mx-auto max-w-2xl text-xl text-gray-600">Nos coups de cœur sélectionnés spécialement pour vous</p>
            </div>

            <div class="overflow-x-auto mb-5">
                <div class="flex space-x-6 pb-4 lg:grid lg:grid-cols-4 lg:gap-8 lg:space-x-0">
                    <ProductCard v-for="(product, index) in products" :key="product.id" :product="product" :index="index" />
                </div>
            </div>

            <div class="w-full text-center">
                <Link
                    :href="route('products.index')"
                    class="flex-1 transform rounded-xl bg-green-600 px-4 py-2 font-medium text-white transition-all duration-300 hover:scale-105 hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Catalogue de produit
                </Link>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    name: 'FeaturedProductsSection',
};
</script>
