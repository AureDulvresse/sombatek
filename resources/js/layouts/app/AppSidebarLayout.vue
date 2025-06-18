<script setup lang="ts">
import AppContent from '@/components/admin/sidebar/AppContent.vue';
import AppShell from '@/components/admin/sidebar/AppShell.vue';
import AppSidebar from '@/components/admin/sidebar/AppSidebar.vue';
import AppSidebarHeader from '@/components/admin/sidebar/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
