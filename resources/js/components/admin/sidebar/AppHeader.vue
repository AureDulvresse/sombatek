<script setup lang="ts">
import AppLogo from '@/components/admin/sidebar/AppLogo.vue';
import Breadcrumbs from '@/components/global/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import type { BreadcrumbItem, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, Search } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const user = page.props.auth.user;

const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <div class="bg-gradient-to-r from-blue-100 via-white to-green-100 border-b border-sidebar-border/80 shadow-lg backdrop-blur-md">
        <div class="mx-auto flex h-20 items-center px-4 md:max-w-7xl">
            <Link :href="route('dashboard')" class="flex items-center gap-x-2">
                <AppLogo />
            </Link>
            <div class="ml-8 flex-1 text-center md:text-left">
                <h1 class="text-3xl font-extrabold mb-1">
                    <template v-if="user.role === 'admin'">Tableau de bord administrateur</template>
                    <template v-else-if="user.role === 'shop'">Tableau de bord boutique</template>
                    <template v-else-if="user.role === 'promoter'">Tableau de bord démarcheur</template>
                    <template v-else>Tableau de bord client</template>
                </h1>
                <p class="text-gray-500 text-base">Bienvenue, {{ user.name.split(' ')[0] }} !</p>
            </div>
            <div class="flex gap-2">
                <template v-if="user.role === 'admin'">
                    <Link :href="route('shops.pending')" class="bg-primary text-white px-5 py-2 rounded-full font-semibold shadow hover:bg-primary-dark transition transform hover:scale-105 hover:shadow-xl">
                        Boutiques en attente
                    </Link>
                </template>
                <template v-else-if="user.role === 'shop'">
                    <Link :href="route('shop.products')" class="bg-primary text-white px-5 py-2 rounded-full font-semibold shadow hover:bg-primary-dark transition transform hover:scale-105 hover:shadow-xl">
                        Mes produits
                    </Link>
                </template>
                <template v-else-if="user.role === 'promoter'">
                    <Link :href="route('promoter.commissions')" class="bg-primary text-white px-5 py-2 rounded-full font-semibold shadow hover:bg-primary-dark transition transform hover:scale-105 hover:shadow-xl">
                        Mes commissions
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('orders.index')" class="bg-primary text-white px-5 py-2 rounded-full font-semibold shadow hover:bg-primary-dark transition transform hover:scale-105 hover:shadow-xl">
                        Mes commandes
                    </Link>
                </template>
            </div>
            <div class="ml-4 flex items-center space-x-2">
                <div class="relative flex items-center space-x-1">
                    <Button variant="ghost" size="icon" class="group h-10 w-10 cursor-pointer">
                        <Search class="size-7 text-blue-500 opacity-80 group-hover:opacity-100 transition-transform group-hover:scale-110" />
                    </Button>
                    <div class="hidden space-x-2 lg:flex">
                        <template v-for="item in rightNavItems" :key="item.title">
                            <TooltipProvider :delay-duration="0">
                                <Tooltip>
                                    <TooltipTrigger>
                                        <Button variant="ghost" size="icon" as-child class="group h-10 w-10 cursor-pointer">
                                            <a :href="item.href" target="_blank" rel="noopener noreferrer">
                                                <span class="sr-only">{{ item.title }}</span>
                                                <component :is="item.icon" class="size-7 text-green-500 opacity-80 group-hover:opacity-100 transition-transform group-hover:scale-110" />
                                            </a>
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{{ item.title }}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-b border-sidebar-border/70">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
