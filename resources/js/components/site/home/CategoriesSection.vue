<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps({
    categories: {
        type: Array,
        required: true,
    },
});
</script>

<script lang="ts">
export default {
    name: 'CategoriesSection',
};
</script>

<template>
    <div class="bg-white py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-4xl font-bold text-gray-900">
                    Catégories
                    <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">populaires</span>
                </h2>
                <p class="mx-auto max-w-2xl text-xl text-gray-600">
                    Découvrez nos catégories les plus prisées et trouvez exactement ce que vous cherchez
                </p>
            </div>

            <!-- Categories carousel -->
            <div class="relative">
                <div class="scrollbar-hide flex space-x-6 overflow-x-auto pb-4">
                    <div
                        v-for="(category, index) in categories"
                        :key="category.id"
                        class="group w-80 flex-none transform rounded-3xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 transition-all duration-500 hover:-translate-y-2 hover:border-green-300 hover:shadow-xl"
                        :style="{ animationDelay: `${index * 100}ms` }"
                    >
                        <div
                            class="absolute inset-0 rounded-3xl bg-gradient-to-br from-green-50 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        ></div>

                        <div class="relative z-10">
                            <div
                                class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 transition-transform duration-300 group-hover:scale-110"
                            >
                                <img :src="category.image" :alt="category.name" class="h-7 w-7 text-white" />
                            </div>

                            <h3 class="mb-2 text-lg font-bold text-gray-900 transition-colors duration-300 group-hover:text-green-600">
                                {{ category.name }}
                            </h3>

                            <p class="mb-4 text-sm leading-relaxed text-gray-600">
                                {{ category.description }}
                            </p>

                            <Link
                                :href="route('category.show', category.slug)"
                                class="inline-flex items-center text-sm font-semibold text-green-600 transition-colors duration-300 group-hover:text-green-700"
                            >
                                Voir les produits
                                <svg
                                    class="ml-2 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Scroll indicators -->
                <div class="mt-6 flex justify-center space-x-2">
                    <div class="h-2 w-2 rounded-full bg-green-600"></div>
                    <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                    <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Hide scrollbar for horizontal scroll */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
