<template>
    <div class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black">
        <div class="w-full max-w-lg rounded-lg bg-white p-6">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-bold">
                    {{ address ? "Modifier l'adresse" : 'Ajouter une adresse' }}
                </h2>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitForm" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Prénom</label>
                        <input type="text" v-model="form.firstName" required class="w-full rounded-lg border p-2" />
                        <p v-if="errors.firstName" class="mt-1 text-sm text-red-500">
                            {{ errors.firstName }}
                        </p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Nom</label>
                        <input type="text" v-model="form.lastName" required class="w-full rounded-lg border p-2" />
                        <p v-if="errors.lastName" class="mt-1 text-sm text-red-500">
                            {{ errors.lastName }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Adresse</label>
                    <input type="text" v-model="form.address" required class="w-full rounded-lg border p-2" />
                    <p v-if="errors.address" class="mt-1 text-sm text-red-500">
                        {{ errors.address }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Code postal</label>
                        <input type="text" v-model="form.postalCode" required class="w-full rounded-lg border p-2" />
                        <p v-if="errors.postalCode" class="mt-1 text-sm text-red-500">
                            {{ errors.postalCode }}
                        </p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Ville</label>
                        <input type="text" v-model="form.city" required class="w-full rounded-lg border p-2" />
                        <p v-if="errors.city" class="mt-1 text-sm text-red-500">
                            {{ errors.city }}
                        </p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Pays</label>
                        <select v-model="form.country" required class="w-full rounded-lg border p-2">
                            <option value="FR">France</option>
                            <option value="BE">Belgique</option>
                            <option value="CH">Suisse</option>
                        </select>
                        <p v-if="errors.country" class="mt-1 text-sm text-red-500">
                            {{ errors.country }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Téléphone</label>
                    <input type="tel" v-model="form.phone" class="w-full rounded-lg border p-2" />
                    <p v-if="errors.phone" class="mt-1 text-sm text-red-500">
                        {{ errors.phone }}
                    </p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" v-model="form.isDefault" id="default" class="rounded border-gray-300 text-primary" />
                    <label for="default" class="ml-2 text-sm text-gray-600"> Définir comme adresse par défaut </label>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <button type="button" @click="$emit('close')" class="rounded-lg border px-6 py-2 text-gray-600 hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="hover:bg-primary-dark rounded-lg bg-primary px-6 py-2 text-white">
                        {{ address ? 'Modifier' : 'Ajouter' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    address?: {
        id: number;
        firstName: string;
        lastName: string;
        address: string;
        postalCode: string;
        city: string;
        country: string;
        phone: string;
        isDefault: boolean;
    };
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = ref({
    firstName: '',
    lastName: '',
    address: '',
    postalCode: '',
    city: '',
    country: 'FR',
    phone: '',
    isDefault: false,
});

const errors = ref<Record<string, string>>({});

onMounted(() => {
    if (props.address) {
        form.value = { ...props.address };
    }
});

const submitForm = () => {
    if (props.address) {
        router.put(route('addresses.update', props.address.id), form.value, {
            onError: (err) => {
                errors.value = err;
            },
            onSuccess: () => {
                emit('close');
            },
        });
    } else {
        router.post(route('addresses.store'), form.value, {
            onError: (err) => {
                errors.value = err;
            },
            onSuccess: () => {
                emit('close');
            },
        });
    }
};
</script>
