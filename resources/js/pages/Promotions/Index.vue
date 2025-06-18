<template>
    <AppLayout title="Promotions">
        <template #header>
            <h2 class="text-xl leading-tight font-semibold text-gray-800">Gestion des promotions</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Liste des promotions</h3>
                        <Link
                            :href="route('promotions.create')"
                            class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase hover:bg-indigo-700"
                        >
                            Nouvelle promotion
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Valeur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Expiration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="promotion in promotions.data" :key="promotion.id">
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                                        {{ promotion.code }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                        {{ promotion.description }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                        {{ promotion.discount_type === 'percentage' ? 'Pourcentage' : 'Montant fixe' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                        {{
                                            promotion.discount_type === 'percentage'
                                                ? `${promotion.discount_value}%`
                                                : `${promotion.discount_value} €`
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'inline-flex rounded-full px-2 text-xs leading-5 font-semibold',
                                                promotion.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
                                            ]"
                                        >
                                            {{ promotion.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                        {{ promotion.expires_at ? formatDate(promotion.expires_at) : 'Sans limite' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        <div class="flex space-x-3">
                                            <Link :href="route('promotions.edit', promotion.id)" class="text-indigo-600 hover:text-indigo-900">
                                                Modifier
                                            </Link>
                                            <button @click="toggleStatus(promotion)" class="text-gray-600 hover:text-gray-900">
                                                {{ promotion.is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <button @click="deletePromotion(promotion)" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination :links="promotions.links" class="mt-6" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    promotions: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const toggleStatus = (promotion) => {
    router.post(route('promotions.toggle', promotion.id));
};

const deletePromotion = (promotion) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')) {
        router.delete(route('promotions.destroy', promotion.id));
    }
};
</script>
