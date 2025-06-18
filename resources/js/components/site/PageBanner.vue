<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Breadcrumbs from '../global/Breadcrumbs.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    breadcrumbs: {
        type: Array as () => Array<{ name: string; route?: string }>,
        default: () => [],
    },
});
</script>

<template>
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-10"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-teal-500/10"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav v-if="breadcrumbs.length" class="mb-4 flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <Link
                            :href="route('home')"
                            class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300"
                        >
                            Accueil
                        </Link>
                    </li>
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </ol>
            </nav>

            <!-- Titre et description -->
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                    {{ title }}
                </h1>
                <p v-if="description" class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                    {{ description }}
                </p>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    name: 'PageBanner'
}
</script>
