<template>
    <AppLayout title="Paiement">
        <template #header>
            <h2 class="text-xl leading-tight font-semibold text-gray-800">Paiement de la commande #{{ order.id }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <!-- Résumé de la commande -->
                        <div>
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Résumé de la commande</h3>
                            <div class="space-y-4">
                                <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4">
                                    <img :src="item.product.image" :alt="item.product.name" class="h-16 w-16 rounded-lg object-cover" />
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ item.product.name }}</h4>
                                        <p class="text-sm text-gray-500">{{ item.shop.name }}</p>
                                        <p class="text-sm text-gray-500">Quantité: {{ item.quantity }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">{{ formatPrice(item.total) }}</p>
                                </div>
                            </div>

                            <div class="mt-6 border-t border-gray-200 pt-6">
                                <div class="flex justify-between">
                                    <p class="text-base font-medium text-gray-900">Total</p>
                                    <p class="text-base font-medium text-gray-900">{{ formatPrice(order.total) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire de paiement -->
                        <div>
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Informations de paiement</h3>
                            <form @submit.prevent="submit">
                                <div class="space-y-4">
                                    <!-- Carte de crédit -->
                                    <div>
                                        <label for="card-element" class="block text-sm font-medium text-gray-700"> Carte de crédit </label>
                                        <div
                                            id="card-element"
                                            ref="cardElement"
                                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                        ></div>
                                        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
                                    </div>

                                    <!-- Boutons d'action -->
                                    <div class="flex items-center justify-between pt-6">
                                        <button
                                            type="button"
                                            @click="cancel"
                                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:outline-none"
                                        >
                                            Annuler
                                        </button>
                                        <button
                                            type="submit"
                                            :disabled="processing"
                                            class="inline-flex items-center rounded-md border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                                        >
                                            <span v-if="processing">Traitement en cours...</span>
                                            <span v-else>Payer {{ formatPrice(order.total) }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { loadStripe } from '@stripe/stripe-js';
import { onMounted, ref } from 'vue';

const props = defineProps({
    order: Object,
    intent: Object,
});

const stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);
const elements = stripe.elements();
const cardElement = ref(null);
const error = ref('');
const processing = ref(false);

onMounted(() => {
    const card = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });

    card.mount(cardElement.value);
    card.on('change', (event) => {
        error.value = event.error ? event.error.message : '';
    });
});

const form = useForm({
    payment_method: '',
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(price);
};

const submit = async () => {
    processing.value = true;
    error.value = '';

    try {
        const { setupIntent, error: stripeError } = await stripe.confirmCardSetup(props.intent.client_secret, {
            payment_method: {
                card: cardElement.value,
                billing_details: {
                    name: props.order.user.name,
                    email: props.order.user.email,
                },
            },
        });

        if (stripeError) {
            error.value = stripeError.message;
            processing.value = false;
            return;
        }

        form.payment_method = setupIntent.payment_method;
        form.post(route('payment.process', props.order.id));
    } catch (e: any) {
        error.value = 'Une erreur est survenue lors du traitement du paiement.';
        processing.value = false;
    }
};

const cancel = () => {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        form.delete(route('payment.cancel', props.order.id));
    }
};
</script>
