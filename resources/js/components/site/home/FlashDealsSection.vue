<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import ProductCard from '../ProductCard.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    products: Array<any>;
}>();

// Timer pour les offres flash
const timeLeft = ref({
    hours: 2,
    minutes: 0,
    seconds: 0,
});

let timer: number;

const updateTimer = () => {
    if (timeLeft.value.seconds > 0) {
        timeLeft.value.seconds--;
    } else if (timeLeft.value.minutes > 0) {
        timeLeft.value.minutes--;
        timeLeft.value.seconds = 59;
    } else if (timeLeft.value.hours > 0) {
        timeLeft.value.hours--;
        timeLeft.value.minutes = 59;
        timeLeft.value.seconds = 59;
    }
};

onMounted(() => {
    timer = setInterval(updateTimer, 1000);
});

onUnmounted(() => {
    clearInterval(timer);
});
</script>

<template>
    <section class="bg-gradient-to-r from-red-50 to-orange-50 px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <!-- En-tête de section -->
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Offres Flash</h2>
                <p class="mt-4 text-lg text-gray-600">Profitez des meilleures offres du moment</p>
            </div>

            <!-- Timer -->
            <div class="mb-8 flex justify-center">
                <div class="inline-flex space-x-4 rounded-lg bg-white p-4 shadow-lg">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ timeLeft.hours.toString().padStart(2, '0') }}</div>
                        <div class="text-sm text-gray-600">Heures</div>
                    </div>
                    <div class="text-2xl font-bold text-gray-400">:</div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ timeLeft.minutes.toString().padStart(2, '0') }}</div>
                        <div class="text-sm text-gray-600">Minutes</div>
                    </div>
                    <div class="text-2xl font-bold text-gray-400">:</div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ timeLeft.seconds.toString().padStart(2, '0') }}</div>
                        <div class="text-sm text-gray-600">Secondes</div>
                    </div>
                </div>
            </div>

            <!-- Grille de produits -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="(product, index) in products"
                    :key="product.id"
                    class="animate-fade-in-up"
                    :style="{ animationDelay: `${index * 100}ms` }"
                >
                    <div class="relative">
                        <div class="absolute -top-2 -right-2 z-10">
                            <span class="inline-flex items-center rounded-full bg-red-600 px-3 py-1 text-sm font-medium text-white">
                                -{{ product.discount_percentage }}%
                            </span>
                        </div>
                        <ProductCard :product="product" :index="index" />
                    </div>
                </div>
            </div>

            <!-- Bouton Voir plus -->
            <div class="mt-12 text-center">
                <Link
                    :href="route('products.index')"
                    class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-6 py-3 text-base font-medium text-white transition-colors duration-200 hover:bg-red-700"
                >
                    Voir toutes les offres
                    <svg class="-mr-1 ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script lang="ts">
export default {
    name: 'FlashDealsSection',
};
</script>
