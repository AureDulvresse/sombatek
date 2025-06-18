<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

interface Advertisement {
    id: number;
    title: string;
    description: string;
    image: string;
    link: string;
    type: 'product' | 'shop';
    start_date: string;
    end_date: string;
}

const props = defineProps<{
    advertisements: Advertisement[];
}>();

const currentIndex = ref(0);
const isTransitioning = ref(false);
let interval: number;

const nextAd = () => {
    if (isTransitioning.value) return;
    isTransitioning.value = true;
    setTimeout(() => {
        currentIndex.value = (currentIndex.value + 1) % props.advertisements?.length;
        isTransitioning.value = false;
    }, 500);
};

const prevAd = () => {
    if (isTransitioning.value) return;
    isTransitioning.value = true;
    setTimeout(() => {
        currentIndex.value = (currentIndex.value - 1 + props.advertisements.length) % props.advertisements.length;
        isTransitioning.value = false;
    }, 500);
};

onMounted(() => {
    interval = window.setInterval(nextAd, 5000);
});

onUnmounted(() => {
    clearInterval(interval);
});
</script>

<template>
    <section class="relative overflow-hidden bg-gradient-to-r from-emerald-50 to-teal-50 py-16 dark:from-gray-800 dark:to-gray-700">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="mb-8 text-3xl font-bold text-gray-900 dark:text-white">Découvrez nos offres spéciales</h2>

            <div class="relative">
                <!-- Contrôles de navigation -->
                <button
                    @click="prevAd"
                    class="absolute top-1/2 left-0 z-10 -translate-y-1/2 transform rounded-full bg-white/80 p-2 text-gray-800 shadow-lg backdrop-blur-sm transition-all hover:bg-white dark:bg-gray-800/80 dark:text-white dark:hover:bg-gray-800"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    @click="nextAd"
                    class="absolute top-1/2 right-0 z-10 -translate-y-1/2 transform rounded-full bg-white/80 p-2 text-gray-800 shadow-lg backdrop-blur-sm transition-all hover:bg-white dark:bg-gray-800/80 dark:text-white dark:hover:bg-gray-800"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Carrousel de publicités -->
                <div class="relative h-[400px] overflow-hidden rounded-2xl">
                    <div
                        v-for="(ad, index) in advertisements"
                        :key="ad.id"
                        class="absolute inset-0 h-full w-full transition-all duration-500"
                        :class="{
                            'translate-x-0 opacity-100': index === currentIndex,
                            'translate-x-full opacity-0': index > currentIndex,
                            '-translate-x-full opacity-0': index < currentIndex,
                        }"
                    >
                        <Link :href="ad.link" class="block h-full">
                            <div class="relative h-full">
                                <img :src="ad.image" :alt="ad.title" class="h-full w-full object-cover" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                                    <div class="absolute right-0 bottom-0 left-0 p-8 text-white">
                                        <span
                                            class="mb-2 inline-block rounded-full bg-emerald-500 px-3 py-1 text-sm font-medium"
                                            :class="{ 'bg-blue-500': ad.type === 'shop' }"
                                        >
                                            {{ ad.type === 'product' ? 'Offre Produit' : 'Boutique Partenaire' }}
                                        </span>
                                        <h3 class="mb-2 text-2xl font-bold">{{ ad.title }}</h3>
                                        <p class="text-lg text-gray-200">{{ ad.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Indicateurs -->
                <div class="mt-4 flex justify-center space-x-2">
                    <button
                        v-for="(ad, index) in advertisements"
                        :key="ad.id"
                        @click="currentIndex = index"
                        class="h-2 w-2 rounded-full transition-all"
                        :class="{
                            'w-8 bg-emerald-500': index === currentIndex,
                            'bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500': index !== currentIndex,
                        }"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.5s ease;
}

.slide-enter-from {
    transform: translateX(100%);
}

.slide-leave-to {
    transform: translateX(-100%);
}
</style>

<script lang="ts">
export default {
    name: 'AdvertisementSection',
};
</script>
