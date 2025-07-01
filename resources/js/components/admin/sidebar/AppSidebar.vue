<script setup lang="ts">
import NavMain from '@/components/admin/sidebar/NavMain.vue';
import NavUser from '@/components/admin/sidebar/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, HelpCircle, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import NavFooter from './NavFooter.vue';

const page = usePage();
const user = page.props.auth.user;

let mainNavItems: NavItem[] = [];
const stats = (page.props as any).stats || {};
const pendingShops = typeof stats.pendingShops === 'number' ? stats.pendingShops : 0;

if (user.role === 'admin') {
    mainNavItems = [
        { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
        { title: 'Utilisateurs', href: route('users.index'), icon: Folder },
        { title: 'Boutiques', href: route('shops.index'), icon: Folder, badge: pendingShops },
        { title: 'Produits', href: '/admin/products', icon: Folder },
        { title: 'Commandes', href: '/admin/orders', icon: Folder },
        { title: 'Promotions', href: '/admin/promotions', icon: Folder },
    ];
} else if (user.role === 'shop') {
    mainNavItems = [
        { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
        { title: 'Mes produits', href: '/shop/products', icon: Folder },
        { title: 'Mes commandes', href: '/shop/orders', icon: Folder },
        { title: 'Statistiques', href: '/shop/stats', icon: Folder },
    ];
} else if (user.role === 'promoter') {
    mainNavItems = [
        { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
        { title: 'Mes commissions', href: '/promoter/commissions', icon: Folder },
        { title: 'Boutiques partenaires', href: '/promoter/shops', icon: Folder },
    ];
} else {
    mainNavItems = [
        { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
        { title: 'Mes commandes', href: '/orders', icon: Folder },
        { title: 'Wishlist', href: '/wishlist', icon: Folder },
        { title: 'Profil', href: '/profile', icon: Folder },
    ];
}

const footerNavItems: NavItem[] = [
    {
        title: 'A Propos de sombatek',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
    {
        title: 'Centre d\'aide',
        href: '/help',
        icon: HelpCircle,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset"
        class="bg-gradient-to-b from-blue-50 via-white to-green-50 min-h-screen">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
