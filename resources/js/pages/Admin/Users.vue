<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card } from '@/components/ui/card';

defineProps<{ users: any }>();
const page = usePage();
const user = page.props.auth.user;

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
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Utilisateurs</h1>
        <Link :href="route('users.create')" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
          Ajouter un utilisateur
        </Link>
      </div>
      <div v-if="users.data.length === 0" class="text-center text-gray-500 py-12">
        Aucun utilisateur trouvé.
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="u in users.data" :key="u.id">
          <div class="flex items-center gap-4 mb-4">
            <img :src="u.avatar || '/log.png'" class="h-12 w-12 rounded-full object-cover" />
            <div>
              <div class="font-bold">{{ u.name }}</div>
              <div class="text-sm text-gray-500">{{ u.email }}</div>
              <div class="text-xs text-gray-400">Rôle : {{ u.role }}</div>
            </div>
          </div>
          <div class="flex gap-2 mt-2">
            <Link v-if="canEdit(u)" :href="route('users.edit', u.id)" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200">Éditer</Link>
            <button v-if="canDelete(u)" @click="handleDelete(u.id)" class="px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200">Supprimer</button>
            <button v-if="canToggle(u)" @click="handleToggle(u.id)" :class="u.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-3 py-1 rounded">
              {{ u.is_active ? 'Désactiver' : 'Activer' }}
            </button>
          </div>
        </Card>
      </div>
      <!-- Pagination -->
      <div v-if="users.links && users.links.length > 1" class="flex justify-center mt-8 gap-2">
        <Link v-for="link in users.links" :key="link.label" :href="link.url || '#'" :class="[link.active ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700', 'px-3 py-1 rounded']">
          {{ link.label.replace(/(<([^>]+)>)/gi, '') }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
