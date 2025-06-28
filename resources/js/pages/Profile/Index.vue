<template>
    <SiteLayout title="Profile">
        <!-- Bannière de page -->
        <PageBanner title="Nos Produits" description="Découvrez notre sélection de produits de qualité"
            :breadcrumbs="breadcrumbs" />
        <div class="container mx-auto px-4 py-8">
            <div class="mx-auto max-w-4xl">
                <h1 class="mb-8 text-3xl font-bold">Mon Profil</h1>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Menu latéral -->
                    <div class="lg:col-span-1">
                        <div class="rounded-lg bg-white p-6 shadow-sm">
                            <nav class="space-y-2">
                                <button v-for="tab in tabs" :key="tab.id" @click="currentTab = tab.id" :class="[
                                    'w-full rounded-lg px-4 py-2 text-left',
                                    currentTab === tab.id ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100',
                                ]">
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
                                        <input type="text" v-model="form.firstName" required
                                            class="w-full rounded-lg border p-2" />
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-medium">Nom</label>
                                        <input type="text" v-model="form.lastName" required
                                            class="w-full rounded-lg border p-2" />
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium">Email</label>
                                    <input type="email" v-model="form.email" required
                                        class="w-full rounded-lg border p-2" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium">Téléphone</label>
                                    <input type="tel" v-model="form.phone" class="w-full rounded-lg border p-2" />
                                </div>

                                <button type="submit"
                                    class="hover:bg-primary-dark rounded-lg bg-primary px-6 py-2 text-white">
                                    Enregistrer les modifications
                                </button>
                            </form>
                        </div>

                        <!-- Adresses -->
                        <div v-if="currentTab === 'addresses'" class="space-y-6">
                            <div class="rounded-lg bg-white p-6 shadow-sm">
                                <div class="mb-6 flex items-center justify-between">
                                    <h2 class="text-xl font-bold">Mes adresses</h2>
                                    <button @click="showAddressForm = true"
                                        class="hover:bg-primary-dark rounded-lg bg-primary px-4 py-2 text-white">
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
                                            <button @click="editAddress(address)"
                                                class="hover:text-primary-dark text-primary">Modifier</button>
                                            <button @click="deleteAddress(address.id)"
                                                class="text-red-500 hover:text-red-700">Supprimer</button>
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
                                        <Link :href="route('orders.show', order.id)"
                                            class="hover:text-primary-dark text-primary">
                                        Voir les détails
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutique : Ma boutique -->
                        <div v-if="currentTab === 'shop' && user.role === 'shop'"
                            class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold">Ma boutique</h2>
                            <div v-if="shop">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <div class="text-xl font-bold">{{ shopStats.total_sales }}</div>
                                        <div class="text-gray-500 text-xs">Ventes</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <div class="text-xl font-bold">{{ shopStats.total_orders }}</div>
                                        <div class="text-gray-500 text-xs">Commandes</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <div class="text-xl font-bold">{{ shopStats.total_products }}</div>
                                        <div class="text-gray-500 text-xs">Produits</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <div class="text-xl font-bold">{{ shopStats.rating }} ★</div>
                                        <div class="text-gray-500 text-xs">Note ({{ shopStats.review_count }} avis)
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h3 class="font-semibold mb-2">Derniers produits</h3>
                                        <ul>
                                            <li v-for="product in shopProducts" :key="product.id" class="mb-1">
                                                {{ product.name }} <span class="text-gray-400">- {{ product.price }}
                                                    €</span>
                                            </li>
                                        </ul>
                                        <div v-if="!shopProducts || !shopProducts.length" class="text-gray-400">Aucun
                                            produit récent.</div>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold mb-2">Dernières commandes</h3>
                                        <ul>
                                            <li v-for="order in shopOrders" :key="order.id" class="mb-1">
                                                #{{ order.id }} - {{ order.total }} € - {{ order.status }}
                                            </li>
                                        </ul>
                                        <div v-if="!shopOrders || !shopOrders.length" class="text-gray-400">Aucune
                                            commande récente.</div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <p>Aucune boutique associée.</p>
                            </div>
                        </div>
                        <!-- Boutique : Produits -->
                        <div v-if="currentTab === 'products' && user.role === 'shop'"
                            class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold">Mes produits</h2>
                            <div v-if="shopProducts && shopProducts.length">
                                <ul>
                                    <li v-for="product in shopProducts" :key="product.id">
                                        {{ product.name }} - {{ product.price }} €
                                    </li>
                                </ul>
                            </div>
                            <div v-else>
                                <p>Aucun produit trouvé.</p>
                            </div>
                        </div>
                        <!-- Boutique : Commandes boutique -->
                        <div v-if="currentTab === 'shopOrders' && user.role === 'shop'"
                            class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold">Commandes boutique</h2>
                            <div v-if="shopOrders && shopOrders.length">
                                <ul>
                                    <li v-for="order in shopOrders" :key="order.id">
                                        Commande #{{ order.id }} - {{ order.total }} € - {{ order.status }}
                                    </li>
                                </ul>
                            </div>
                            <div v-else>
                                <p>Aucune commande trouvée.</p>
                            </div>
                        </div>
                        <!-- Boutique : Statistiques -->
                        <div v-if="currentTab === 'shopStats' && user.role === 'shop'"
                            class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold">Statistiques</h2>
                            <div v-if="shopStats">
                                <p><strong>Total ventes :</strong> {{ shopStats.total_sales }}</p>
                                <p><strong>Total commandes :</strong> {{ shopStats.total_orders }}</p>
                                <p><strong>Total produits :</strong> {{ shopStats.total_products }}</p>
                            </div>
                        </div>

                        <!-- Promoteur : Commissions -->
                        <div v-if="currentTab === 'commissions' && user.role === 'promoter'"
                            class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold">Mes commissions</h2>
                            <div v-if="commissions && commissions.length">
                                <ul>
                                    <li v-for="commission in commissions" :key="commission.id">
                                        {{ commission.amount }} € - {{ commission.status }}
                                    </li>
                                </ul>
                            </div>
                            <div v-else>
                                <p>Aucune commission trouvée.</p>
                            </div>
                        </div>
                        <!-- Promoteur : Partenariats -->
                        <div v-if="currentTab === 'partnerships' && user.role === 'promoter'"
                            class="rounded-lg bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold">Mes partenariats</h2>
                            <div v-if="partnerships && partnerships.length">
                                <ul>
                                    <li v-for="partnership in partnerships" :key="partnership.id">
                                        {{ partnership.code }} - {{ partnership.status }}
                                    </li>
                                </ul>
                            </div>
                            <div v-else>
                                <p>Aucun partenariat trouvé.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>

<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
    user: {
        firstName: string;
        lastName: string;
        email: string;
        phone: string;
        role: string;
    };
    addresses: Array<any>;
    orders: Array<any>;
    shop?: any;
    shopProducts?: Array<any>;
    shopOrders?: Array<any>;
    shopStats?: any;
    commissions?: Array<any>;
    partnerships?: Array<any>;
}>();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile',
        href: '/profile',
    },
];

const baseTabs = [
    { id: 'personal', name: 'Informations personnelles' },
    { id: 'addresses', name: 'Adresses' },
    { id: 'orders', name: 'Commandes' },
];

const promoterTabs = [
    { id: 'commissions', name: 'Mes commissions' },
    { id: 'partnerships', name: 'Mes partenariats' },
];

const shopTabs = [
    { id: 'shop', name: 'Ma boutique' },
    { id: 'products', name: 'Mes produits' },
    { id: 'shopOrders', name: 'Commandes boutique' },
    { id: 'shopStats', name: 'Statistiques' },
];

const tabs = computed(() => {
    if (props.user.role === 'shop') {
        return baseTabs.concat(shopTabs);
    } else if (props.user.role === 'promoter') {
        return baseTabs.concat(promoterTabs);
    } else {
        return baseTabs;
    }
});

const currentTab = ref('personal');
const showAddressForm = ref(false);

const form = ref({
    firstName: props.user.firstName,
    lastName: props.user.lastName,
    email: props.user.email,
    phone: props.user.phone,
    address: '',
    postalCode: '',
    city: '',
    country: '',
    id: null as number | null
});

const updateProfile = () => {
    router.put(route('profile.update'), form.value);
};

const editAddress = (address: any) => {
    // Ouvre le formulaire d'édition et pré-remplit les champs avec l'adresse sélectionnée
    showAddressForm.value = true;
    form.value = {
        ...form.value,
        firstName: address.firstName,
        lastName: address.lastName,
        email: form.value.email, // L'email reste celui de l'utilisateur
        phone: form.value.phone, // Le téléphone reste celui de l'utilisateur
        address: address.address,
        postalCode: address.postalCode,
        city: address.city,
        country: address.country,
        id: address.id,
    };
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
