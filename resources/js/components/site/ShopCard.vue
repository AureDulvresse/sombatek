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
    <div class="group relative w-full overflow-hidden rounded-2xl bg-white/90 backdrop-blur-sm border border-gray-100/80 shadow-sm transition-all duration-500 ease-out hover:shadow-2xl hover:shadow-gray-900/10 hover:-translate-y-2 hover:border-gray-200/80 dark:bg-gray-800/90 dark:border-gray-700/80 dark:hover:border-gray-600/80"
        :style="{ animationDelay: `${index * 100}ms` }">
        <!-- Image -->
        <div class="relative aspect-[4/3] overflow-hidden bg-gray-50 dark:bg-gray-900">
            <img :src="shop.image" :alt="shop.name"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        </div>

        <!-- Content -->
        <div class="p-5">
            <!-- Title and rating -->
            <div class="mb-3 flex items-center justify-between">
                <h3
                    class="text-lg font-semibold text-gray-900 transition-colors duration-300 group-hover:text-emerald-600 dark:text-gray-100 dark:group-hover:text-emerald-400">
                    <Link :href="route('shops.show', shop.slug || shop.id)" class="hover:underline">
                    {{ shop.name }}
                    </Link>
                </h3>
                <div class="flex items-center rounded-full bg-yellow-100 px-3 py-1">
                    <Star class="mr-1 h-4 w-4 text-yellow-500" />
                    <span class="text-sm font-medium text-yellow-800">{{ shop.rating }}</span>
                </div>
            </div>

            <!-- Description -->
            <p class="mb-4 line-clamp-2 text-gray-600 dark:text-gray-400 min-h-[2.5em]">{{ shop.description }}</p>

            <!-- Main CTA Button -->
            <Link :href="route('shops.show', shop.slug || shop.id)"
                class="mt-2 block w-full rounded-xl border-2 border-emerald-600 bg-transparent px-6 py-3 text-center text-base font-semibold text-emerald-600 transition-all duration-300 hover:bg-emerald-600 hover:text-white">
            Visiter la boutique
            <svg class="ml-2 h-4 w-4 inline-block transition-transform duration-300 group-hover:translate-x-1"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
            </Link>
        </div>
    </div>
</template>
