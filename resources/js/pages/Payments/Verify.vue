<template>
    <AppLayout title="Vérification du paiement">
        <template #header>
            <h2 class="text-xl leading-tight font-semibold text-gray-800">Vérification du paiement</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg">
                    <div class="text-center">
                        <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-yellow-100 p-2">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900">Action requise</h3>
                        <p class="mb-6 text-gray-500">
                            Une action supplémentaire est nécessaire pour compléter votre paiement. Veuillez suivre les instructions ci-dessous.
                        </p>

                        <div class="mx-auto max-w-md">
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
                                            <span v-else>Confirmer le paiement</span>
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
    paymentIntent: String,
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
    payment_intent: props.paymentIntent,
});

const submit = async () => {
    processing.value = true;
    error.value = '';

    try {
        const { error: stripeError } = await stripe.confirmCardPayment(props.paymentIntent, {
            payment_method: {
                card: cardElement.value,
            },
        });

        if (stripeError) {
            error.value = stripeError.message;
            processing.value = false;
            return;
        }

        form.post(route('payment.confirm', props.order.id));
    } catch (error: any) {
        error.value = 'Une erreur est survenue lors de la confirmation du paiement.';
        processing.value = false;
    }
};

const cancel = () => {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        form.delete(route('payment.cancel', props.order.id));
    }
};
</script>
