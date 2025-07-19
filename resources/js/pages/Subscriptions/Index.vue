<template>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Mes Abonnements</h1>
            <button
                @click="showNewSubscriptionModal = true"
                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
            >
                <PlusIcon class="mr-2 h-5 w-5" />
                Nouvel Abonnement
            </button>
        </div>

        <!-- Liste des abonnements -->
        <div class="overflow-hidden bg-white shadow sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                <li v-for="subscription in subscriptions.data" :key="subscription.id">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img
                                        :src="subscription.items[0].product.image"
                                        :alt="subscription.items[0].product.name"
                                        class="h-12 w-12 rounded-lg object-cover"
                                    />
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ subscription.items[0].product.name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">Quantité: {{ subscription.items[0].quantity }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        {
                                            'bg-green-100 text-green-800': subscription.status === 'active',
                                            'bg-yellow-100 text-yellow-800': subscription.status === 'paused',
                                            'bg-red-100 text-red-800': subscription.status === 'cancelled',
                                        },
                                    ]"
                                >
                                    {{ subscription.status }}
                                </span>
                                <div class="flex space-x-2">
                                    <button @click="editSubscription(subscription)" class="text-indigo-600 hover:text-indigo-900">
                                        <PencilIcon class="h-5 w-5" />
                                    </button>
                                    <button @click="cancelSubscription(subscription)" class="text-red-600 hover:text-red-900">
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    <CalendarIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" />
                                    Fréquence: {{ subscription.frequency }}
                                </p>
                                <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                    <MapPinIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" />
                                    {{ subscription.address.street }}, {{ subscription.address.city }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <ClockIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" />
                                <p>
                                    Prochaine livraison:
                                    {{ formatDate(subscription.next_delivery_date) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <Pagination :links="subscriptions.links" />
        </div>

        <!-- Modal Nouvel Abonnement -->
        <Modal v-if="showNewSubscriptionModal" @close="showNewSubscriptionModal = false">
            <template #title>Nouvel Abonnement</template>
            <template #content>
                <form @submit.prevent="createSubscription" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"> Produit </label>
                        <select
                            v-model="form.product_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"> Quantité </label>
                        <input
                            type="number"
                            v-model="form.quantity"
                            min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"> Fréquence </label>
                        <select
                            v-model="form.frequency"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="weekly">Hebdomadaire</option>
                            <option value="monthly">Mensuelle</option>
                            <option value="quarterly">Trimestrielle</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"> Date de début </label>
                        <input
                            type="date"
                            v-model="form.start_date"
                            :min="minStartDate"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"> Adresse de livraison </label>
                        <select
                            v-model="form.address_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option v-for="address in addresses" :key="address.id" :value="address.id">
                                {{ address.street }}, {{ address.city }}
                            </option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="showNewSubscriptionModal = false"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                            Créer l'abonnement
                        </button>
                    </div>
                </form>
            </template>
        </Modal>
    </div>
</template>

<script setup lang="ts">
// import Modal from '@/components/Modal.vue';
// import Pagination from '@/components/Pagination.vue';
import { CalendarIcon, ClockIcon, MapPinIcon, PencilIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps({
    subscriptions: Object,
    products: Array,
    addresses: Array,
});

const showNewSubscriptionModal = ref(false);

const form = useForm({
    product_id: '',
    quantity: 1,
    frequency: 'monthly',
    start_date: '',
    address_id: '',
});

const minStartDate = computed(() => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const createSubscription = () => {
    form.post(route('subscriptions.store'), {
        onSuccess: () => {
            showNewSubscriptionModal.value = false;
            form.reset();
        },
    });
};

// TODO - Implement edit functionality
// const editSubscription = (subscription : any) => {
//     // Implémenter la logique d'édition
// };

const cancelSubscription = (subscription: any) => {
    if (confirm('Êtes-vous sûr de vouloir annuler cet abonnement ?')) {
        router.delete(route('subscriptions.destroy', subscription.id));
    }
};
</script>
