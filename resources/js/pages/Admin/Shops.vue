<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card } from '@/components/ui/card';

defineProps<{ shops: any }>();
const page = usePage();
const user = page.props.auth.user;

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
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Boutiques</h1>
        <Link v-if="user.role === 'admin'" :href="route('shops.create')" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
          Ajouter une boutique
        </Link>
      </div>
      <div v-if="shops.data.length === 0" class="text-center text-gray-500 py-12">
        Aucune boutique trouvée.
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="s in shops.data" :key="s.id">
          <div class="flex items-center gap-4 mb-4">
            <img :src="s.image || '/log.png'" class="h-12 w-12 rounded-full object-cover" />
            <div>
              <div class="font-bold">{{ s.name }}</div>
              <div class="text-sm text-gray-500">{{ s.email }}</div>
              <div class="text-xs text-gray-400">Responsable : {{ s.user?.name || 'N/A' }}</div>
              <div class="text-xs" :class="{
                'text-yellow-600': s.status === 'pending',
                'text-green-600': s.status === 'active',
                'text-red-600': s.status === 'rejected',
              }">Statut : {{ s.status }}</div>
            </div>
          </div>
          <div class="flex gap-2 mt-2">
            <Link v-if="canEdit(s)" :href="route('shops.edit', s.id)" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200">Éditer</Link>
            <button v-if="canDelete(s)" @click="handleDelete(s.id)" class="px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200">Supprimer</button>
            <button v-if="canApprove(s)" @click="handleApprove(s.id)" class="px-3 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200">Approuver</button>
            <button v-if="canReject(s)" @click="handleReject(s.id)" class="px-3 py-1 rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200">Rejeter</button>
          </div>
        </Card>
      </div>
      <!-- Pagination -->
      <div v-if="shops.links && shops.links.length > 1" class="flex justify-center mt-8 gap-2">
        <Link v-for="link in shops.links" :key="link.label" :href="link.url || '#'" :class="[link.active ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700', 'px-3 py-1 rounded']">
          {{ link.label.replace(/(<([^>]+)>)/gi, '') }}
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
