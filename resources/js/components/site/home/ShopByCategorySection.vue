<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    categories: Array<any>;
}>();
</script>

<template>
    <section class="bg-white py-16 dark:bg-gray-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-8 text-3xl font-bold text-gray-900 dark:text-white">Explorer par Catégorie</h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 p-6 transition-all duration-300 hover:shadow-lg dark:from-gray-800 dark:to-gray-700"
                >
                    <Link :href="route('category.show', category.id)" class="block">
                        <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">{{ category.name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ category.products_count }} produits</p>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script lang="ts">
export default {
    name: 'ShopByCategorySection',
};
</script>
