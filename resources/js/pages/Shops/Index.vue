<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import ShopCard from '@/components/site/ShopCard.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Shop {
    id: number;
    name: string;
    slug: string;
    description: string;
    image: string;
    rating: number;
    products_count: number;
    user: {
        name: string;
    };
}

interface Props {
    shops: {
        data: Shop[];
        links: any[];
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Boutiques',
        href: '/shops',
    },
];

const search = ref('');

const filteredShops = computed(() => {
    let result = props.shops.data;

    if (search.value.trim()) {
        const searchTerm = search.value.toLowerCase().trim();
        result = result.filter((shop: Shop) =>
            shop.name.toLowerCase().includes(searchTerm) ||
            shop.description.toLowerCase().includes(searchTerm)
        );
    }

    return result;
});
</script>

<template>

    <Head title="Nos Boutiques" />
    <SiteLayout title="Nos Boutiques">
        <PageBanner title="Nos Boutiques" description="Découvrez nos boutiques partenaires et leurs produits de qualité"
            :breadcrumbs="breadcrumbs" />

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header avec recherche -->
            <div class="mb-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Nos Boutiques
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ filteredShops.length }} boutique{{ filteredShops.length > 1 ? 's' : '' }} trouvée{{
                                filteredShops.length > 1 ? 's' : '' }}
                        </p>
                    </div>

                    <!-- Barre de recherche -->
                    <div class="relative lg:w-80">
                        <input v-model="search" type="text" placeholder="Rechercher une boutique..."
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 pl-10 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                        <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Liste des boutiques -->
            <div v-if="filteredShops.length === 0" class="py-12 text-center">
                <div class="mx-auto max-w-md">
                    <div class="mb-4 text-6xl">🏪</div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                        Aucune boutique trouvée
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Essayez d'ajuster votre recherche
                    </p>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <ShopCard v-for="(shop, index) in filteredShops" :key="shop.id" :shop="shop" :index="index" />
            </div>

            <!-- Pagination -->
            <div v-if="props.shops.links && props.shops.links.length > 1" class="mt-12 flex justify-center">
                <nav class="flex items-center gap-2">
                    <Link v-for="link in props.shops.links" :key="link.label" :href="link.url || '#'" :class="[
                        'px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                        link.active
                            ? 'bg-primary text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'
                    ]">
                    {{ link.label.replace(/(<([^>]+)>)/gi, '') }}
                        </Link>
                </nav>
            </div>
        </div>
    </SiteLayout>
</template>