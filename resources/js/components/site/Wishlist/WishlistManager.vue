<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';

interface Wishlist {
    id: number;
    name: string;
    description: string | null;
    is_public: boolean;
    max_items: number | null;
    items: Array<{
        id: number;
        product: {
            id: number;
            name: string;
            price: number;
            image: string;
        };
    }>;
}

const props = defineProps<{
    wishlists: Wishlist[];
}>();

const toast = useToast();
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showShareModal = ref(false);
const selectedWishlist = ref<Wishlist | null>(null);
const shareLink = ref('');

const createForm = useForm({
    name: '',
    description: '',
    is_public: false,
    max_items: null,
});

const editForm = useForm({
    name: '',
    description: '',
    is_public: false,
    max_items: null,
});

const openCreateModal = () => {
    createForm.reset();
    showCreateModal.value = true;
};

const openEditModal = (wishlist: Wishlist) => {
    selectedWishlist.value = wishlist;
    editForm.name = wishlist.name;
    editForm.description = wishlist.description || '';
    editForm.is_public = wishlist.is_public;
    editForm.max_items = wishlist.max_items;
    showEditModal.value = true;
};

const openShareModal = async (wishlist: Wishlist) => {
    selectedWishlist.value = wishlist;
    try {
        const response = await axios.post(route('wishlists.share', wishlist.id));
        shareLink.value = response.data.share_link;
        showShareModal.value = true;
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la génération du lien de partage');
    }
};

const createWishlist = async () => {
    try {
        const response = await axios.post(route('wishlists.store'), createForm.data());
        toast.success('Liste de souhaits créée avec succès');
        showCreateModal.value = false;
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la création de la liste');
    }
};

const updateWishlist = async () => {
    if (!selectedWishlist.value) return;

    try {
        const response = await axios.put(route('wishlists.update', selectedWishlist.value.id), editForm.data());
        toast.success('Liste de souhaits mise à jour avec succès');
        showEditModal.value = false;
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la mise à jour de la liste');
    }
};

const deleteWishlist = async (wishlist: Wishlist) => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette liste ?')) return;

    try {
        await axios.delete(route('wishlists.destroy', wishlist.id));
        toast.success('Liste de souhaits supprimée avec succès');
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la suppression de la liste');
    }
};

const moveToCart = async (wishlist: Wishlist) => {
    try {
        await axios.post(route('wishlists.move-to-cart', wishlist.id));
        toast.success('Articles déplacés vers le panier avec succès');
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors du déplacement vers le panier');
    }
};

const duplicateWishlist = async (wishlist: Wishlist) => {
    try {
        await axios.post(route('wishlists.duplicate', wishlist.id));
        toast.success('Liste de souhaits dupliquée avec succès');
        window.location.reload();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la duplication de la liste');
    }
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mes listes de souhaits</h2>
            <button
                @click="openCreateModal"
                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
                Nouvelle liste
            </button>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="wishlist in wishlists"
                :key="wishlist.id"
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ wishlist.name }}</h3>
                    <div class="flex space-x-2">
                        <button
                            @click="openEditModal(wishlist)"
                            class="rounded-full p-1 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                />
                            </svg>
                        </button>
                        <button
                            @click="deleteWishlist(wishlist)"
                            class="rounded-full p-1 text-red-500 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <p v-if="wishlist.description" class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ wishlist.description }}
                </p>

                <div class="mb-4 flex items-center space-x-2">
                    <span
                        v-if="wishlist.is_public"
                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200"
                    >
                        Public
                    </span>
                    <span
                        v-if="wishlist.max_items"
                        class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                    >
                        Limite : {{ wishlist.max_items }} articles
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ wishlist.items.length }} article(s)</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        @click="openShareModal(wishlist)"
                        class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        Partager
                    </button>
                    <button
                        @click="moveToCart(wishlist)"
                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    >
                        Vers panier
                    </button>
                    <button
                        @click="duplicateWishlist(wishlist)"
                        class="rounded-lg bg-gray-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                    >
                        Dupliquer
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de création -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-opacity-50 fixed inset-0 bg-black"></div>
                <div class="relative w-full max-w-md rounded-lg bg-white p-6 dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Nouvelle liste de souhaits</h3>
                    <form @submit.prevent="createWishlist" class="space-y-4">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Nom </label>
                            <input
                                type="text"
                                id="name"
                                v-model="createForm.name"
                                required
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                            />
                        </div>
                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Description </label>
                            <textarea
                                id="description"
                                v-model="createForm.description"
                                rows="3"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                            ></textarea>
                        </div>
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="is_public"
                                v-model="createForm.is_public"
                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-green-600 focus:ring-2 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-green-600"
                            />
                            <label for="is_public" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"> Liste publique </label>
                        </div>
                        <div>
                            <label for="max_items" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Limite d'articles (optionnel)
                            </label>
                            <input
                                type="number"
                                id="max_items"
                                v-model="createForm.max_items"
                                min="1"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                            />
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                @click="showCreateModal = false"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                            >
                                Créer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal d'édition -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-opacity-50 fixed inset-0 bg-black"></div>
                <div class="relative w-full max-w-md rounded-lg bg-white p-6 dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Modifier la liste</h3>
                    <form @submit.prevent="updateWishlist" class="space-y-4">
                        <div>
                            <label for="edit-name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Nom </label>
                            <input
                                type="text"
                                id="edit-name"
                                v-model="editForm.name"
                                required
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                            />
                        </div>
                        <div>
                            <label for="edit-description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Description </label>
                            <textarea
                                id="edit-description"
                                v-model="editForm.description"
                                rows="3"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                            ></textarea>
                        </div>
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="edit-is_public"
                                v-model="editForm.is_public"
                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-green-600 focus:ring-2 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-green-600"
                            />
                            <label for="edit-is_public" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"> Liste publique </label>
                        </div>
                        <div>
                            <label for="edit-max_items" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                Limite d'articles (optionnel)
                            </label>
                            <input
                                type="number"
                                id="edit-max_items"
                                v-model="editForm.max_items"
                                min="1"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                            />
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                @click="showEditModal = false"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                            >
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de partage -->
        <div v-if="showShareModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-opacity-50 fixed inset-0 bg-black"></div>
                <div class="relative w-full max-w-md rounded-lg bg-white p-6 dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Partager la liste</h3>
                    <div class="mb-4">
                        <label for="share-link" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Lien de partage </label>
                        <div class="flex">
                            <input
                                type="text"
                                id="share-link"
                                v-model="shareLink"
                                readonly
                                class="block w-full rounded-l-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                            />
                            <button
                                @click="navigator.clipboard.writeText(shareLink)"
                                class="rounded-r-lg border border-l-0 border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200 focus:ring-4 focus:ring-gray-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700"
                            >
                                Copier
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button
                            @click="showShareModal = false"
                            class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                        >
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
