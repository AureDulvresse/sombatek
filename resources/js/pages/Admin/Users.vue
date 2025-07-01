<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card } from '@/components/ui/card';
import { ref, computed } from 'vue';
import { MoreVertical } from 'lucide-vue-next';

defineProps<{ users: any }>();
const page = usePage();
const user = page.props.auth.user;

const filterRole = ref('all');
const filterStatus = ref('all');

const filteredUsers = computed(() => {
    return users.data.filter((u: any) => {
        const roleMatch = filterRole.value === 'all' || u.role === filterRole.value;
        const statusMatch = filterStatus.value === 'all' || (u.is_active ? 'active' : 'inactive') === filterStatus.value;
        return roleMatch && statusMatch;
    });
});

const handleDelete = (id: number) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        router.delete(route('users.destroy', id));
    }
};

const handleToggle = (id: number) => {
    router.post(route('users.toggle-status', id));
};

const canEdit = (target: any) => {
    // L'admin peut tout éditer sauf lui-même
    if (user.role === 'admin') return user.id !== target.id;
    // Un shop peut éditer ses propres infos (exemple)
    if (user.role === 'shop') return user.id === target.id;
    // Un promoteur peut éditer ses infos
    if (user.role === 'promoter') return user.id === target.id;
    return false;
};
const canDelete = (target: any) => {
    // L'admin ne peut pas se supprimer lui-même
    if (user.role === 'admin') return user.id !== target.id;
    return false;
};
const canToggle = (target: any) => {
    // Seul l'admin peut activer/désactiver
    return user.role === 'admin' && user.id !== target.id;
};
</script>

<template>

    <Head title="Gestion des utilisateurs" />
    <AppLayout>
        <div class="container mx-auto py-8">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
                <h1 class="text-3xl font-bold">Utilisateurs</h1>
                <div class="flex gap-2 items-center">
                    <!-- Filtres rapides -->
                    <select v-model="filterRole" class="rounded border px-2 py-1">
                        <option value="all">Tous rôles</option>
                        <option value="admin">Admin</option>
                        <option value="shop">Boutique</option>
                        <option value="promoter">Démarcheur</option>
                        <option value="customer">Client</option>
                    </select>
                    <select v-model="filterStatus" class="rounded border px-2 py-1">
                        <option value="all">Tous statuts</option>
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
                    <Link :href="route('users.create')"
                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
                    Ajouter un utilisateur
                    </Link>
                </div>
            </div>
            <div v-if="filteredUsers.length === 0" class="text-center text-gray-500 py-12">
                Aucun utilisateur trouvé.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="u in filteredUsers" :key="u.id">
                    <div class="flex items-center gap-4 mb-4">
                        <img :src="u.avatar || '/log.png'" class="h-12 w-12 rounded-full object-cover" />
                        <div>
                            <div class="font-bold flex items-center gap-2">
                                {{ u.name }}
                                <!-- Badge de statut -->
                                <span v-if="u.is_active"
                                    class="ml-2 rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs font-semibold">Actif</span>
                                <span v-else
                                    class="ml-2 rounded-full bg-gray-200 text-gray-600 px-2 py-0.5 text-xs font-semibold">Inactif</span>
                            </div>
                            <div class="text-sm text-gray-500">{{ u.email }}</div>
                            <div class="text-xs text-gray-400">Rôle : {{ u.role }}</div>
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
                                    <DropdownMenuItem v-if="canEdit(u)" as-child>
                                        <Link :href="route('users.edit', u.id)">Éditer</Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem v-if="canDelete(u)" as-child>
                                        <button @click="handleDelete(u.id)">Supprimer</button>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem v-if="canToggle(u)" as-child>
                                        <button @click="handleToggle(u.id)">{{ u.is_active ? 'Désactiver' : 'Activer'
                                            }}</button>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </div>
                </Card>
            </div>
            <!-- Pagination -->
            <div v-if="users.links && users.links.length > 1" class="flex justify-center mt-8 gap-2">
                <Link v-for="link in users.links" :key="link.label" :href="link.url || '#'"
                    :class="[link.active ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700', 'px-3 py-1 rounded']">
                {{ link.label.replace(/(<([^>]+)>)/gi, '') }}
                    </Link>
            </div>
        </div>
    </AppLayout>
</template>
