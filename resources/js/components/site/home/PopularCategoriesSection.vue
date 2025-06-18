<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const popularCategories = [
    {
        id: 1,
        name: 'Mode',
        icon: 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
        color: 'from-pink-500 to-rose-500',
    },
    {
        id: 2,
        name: 'Électronique',
        icon: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z',
        color: 'from-blue-500 to-indigo-500',
    },
    {
        id: 3,
        name: 'Maison',
        icon: 'M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z',
        color: 'from-green-500 to-emerald-500',
    },
    {
        id: 4,
        name: 'Beauté',
        icon: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z',
        color: 'from-purple-500 to-violet-500',
    },
    {
        id: 5,
        name: 'Sport',
        icon: 'M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7',
        color: 'from-orange-500 to-amber-500',
    },
    {
        id: 6,
        name: 'Jouets',
        icon: 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z',
        color: 'from-yellow-500 to-amber-500',
    },
];
</script>

<template>
    <section class="px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <!-- En-tête de section -->
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Catégories Populaires</h2>
                <p class="mt-4 text-lg text-gray-600">Explorez nos catégories les plus populaires</p>
            </div>

            <!-- Grille de catégories -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                <Link
                    v-for="category in popularCategories"
                    :key="category.id"
                    :href="route('category.show', category.id)"
                    class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                >
                    <!-- Icône avec dégradé -->
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r" :class="category.color">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path :d="category.icon" />
                        </svg>
                    </div>

                    <!-- Nom de la catégorie -->
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600">
                        {{ category.name }}
                    </h3>

                    <!-- Nombre de produits -->
                    <p class="mt-2 text-sm text-gray-500">{{ Math.floor(Math.random() * 1000) }} produits</p>

                    <!-- Effet de survol -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r opacity-0 transition-opacity duration-300 group-hover:opacity-5"
                        :class="category.color"
                    ></div>
                </Link>
            </div>

            <!-- Bouton Voir plus -->
            <div class="mt-12 text-center">
                <Link
                    :href="route('categories.index')"
                    class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-6 py-3 text-base font-medium text-white transition-colors duration-200 hover:bg-green-700"
                >
                    Voir toutes les catégories
                    <svg class="-mr-1 ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.group:hover .group-hover\:text-green-600 {
    color: #059669;
}
</style>

<script lang="ts">
export default {
    name: 'PopularCategoriesSection',
};
</script>
