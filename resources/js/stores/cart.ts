import axios from 'axios';
import { defineStore } from 'pinia';
import { reactive, toRefs } from 'vue';

interface CartItem {
    id: number;
    product_id: number;
    name: string;
    price: number;
    quantity: number;
    default_image: string;
    options: Record<string, string>;
}

interface CartState {
    items: CartItem[];
    items_count: number;
    subtotal: number;
    total: number;
    discount_total: number;
    promotion_code: string | null;
    promotion_discount: number;
    isLoading: boolean;
}

export const useCartStore = defineStore(
    'cart',
    () => {
        const state = reactive<CartState>({
            items: [],
            items_count: 0,
            subtotal: 0,
            total: 0,
            discount_total: 0,
            promotion_code: null,
            promotion_discount: 0,
            isLoading: false,
        });

        async function fetchCart() {
            if (state.isLoading) return;

            try {
                state.isLoading = true;
                const response = await axios.get(route('cart.get'));
                if (response.data.success) {
                    state.items = response.data.cart.items || [];
                    state.items_count = response.data.cart.items_count || 0;
                    state.subtotal = response.data.cart.subtotal || 0;
                    state.total = response.data.cart.total || 0;
                    state.discount_total = response.data.cart.discount_total || 0;
                    state.promotion_code = response.data.cart.promotion_code;
                    state.promotion_discount = response.data.cart.promotion_discount || 0;
                }
                return response.data.cart;
            } catch (error) {
                console.error('Erreur lors de la récupération du panier:', error);
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        async function addItem(productId: number, quantity: number) {
            if (state.isLoading) return;

            try {
                state.isLoading = true;
                const response = await axios.post(route('cart.add-item'), {
                    product_id: productId,
                    quantity: quantity,
                });

                if (response.data.success) {
                    await fetchCart();
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                }

                return response.data;
            } catch (error) {
                console.error("Erreur lors de l'ajout au panier:", error);
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        async function updateQuantity(itemId: number, quantity: number) {
            if (state.isLoading) return;

            try {
                state.isLoading = true;
                const response = await axios.post(route('cart.update-quantity'), {
                    item_id: itemId,
                    quantity: quantity,
                });

                if (response.data.success) {
                    await fetchCart();
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                }

                return response.data;
            } catch (error) {
                console.error('Erreur lors de la mise à jour de la quantité:', error);
                throw error;
            } finally {
                state.isLoading = false;
            }
        }

        async function removeItem(itemId: number) {
            if (state.isLoading) return;

            try {
                state.isLoading = true;
                const response = await axios.post(route('cart.remove-item'), {
                    item_id: itemId,
                });

                if (response.data.success) {
                    await fetchCart();
                    window.dispatchEvent(new CustomEvent('cart-updated'));
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
            state.subtotal = 0;
            state.total = 0;
            state.discount_total = 0;
            state.promotion_code = null;
            state.promotion_discount = 0;
            state.isLoading = false;
        }

        return {
            ...toRefs(state),
            fetchCart,
            addItem,
            updateQuantity,
            removeItem,
            clear,
        };
    },
    {
        persist: {
            key: 'cart-store',
            storage: localStorage,
            paths: ['items', 'items_count', 'subtotal', 'total', 'discount_total', 'promotion_code', 'promotion_discount'],
        },
    },
);
