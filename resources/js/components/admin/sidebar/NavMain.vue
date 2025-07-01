<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="(item, idx) in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title"
                    :class="[item.href === page.url ? 'bg-blue-100 rounded-xl shadow' : 'hover:bg-gray-100', 'transition-all flex items-center gap-3 px-3 py-2']">
                    <Link :href="item.href" class="flex items-center w-full">
                        <component :is="item.icon"
                            :class="[
                                'text-2xl',
                                idx === 0 ? 'text-blue-500' : idx === 1 ? 'text-green-500' : idx === 2 ? 'text-orange-500' : 'text-gray-400'
                            ]"
                        />
                        <span class="ml-2 font-medium">{{ item.title }}</span>
                        <span v-if="item.badge && item.badge > 0"
                            class="ml-2 inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-1 text-sm font-bold text-white shadow animate-bounce">
                            {{ item.badge }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

<script lang="ts">
export default {
    name: 'NavMain',
};
</script>
