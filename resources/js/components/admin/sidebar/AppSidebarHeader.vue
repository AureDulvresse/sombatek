<script setup lang="ts">
import Breadcrumbs from '@/components/global/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Globe } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div>
            <Link
                :href="route('home')"
                class="flex-1 transform rounded-xl bg-green-600 px-4 py-2 font-medium text-white transition-all duration-300 hover:scale-105 hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <Globe :size="18" />
                Voir le site
            </Link>
        </div>
    </header>
</template>
