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
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" class="truncate" />
            </template>
        </div>

        <div class="flex-shrink-0">
            <Link
                :href="route('home')"
                class="flex items-center gap-2 rounded-full bg-green-600 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:bg-green-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-green-400"
            >
                <Globe :size="24" />
                Voir le site
            </Link>
        </div>
    </header>
</template>
