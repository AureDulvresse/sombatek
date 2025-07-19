<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Les produits sont passés en props via Inertia
const props = defineProps<{ products: any[] }>();
const products = ref(props.products);
// const user = usePage().props.auth.user;

const handleDelete = (id: number) => {
    if (confirm('Supprimer ce produit ?')) {
        router.delete(route('products.destroy', id));
    }
};
</script>

<template>
    <Head title="Mes produits" />
    <AppLayout>
        <div class="container mx-auto py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Mes produits</h1>
                <Link :href="route('products.create')" class="bg-primary text-white px-4 py-2 rounded-lg">Ajouter un produit</Link>
            </div>
            <div v-if="products.length === 0" class="text-center text-gray-500 py-12">
                Aucun produit trouvé.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="p in products" :key="p.id" class="bg-white rounded-lg shadow p-4 flex flex-col gap-2">
                    <div class="font-bold text-lg">{{ p.name }}</div>
                    <div class="text-sm text-gray-500">Prix : {{ p.price }} €</div>
                    <div class="text-sm text-gray-500">Stock : <span :class="p.stock < 5 ? 'text-red-600 font-bold' : ''">{{ p.stock }}</span></div>
                    <div v-if="p.promotion" class="text-xs text-green-600 font-semibold">Promotion : -{{ p.promotion.discount }}% jusqu'au {{ p.promotion.ends_at }}</div>
                    <div class="flex gap-2 mt-2">
                        <Link :href="route('products.edit', p.id)" class="text-blue-600 hover:underline">Éditer</Link>
                        <button @click="handleDelete(p.id)" class="text-red-600 hover:underline">Supprimer</button>
                        <span v-if="p.status === 'inactive'" class="ml-2 text-xs text-gray-400">(Inactif)</span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
