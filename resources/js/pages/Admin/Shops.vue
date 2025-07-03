<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card } from '@/components/ui/card';
import { ref, computed } from 'vue';
import { MoreVertical } from 'lucide-vue-next';

const props = defineProps<{ shops: any }>();
const shops = props.shops;
const page = usePage();
const user = page.props.auth.user;

const filterStatus = ref('all');
const filteredShops = computed(() => {
    return shops.data.filter((s: any) => {
        return filterStatus.value === 'all' || s.status === filterStatus.value;
    });
});

const handleDelete = (id: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette boutique ?')) {
        router.delete(route('shops.destroy', id));
    }
};
const handleApprove = (id: number) => {
    router.post(route('shops.approve', id));
};
const handleReject = (id: number) => {
    router.post(route('shops.reject', id));
};
const canEdit = (shop: any) => {
    if (user.role === 'admin') return true;
    if (user.role === 'shop') return user.id === shop.user_id;
    return false;
};
const canDelete = () => {
    return user.role === 'admin';
};
const canApprove = (shop: any) => {
    return user.role === 'admin' && shop.status === 'pending';
};
const canReject = (shop: any) => {
    return user.role === 'admin' && shop.status === 'pending';
};
</script>

<template>

    <Head title="Gestion des boutiques" />
    <AppLayout>
        <div class="container mx-auto py-8">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
                <h1 class="text-3xl font-bold">Boutiques</h1>
                <div class="flex gap-2 items-center">
                    <!-- Filtres rapides -->
                    <select v-model="filterStatus" class="rounded border px-2 py-1">
                        <option value="all">Tous statuts</option>
                        <option value="pending">En attente</option>
                        <option value="active">Active</option>
                        <option value="rejected">Rejetée</option>
                    </select>
                    <Link v-if="user.role === 'admin'" :href="route('shops.create')"
                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
                    Ajouter une boutique
                    </Link>
                </div>
            </div>
            <div v-if="filteredShops.length === 0" class="text-center text-gray-500 py-12">
                Aucune boutique trouvée.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="s in filteredShops" :key="s.id">
                    <div class="flex items-center gap-4 mb-4">
                        <img :src="s.image || '/log.png'" class="h-12 w-12 rounded-full object-cover" />
                        <div>
                            <div class="font-bold flex items-center gap-2">
                                {{ s.name }}
                                <!-- Badge de statut -->
                                <span v-if="s.status === 'pending'"
                                    class="ml-2 rounded-full bg-yellow-100 text-yellow-700 px-2 py-0.5 text-xs font-semibold">En
                                    attente</span>
                                <span v-else-if="s.status === 'active'"
                                    class="ml-2 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-semibold">Active</span>
                                <span v-else
                                    class="ml-2 rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-xs font-semibold">Rejetée</span>
                            </div>
                            <div class="text-sm text-gray-500">{{ s.email }}</div>
                            <div class="text-xs text-gray-400">Responsable : {{ s.user?.name || 'N/A' }}</div>
                        </div>
                        <!-- Menu contextuel -->
                        <div class="ml-auto relative">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <button class="p-2 rounded-full hover:bg-gray-100">
                                        <MoreVertical class="h-5 w-5" />
                                    </button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem v-if="canEdit(s)" as-child>
                                        <Link :href="route('shops.edit', s.id)">Éditer</Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem v-if="canDelete(s)" as-child>
                                        <button @click="handleDelete(s.id)">Supprimer</button>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem v-if="canApprove(s)" as-child>
                                        <button @click="handleApprove(s.id)">Approuver</button>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem v-if="canReject(s)" as-child>
                                        <button @click="handleReject(s.id)">Rejeter</button>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                        <Link :href="route('shops.show', s.slug || s.id)" class="text-green-600 hover:underline ml-2"
                            target="_blank">
                        Voir le profil public
                        </Link>
                    </div>
                </Card>
            </div>
            <!-- Pagination -->
            <div v-if="shops.links && shops.links.length > 1" class="flex justify-center mt-8 gap-2">
                <Link v-for="link in shops.links" :key="link.label" :href="link.url || '#'"
                    :class="[link.active ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700', 'px-3 py-1 rounded']">
                {{ link.label.replace(/(<([^>]+)>)/gi, '') }}
                    </Link>
            </div>
        </div>
    </AppLayout>
</template>
