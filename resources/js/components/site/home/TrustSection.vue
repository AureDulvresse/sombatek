<script setup lang="ts">
const trustPoints = [
    {
        title: 'Paiement Sécurisé',
        description: 'Payez en toute sécurité avec nos méthodes de paiement locales (MTN Money, Airtel Money, Orange Money, Carte bancaire)',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    },
    {
        title: 'Livraison Rapide',
        description: 'Livraison dans toutes les grandes villes du Congo (Pointe-Noire, Brazzaville, Dolisie, etc.)',
        icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
    },
    {
        title: 'Support Local',
        description: 'Une équipe de support disponible en français, lingala et kituba',
        icon: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    },
    {
        title: 'Garantie Produits',
        description: 'Garantie de 7 jours sur tous les produits et retour gratuit',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    },
];
</script>

<template>
    <section class="bg-white py-16 dark:bg-gray-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">Pourquoi Nous Faire Confiance ?</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Votre satisfaction est notre priorité</p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="(point, index) in trustPoints"
                    :key="index"
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 p-6 transition-all duration-300 hover:shadow-lg dark:from-gray-800 dark:to-gray-700"
                >
                    <div class="relative z-10">
                        <div class="mb-4 inline-flex rounded-full bg-white p-3 shadow-md dark:bg-gray-700">
                            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="point.icon" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">{{ point.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ point.description }}</p>
                    </div>
                    <div
                        class="absolute -top-4 -right-4 h-24 w-24 rounded-full bg-gradient-to-br from-emerald-200 to-teal-200 opacity-0 transition-opacity duration-300 group-hover:opacity-20 dark:from-emerald-800 dark:to-teal-800"
                    ></div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 p-6 dark:from-emerald-900/20 dark:to-teal-900/20">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">10k+</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Clients Satisfaits</p>
                    </div>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 p-6 dark:from-blue-900/20 dark:to-indigo-900/20">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600 dark:text-blue-400">500+</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Villes Desservies</p>
                    </div>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 p-6 dark:from-purple-900/20 dark:to-pink-900/20">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-purple-600 dark:text-purple-400">24/7</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Support Client</p>
                    </div>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 p-6 dark:from-amber-900/20 dark:to-orange-900/20">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-amber-600 dark:text-amber-400">100%</div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Satisfaction</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.group:hover .group-hover\:opacity-20 {
    opacity: 0.2;
}
</style>

<script lang="ts">
export default {
    name: 'TrustSection',
};
</script>
