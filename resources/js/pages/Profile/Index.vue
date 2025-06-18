<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <h1 class="mb-8 text-3xl font-bold">Mon Profil</h1>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Menu latéral -->
                <div class="lg:col-span-1">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <nav class="space-y-2">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="currentTab = tab.id"
                                :class="[
                                    'w-full rounded-lg px-4 py-2 text-left',
                                    currentTab === tab.id ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100',
                                ]"
                            >
                                {{ tab.name }}
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="lg:col-span-2">
                    <!-- Informations personnelles -->
                    <div v-if="currentTab === 'personal'" class="rounded-lg bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-xl font-bold">Informations personnelles</h2>
                        <form @submit.prevent="updateProfile" class="space-y-4">
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
                                <label class="mb-2 block text-sm font-medium">Email</label>
                                <input type="email" v-model="form.email" required class="w-full rounded-lg border p-2" />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">Téléphone</label>
                                <input type="tel" v-model="form.phone" class="w-full rounded-lg border p-2" />
                            </div>

                            <button type="submit" class="hover:bg-primary-dark rounded-lg bg-primary px-6 py-2 text-white">
                                Enregistrer les modifications
                            </button>
                        </form>
                    </div>

                    <!-- Adresses -->
                    <div v-if="currentTab === 'addresses'" class="space-y-6">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-xl font-bold">Mes adresses</h2>
                                <button @click="showAddressForm = true" class="hover:bg-primary-dark rounded-lg bg-primary px-4 py-2 text-white">
                                    Ajouter une adresse
                                </button>
                            </div>

                            <div v-if="addresses.length === 0" class="py-8 text-center">
                                <p class="text-gray-500">Vous n'avez pas encore d'adresse enregistrée</p>
                            </div>

                            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div v-for="address in addresses" :key="address.id" class="rounded-lg border p-4">
                                    <div class="mb-4">
                                        <p class="font-medium">{{ address.firstName }} {{ address.lastName }}</p>
                                        <p>{{ address.address }}</p>
                                        <p>{{ address.postalCode }} {{ address.city }}</p>
                                        <p>{{ address.country }}</p>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button @click="editAddress(address)" class="hover:text-primary-dark text-primary">Modifier</button>
                                        <button @click="deleteAddress(address.id)" class="text-red-500 hover:text-red-700">Supprimer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commandes -->
                    <div v-if="currentTab === 'orders'" class="rounded-lg bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-xl font-bold">Mes commandes</h2>
                        <div v-if="orders.length === 0" class="py-8 text-center">
                            <p class="text-gray-500">Vous n'avez pas encore de commande</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="order in orders" :key="order.id" class="rounded-lg border p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium">Commande #{{ order.id }}</p>
                                        <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">{{ order.total }} €</p>
                                        <p class="text-sm text-gray-500">{{ order.status }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <Link :href="route('orders.show', order.id)" class="hover:text-primary-dark text-primary">
                                        Voir les détails
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    user: {
        firstName: string;
        lastName: string;
        email: string;
        phone: string;
    };
    addresses: Array<{
        id: number;
        firstName: string;
        lastName: string;
        address: string;
        postalCode: string;
        city: string;
        country: string;
    }>;
    orders: Array<{
        id: number;
        created_at: string;
        total: number;
        status: string;
    }>;
}>();

const tabs = [
    { id: 'personal', name: 'Informations personnelles' },
    { id: 'addresses', name: 'Adresses' },
    { id: 'orders', name: 'Commandes' },
];

const currentTab = ref('personal');
const showAddressForm = ref(false);

const form = ref({
    firstName: props.user.firstName,
    lastName: props.user.lastName,
    email: props.user.email,
    phone: props.user.phone,
});

const updateProfile = () => {
    router.put(route('profile.update'), form.value);
};

const editAddress = (address: any) => {
    // Logique pour éditer une adresse
};

const deleteAddress = (id: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?')) {
        router.delete(route('addresses.destroy', id));
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>
