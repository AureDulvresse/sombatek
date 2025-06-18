import axios from 'axios';
import { defineStore } from 'pinia';
import { computed, reactive, toRefs } from 'vue';

interface WishlistItem {
    id: number;
    product_id: number;
    name: string;
    price: number;
    default_image: string;
    options?: any;
    product?: {
        stock: number;
        is_active: boolean;
    };
}

interface WishlistState {
    items: WishlistItem[];
    items_count: number;
    total: number;
    isLoading: boolean;
    isInitialized: boolean;
    lastFetch: number | null;
    error: string | null;
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
            error: null,
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
                state.error = null;
                const response = await axios.get(route('wishlist.get'));

                if (response.data.success) {
                    state.items = response.data.wishlist.items || [];
                    state.items_count = response.data.wishlist.items_count || 0;
                    state.total = response.data.wishlist.total || 0;
                    state.lastFetch = Date.now();
                    state.isInitialized = true;
                } else {
                    state.error = response.data.message || 'Erreur lors de la récupération de la wishlist';
                    state.items = [];
                    state.items_count = 0;
                    state.total = 0;
                }
                return response.data.wishlist;
            } catch (error: any) {
                console.error('Erreur lors de la récupération de la wishlist:', error);
                state.error = error.response?.data?.message || 'Erreur lors de la récupération de la wishlist';
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
                state.error = null;
                const response = await axios.post(route('wishlist.toggle-item'), { product_id: productId });

                if (response.data.success) {
                    await fetchWishlist(true); // Rafraîchir la wishlist complète
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                } else {
                    state.error = response.data.message || 'Erreur lors de la modification de la wishlist';
                }

                return response.data;
            } catch (error: any) {
                console.error('Erreur lors de la modification de la wishlist:', error);
                state.error = error.response?.data?.message || 'Erreur lors de la modification de la wishlist';
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        async function removeItem(productId: number) {
            try {
                state.isLoading = true;
                state.error = null;
                const response = await axios.post(route('wishlist.remove-item'), { product_id: productId });

                if (response.data.success) {
                    await fetchWishlist(true); // Rafraîchir la wishlist complète
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                } else {
                    state.error = response.data.message || "Erreur lors de la suppression de l'article";
                }

                return response.data;
            } catch (error: any) {
                console.error("Erreur lors de la suppression de l'article:", error);
                state.error = error.response?.data?.message || "Erreur lors de la suppression de l'article";
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        async function clear() {
            try {
                state.isLoading = true;
                state.error = null;
                const response = await axios.post(route('wishlist.clear'));

                if (response.data.success) {
                    state.items = [];
                    state.items_count = 0;
                    state.total = 0;
                    state.lastFetch = null;
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                } else {
                    state.error = response.data.message || 'Erreur lors de la suppression de la wishlist';
                }

                return response.data;
            } catch (error: any) {
                console.error('Erreur lors de la suppression de la wishlist:', error);
                state.error = error.response?.data?.message || 'Erreur lors de la suppression de la wishlist';
                throw error;
            } finally {
                state.isLoading = false;
            }
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
        persist: true,
    },
);
