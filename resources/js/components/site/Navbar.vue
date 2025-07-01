<script setup lang="ts">
import { useCartStore } from '@/stores/cart';
import { useWishlistStore } from '@/stores/wishlist';
import { Bars3Icon, MagnifyingGlassIcon, ShoppingCartIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Link, usePage } from '@inertiajs/vue3';
import { HeartIcon } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import CartSheet from './Sheets/CartSheet.vue';
import WishlistSheet from './Sheets/WishlistSheet.vue';

// Définition des éléments de navigation
const navigationItems = [
    { name: 'Accueil', route: 'home' },
    { name: 'À Propos', route: 'about' },
    { name: 'Boutiques', route: 'shops.index' },
    { name: 'Produits', route: 'products.index' },
    { name: 'Blog', route: 'blog' },
    { name: 'Contact', route: 'contact' },
];

// Gestion de la recherche avec indicateur de chargement
const searchQuery = ref('');
const isSearching = ref(false);
const search = () => {
    if (searchQuery.value.trim()) {
        isSearching.value = true;
        // Redirection vers la page des produits avec la recherche
        window.location.href = route('products.index', { search: searchQuery.value });
    }
};

// Gestion du panneau latéral du panier
const isCartOpen = ref(false);
const toggleCart = () => {
    isCartOpen.value = !isCartOpen.value;
};

// Gestion du panneau latéral de la wishlist
const isWishlistOpen = ref(false);
const toggleWishlist = () => {
    isWishlistOpen.value = !isWishlistOpen.value;
};

// Gestion du menu mobile
const isMobileMenuOpen = ref(false);
const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

// Ajouter ces refs après les autres refs existantes
const isUserMenuOpen = ref(false);

// Ajouter cette fonction après les autres fonctions existantes
const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const closeUserMenu = () => {
    isUserMenuOpen.value = false;
};

const page = usePage();

// Calcul des initiales de l'utilisateur
const userInitials = computed(() => {
    const user = page.props.auth?.user;
    if (!user || !user.name) return 'U';

    const names = user.name.trim().split(' ');
    if (names.length === 1) {
        return names[0].charAt(0).toUpperCase();
    }
    return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
});

// Vérification si l'utilisateur a une photo de profil
const hasProfilePhoto = computed(() => {
    const user = page.props.auth?.user;
    return user && user.avatar && user.avatar.trim() !== '';
});

const cartStore = useCartStore();
const wishlistStore = useWishlistStore();

// Ajouter ces refs pour stocker les handlers
const cartUpdateHandler = ref<(() => void) | null>(null);
const wishlistUpdateHandler = ref<(() => void) | null>(null);

onMounted(async () => {
    await Promise.all([cartStore.fetchCart(), wishlistStore.fetchWishlist()]);

    // Créer les fonctions de rappel
    cartUpdateHandler.value = () => cartStore.fetchCart();
    wishlistUpdateHandler.value = () => wishlistStore.fetchWishlist();

    // Ajouter les écouteurs d'événements
    window.addEventListener('cart-updated', cartUpdateHandler.value);
    window.addEventListener('wishlist-updated', wishlistUpdateHandler.value);
});

onUnmounted(() => {
    // Supprimer les écouteurs d'événements
    if (cartUpdateHandler.value) {
        window.removeEventListener('cart-updated', cartUpdateHandler.value);
        cartUpdateHandler.value = null;
    }
    if (wishlistUpdateHandler.value) {
        window.removeEventListener('wishlist-updated', wishlistUpdateHandler.value);
        wishlistUpdateHandler.value = null;
    }
});
</script>

<template>
    <nav class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 shadow-sm backdrop-blur-lg dark:border-gray-700 dark:bg-gray-900/95">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Section gauche: Logo et navigation desktop -->
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <Link :href="route('home')" class="flex items-center">
                            <img class="h-10 w-auto transition-transform hover:scale-105" src="/logo.png" alt="Logo Marketplace" />
                        </Link>
                    </div>

                    <!-- Navigation desktop -->
                    <div class="hidden lg:ml-8 lg:flex lg:space-x-1">
                        <Link
                            v-for="item in navigationItems"
                            :key="item.name"
                            :href="route(item.route)"
                            class="group relative rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200"
                            :class="[
                                route().current(item.route)
                                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400'
                                    : 'text-gray-700 hover:bg-gray-50 hover:text-emerald-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-emerald-400',
                            ]"
                        >
                            {{ item.name }}
                            <span
                                v-if="route().current(item.route)"
                                class="absolute bottom-0 left-1/2 h-0.5 w-6 -translate-x-1/2 rounded-full bg-emerald-500"
                            ></span>
                        </Link>
                    </div>
                </div>

                <!-- Section centre: Barre de recherche -->
                <div class="mx-4 hidden max-w-md flex-1 sm:block">
                    <div class="group relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 transition-colors group-focus-within:text-emerald-500" />
                        </div>
                        <input
                            id="search"
                            v-model="searchQuery"
                            @keyup.enter="search"
                            type="search"
                            placeholder="Rechercher des produits..."
                            class="block w-full rounded-xl border border-gray-300 bg-gray-50 py-2.5 pr-12 pl-10 text-sm transition-all duration-200 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-emerald-400 dark:focus:bg-gray-700"
                        />
                        <!-- Indicateur de chargement -->
                        <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 animate-spin text-emerald-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Section droite: Actions utilisateur -->
                <div class="flex items-center space-x-3">
                    <!-- Menu utilisateur et Panier ou boutons de connexion -->
                    <template v-if="page.props.auth?.user">
                        <!-- Bouton du wishlist -->
                        <button
                            class="group relative rounded-lg p-2 text-gray-400 transition-all duration-200 hover:bg-gray-100 hover:text-red-500 dark:hover:bg-gray-800"
                            @click="toggleWishlist"
                        >
                            <HeartIcon class="h-6 w-6 transition-colors" :class="{ 'fill-current text-red-500': wishlistStore.items?.length > 0 }" />
                            <span
                                v-if="wishlistStore.items?.length > 0"
                                class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-semibold text-white ring-2 ring-white dark:ring-gray-900"
                            >
                                {{ wishlistStore.items.length > 99 ? '99+' : wishlistStore.items.length }}
                            </span>
                        </button>

                        <!-- Bouton du panier -->
                        <button
                            class="group relative rounded-lg p-2 text-gray-400 transition-all duration-200 hover:bg-gray-100 hover:text-emerald-500 dark:hover:bg-gray-800"
                            @click="toggleCart"
                        >
                            <ShoppingCartIcon class="h-6 w-6" />
                            <span
                                v-if="cartStore.items_count > 0"
                                class="absolute -top-1 -right-1 flex h-5 w-5 animate-pulse items-center justify-center rounded-full bg-red-500 text-xs font-semibold text-white ring-2 ring-white dark:ring-gray-900"
                            >
                                {{ cartStore.items_count > 99 ? '99+' : cartStore.items_count }}
                            </span>
                        </button>

                        <div class="relative">
                            <button
                                @click="toggleUserMenu"
                                class="flex items-center rounded-full p-1 transition-all duration-200 hover:ring-2 hover:ring-emerald-500/20 focus:ring-2 focus:ring-emerald-500/50 focus:outline-none"
                            >
                                <span class="sr-only">Ouvrir le menu utilisateur</span>

                                <!-- Photo de profil ou initiales -->
                                <div v-if="hasProfilePhoto" class="h-8 w-8 overflow-hidden rounded-full ring-2 ring-gray-200 dark:ring-gray-700">
                                    <img class="h-full w-full object-cover" :src="page.props.auth.user.avatar" :alt="page.props.auth.user.name" />
                                </div>
                                <div
                                    v-else
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-sm ring-2 ring-gray-200 dark:ring-gray-700"
                                >
                                    {{ userInitials }}
                                </div>
                            </button>

                            <!-- Menu déroulant -->
                            <div
                                v-if="isUserMenuOpen"
                                class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl border border-gray-100 bg-white shadow-lg ring-1 ring-black/5 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:ring-white/10"
                            >
                                <div class="p-1">
                                    <!-- Header du menu avec info utilisateur -->
                                    <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ page.props.auth.user.name }}</p>
                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ page.props.auth.user.email }}</p>
                                    </div>

                                    <!-- Liens du menu -->
                                    <div class="py-1">
                                        <Link
                                            v-if="page.props.auth.user.role === 'admin'"
                                            :href="route('admin.dashboard')"
                                            class="group mx-1 flex items-center rounded-lg px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-300 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400"
                                            @click="closeUserMenu"
                                        >
                                            Mon espace (Admin)
                                        </Link>
                                        <Link
                                            v-else
                                            :href="route('profile.edit')"
                                            class="group mx-1 flex items-center rounded-lg px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-300 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400"
                                            @click="closeUserMenu"
                                        >
                                            Mon espace
                                        </Link>

                                        <Link
                                            :href="route('orders.index')"
                                            class="group mx-1 flex items-center rounded-lg px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-300 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400"
                                            @click="closeUserMenu"
                                        >
                                            Mes Commandes
                                        </Link>

                                        <!-- Liens vendeur -->
                                        <template v-if="page.props.auth.user.isVendor && page.props.auth.user.isVendor()">
                                            <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>
                                            <Link
                                                :href="route('vendor.dashboard')"
                                                class="group mx-1 flex items-center rounded-lg px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-emerald-50 hover:text-emerald-600 dark:text-gray-300 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400"
                                                @click="closeUserMenu"
                                            >
                                                Tableau de bord vendeur
                                            </Link>
                                        </template>

                                        <!-- Séparateur -->
                                        <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>

                                        <!-- Déconnexion -->
                                        <Link
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="group mx-1 flex w-full items-center rounded-lg px-4 py-2 text-left text-sm text-gray-700 transition-colors hover:bg-red-50 hover:text-red-600 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                                            @click="closeUserMenu"
                                        >
                                            Déconnexion
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <div class="flex items-center">
                            <Link
                                :href="route('login')"
                                class="text-sm font-medium text-gray-700 hover:text-emerald-600 dark:text-gray-300 dark:hover:text-emerald-400"
                            >
                                Connexion
                            </Link>
                            <Link
                                :href="route('register')"
                                class="ml-4 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                            >
                                Inscription
                            </Link>
                        </div>
                    </template>

                    <!-- Bouton menu mobile -->
                    <button type="button" class="p-2 text-gray-400 transition-colors hover:text-emerald-500 lg:hidden" @click="toggleMobileMenu">
                        <span class="sr-only">Ouvrir le menu principal</span>
                        <Bars3Icon v-if="!isMobileMenuOpen" class="h-6 w-6" />
                        <XMarkIcon v-else class="h-6 w-6" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu mobile -->
        <div v-if="isMobileMenuOpen" class="border-t border-gray-200 bg-white lg:hidden dark:border-gray-700 dark:bg-gray-900">
            <div class="space-y-1 px-4 py-3">
                <!-- Barre de recherche mobile -->
                <div class="mb-3 border-b border-gray-200 pb-3 sm:hidden dark:border-gray-700">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            v-model="searchQuery"
                            @keyup.enter="search"
                            type="search"
                            placeholder="Rechercher des produits..."
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 pr-3 pl-10 text-sm focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                        />
                    </div>
                </div>

                <!-- Navigation mobile -->
                <div class="space-y-1">
                    <Link
                        v-for="item in navigationItems"
                        :key="item.name"
                        :href="route(item.route)"
                        class="block rounded-lg px-3 py-2 text-base font-medium transition-colors"
                        :class="[
                            route().current(item.route)
                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-emerald-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-emerald-400',
                        ]"
                        @click="isMobileMenuOpen = false"
                    >
                        {{ item.name }}
                    </Link>
                </div>

                <!-- Actions mobile pour utilisateur non connecté -->
                <template v-if="!$page.props.auth.user">
                    <div class="mt-3 space-y-2 border-t border-gray-200 pt-3 sm:hidden dark:border-gray-700">
                        <Link
                            :href="route('login')"
                            class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-emerald-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-emerald-400"
                            @click="isMobileMenuOpen = false"
                        >
                            Connexion
                        </Link>
                        <Link
                            :href="route('register')"
                            class="block rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-3 py-2 text-base font-medium text-white transition-all hover:from-emerald-700 hover:to-teal-700"
                            @click="isMobileMenuOpen = false"
                        >
                            Inscription
                        </Link>
                    </div>
                </template>
            </div>
        </div>

        <!-- Panneau latéral du panier -->
        <CartSheet :is-open="isCartOpen" @close="isCartOpen = false" />

        <WishlistSheet :is-open="isWishlistOpen" @close="isWishlistOpen = false" />
    </nav>
</template>

<script lang="ts">
export default {
    name: 'Navbar',
};
</script>
