<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="mb-8 text-3xl font-bold">Paiement</h1>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Formulaire de livraison et paiement -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Adresse de livraison -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold">Adresse de livraison</h2>
                    <form @submit.prevent="submitOrder" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium">Prénom</label>
                                <input type="text" v-model="form.firstName" required class="w-full rounded-lg border p-2" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Nom</label>
                                <input type="text" v-model="form.lastName" required class="w-full rounded-lg border p-2" />
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Adresse</label>
                            <input type="text" v-model="form.address" required class="w-full rounded-lg border p-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-2 block text-sm font-medium">Code postal</label>
                                <input type="text" v-model="form.postalCode" required class="w-full rounded-lg border p-2" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Ville</label>
                                <input type="text" v-model="form.city" required class="w-full rounded-lg border p-2" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Pays</label>
                                <select v-model="form.country" required class="w-full rounded-lg border p-2">
                                    <option value="FR">France</option>
                                    <option value="BE">Belgique</option>
                                    <option value="CH">Suisse</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Email</label>
                            <input type="email" v-model="form.email" required class="w-full rounded-lg border p-2" />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Téléphone</label>
                            <input type="tel" v-model="form.phone" required class="w-full rounded-lg border p-2" />
                        </div>

                        <!-- Informations de paiement -->
                        <div class="mt-8">
                            <h2 class="mb-4 text-xl font-bold">Informations de paiement</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Numéro de carte</label>
                                    <input
                                        type="text"
                                        v-model="form.cardNumber"
                                        required
                                        class="w-full rounded-lg border p-2"
                                        placeholder="1234 5678 9012 3456"
                                    />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="mb-2 block text-sm font-medium">Date d'expiration</label>
                                        <input
                                            type="text"
                                            v-model="form.expiryDate"
                                            required
                                            class="w-full rounded-lg border p-2"
                                            placeholder="MM/AA"
                                        />
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-medium">CVV</label>
                                        <input type="text" v-model="form.cvv" required class="w-full rounded-lg border p-2" placeholder="123" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="hover:bg-primary-dark mt-6 w-full rounded-lg bg-primary px-6 py-3 text-white">
                            Payer {{ cart.total }} €
                        </button>
                    </form>
                </div>
            </div>

            <!-- Résumé de la commande -->
            <div class="lg:col-span-1">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold">Résumé de la commande</h2>
                    <div class="space-y-4">
                        <div v-for="item in cart.items" :key="item.id" class="flex items-center gap-4">
                            <img :src="item.product.image" :alt="item.product.name" class="h-16 w-16 rounded-lg object-cover" />
                            <div class="flex-1">
                                <h3 class="font-medium">{{ item.product.name }}</h3>
                                <p class="text-sm text-gray-500">Quantité: {{ item.quantity }}</p>
                            </div>
                            <p class="font-medium">{{ (item.product.price * item.quantity).toFixed(2) }} €</p>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between py-2">
                                <span>Sous-total</span>
                                <span>{{ cart.total }} €</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Livraison</span>
                                <span>Gratuite</span>
                            </div>
                            <div class="flex justify-between border-t pt-2 font-bold">
                                <span>Total</span>
                                <span>{{ cart.total }} €</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    cart: {
        items: Array<{
            id: number;
            quantity: number;
            product: {
                id: number;
                name: string;
                price: number;
                image: string;
            };
        }>;
        total: number;
    };
}>();

const form = ref({
    firstName: '',
    lastName: '',
    address: '',
    postalCode: '',
    city: '',
    country: 'FR',
    email: '',
    phone: '',
    cardNumber: '',
    expiryDate: '',
    cvv: '',
});

const submitOrder = () => {
    router.post(route('orders.store'), form.value);
};
</script>
