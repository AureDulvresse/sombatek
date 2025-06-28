<script setup lang="ts">
import { Star } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Shop {
    id: number;
    name: string;
    slug: string;
    image: string;
    description: string;
    rating: number;
}

defineProps<{
    shop: Shop;
    index: number;
}>();
</script>

<script lang="ts">
export default {
    name: 'ShopCard',
};
</script>

<template>
    <div class="group relative transform overflow-hidden rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 transition-all duration-500 hover:-translate-y-2 hover:border-green-300 hover:shadow-xl"
        :style="{ animationDelay: `${index * 200}ms` }">
        <div class="aspect-[4/3] overflow-hidden">
            <img :src="shop.image" :alt="shop.name"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        </div>

        <div class="p-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 transition-colors duration-300 group-hover:text-green-600">
                    <Link :href="route('shops.show', shop.slug)">
                    {{ shop.name }}
                    </Link>
                </h3>
                <div class="flex items-center rounded-full bg-yellow-100 px-3 py-1">
                    <Star class="mr-1 h-4 w-4 text-yellow-500" />
                    <span class="text-sm font-medium text-yellow-800">{{ shop.rating }}</span>
                </div>
            </div>

            <p class="mb-4 text-gray-600">{{ shop.description }}</p>

            <Link :href="route('shops.show', shop.slug)"
                class="inline-flex items-center font-semibold text-green-600 transition-colors duration-300 group-hover:text-green-700">
            Visiter la boutique
            <svg class="ml-2 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
            </Link>
        </div>
    </div>
</template>
