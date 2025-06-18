<script setup lang="ts">
import { MagnifyingGlassIcon, ShoppingBagIcon, SparklesIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const searchQuery = ref('');

const categories = [
    { name: 'Mode', icon: '👗' },
    { name: 'Électronique', icon: '📱' },
    { name: 'Maison', icon: '🏠' },
    { name: 'Beauté', icon: '💄' },
    { name: 'Sport', icon: '⚽' },
    { name: 'Jouets', icon: '🎮' },
];

const features = [
    {
        title: 'Livraison Express',
        description: 'Livraison en 24h',
        icon: ShoppingBagIcon,
    },
    {
        title: 'Prix Imbattables',
        description: 'Meilleur rapport qualité/prix',
        icon: SparklesIcon,
    },
];
</script>

<script lang="ts">
export default {
    name: 'HeroSection',
};
</script>

<template>
    <section class="relative overflow-hidden bg-gradient-to-br from-gray-900 to-gray-800">
        <!-- Formes décoratives -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-green-500/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-blue-500/20 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <!-- Contenu principal -->
            <div class="grid gap-12 lg:grid-cols-2">
                <!-- Texte et recherche -->
                <div class="flex flex-col justify-center">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Découvrez des produits
                        <span class="block text-green-400">exceptionnels</span>
                    </h1>
                    <p class="mt-6 text-lg text-gray-300">
                        Explorez notre sélection de produits de qualité, à des prix imbattables. Des milliers de produits vous attendent.
                    </p>

                    <!-- Barre de recherche -->
                    <div class="mt-8">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Rechercher un produit..."
                                class="w-full rounded-xl border-0 bg-white/10 px-6 py-4 text-white placeholder-gray-400 backdrop-blur-sm focus:ring-2 focus:ring-green-500"
                            />
                            <button
                                class="absolute top-1/2 right-2 -translate-y-1/2 rounded-lg bg-green-500 p-2 text-white transition-colors hover:bg-green-600"
                            >
                                <MagnifyingGlassIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <!-- Catégories rapides -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button
                                v-for="category in categories"
                                :key="category.name"
                                class="rounded-full bg-white/10 px-4 py-2 text-sm text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                            >
                                {{ category.icon }} {{ category.name }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Image ou illustration -->
                <div class="relative hidden lg:block">
                    <div class="relative aspect-square overflow-hidden rounded-2xl">
                        <img src="/images/hero-image.jpg" alt="Produits en vedette" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 to-transparent"></div>
                    </div>

                    <!-- Badge flottant -->
                    <div class="absolute -top-4 -right-4 rounded-xl bg-white p-4 shadow-lg">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-green-100 p-2">
                                <SparklesIcon class="h-6 w-6 text-green-600" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Nouveaux produits</p>
                                <p class="text-xs text-gray-500">Ajoutés aujourd'hui</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fonctionnalités -->
            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:mt-20">
                <div v-for="feature in features" :key="feature.title" class="rounded-xl bg-white/10 p-6 backdrop-blur-sm">
                    <component :is="feature.icon" class="h-8 w-8 text-green-400" />
                    <h3 class="mt-4 text-lg font-medium text-white">
                        {{ feature.title }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-300">
                        {{ feature.description }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}
</style>
