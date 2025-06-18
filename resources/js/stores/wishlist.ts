import axios from 'axios';
import { defineStore } from 'pinia';
import { computed, reactive, toRefs } from 'vue';

interface WishlistItem {
    id: number;
    product_id: number;
    name: string;
    price: number;
    default_image: string;
    options: Record<string, string>;
}

interface WishlistState {
    items: WishlistItem[];
    items_count: number;
    total: number;
    isLoading: boolean;
    isInitialized: boolean;
    lastFetch: number | null;
}

export const useWishlistStore = defineStore(
    'wishlist',
    () => {
        const state = reactive<WishlistState>({
            items: [],
            items_count: 0,
            total: 0,
            isLoading: false,
            isInitialized: false,
            lastFetch: null,
        });

        const shouldRefetch = computed(() => {
            if (!state.lastFetch) return true;
            const now = Date.now();
            return now - state.lastFetch > 5 * 60 * 1000; // 5 minutes
        });

        const isInWishlist = (productId: number) => {
            return state.items.some((item) => item.product_id === productId);
        };

        async function fetchWishlist(force = false) {
            if (state.isLoading) return;
            if (!force && !shouldRefetch.value) return;

            try {
                state.isLoading = true;
                const response = await axios.get(route('wishlist.get'));
                if (response.data.success) {
                    state.items = response.data.wishlist.items || [];
                    state.items_count = response.data.wishlist.items_count || 0;
                    state.total = response.data.wishlist.total || 0;
                    state.lastFetch = Date.now();
                    state.isInitialized = true;
                } else {
                    // En cas d'échec, on s'assure que les données sont dans un état valide
                    state.items = [];
                    state.items_count = 0;
                    state.total = 0;
                }
                return response.data.wishlist;
            } catch (error) {
                console.error('Erreur lors de la récupération de la wishlist:', error);
                // En cas d'erreur, on s'assure que les données sont dans un état valide
                state.items = [];
                state.items_count = 0;
                state.total = 0;
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        async function toggleItem(productId: number) {
            try {
                state.isLoading = true;
                const response = await axios.post(route('wishlists.toggle-item'), { product_id: productId });

                if (response.data.success) {
                    // Mettre à jour l'état local
                    if (response.data.isInWishlist) {
                        state.items.push(response.data.item);
                    } else {
                        state.items = state.items.filter((item) => item.product_id !== productId);
                    }
                    state.items_count = state.items.length;
                    state.total = state.items.reduce((total, item) => total + item.price, 0);

                    // Émettre l'événement de mise à jour
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                }

                return response.data;
            } catch (error) {
                console.error('Erreur lors de la modification de la wishlist:', error);
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        async function removeItem(itemId: number) {
            try {
                state.isLoading = true;
                const response = await axios.post(route('wishlists.remove-item'), { item_id: itemId });

                if (response.data.success) {
                    // Mettre à jour l'état local
                    state.items = state.items.filter((item) => item.id !== itemId);
                    state.items_count = state.items.length;
                    state.total = state.items.reduce((total, item) => total + item.price, 0);

                    // Émettre l'événement de mise à jour
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                }

                return response.data;
            } catch (error) {
                console.error("Erreur lors de la suppression de l'article:", error);
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        function clear() {
            state.items = [];
            state.items_count = 0;
            state.total = 0;
            state.lastFetch = null;

            // Émettre l'événement de mise à jour
            window.dispatchEvent(new CustomEvent('wishlist-updated'));
        }

        return {
            ...toRefs(state),
            shouldRefetch,
            isInWishlist,
            fetchWishlist,
            toggleItem,
            removeItem,
            clear,
        };
    },
    {
        persist: {
            key: 'wishlist-store',
            storage: localStorage,
            paths: ['items', 'items_count', 'total', 'lastFetch'],
        },
    },
);
