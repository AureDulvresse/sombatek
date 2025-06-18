<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <h1 class="mb-8 text-3xl font-bold">Paramètres du compte</h1>

            <div class="space-y-6">
                <!-- Informations de connexion -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-xl font-bold">Informations de connexion</h2>
                    <form @submit.prevent="updatePassword" class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Mot de passe actuel</label>
                            <input type="password" v-model="passwordForm.currentPassword" required class="w-full rounded-lg border p-2" />
                            <p v-if="errors.currentPassword" class="mt-1 text-sm text-red-500">
                                {{ errors.currentPassword }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Nouveau mot de passe</label>
                            <input type="password" v-model="passwordForm.newPassword" required class="w-full rounded-lg border p-2" />
                            <p v-if="errors.newPassword" class="mt-1 text-sm text-red-500">
                                {{ errors.newPassword }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Confirmer le nouveau mot de passe</label>
                            <input type="password" v-model="passwordForm.confirmPassword" required class="w-full rounded-lg border p-2" />
                            <p v-if="errors.confirmPassword" class="mt-1 text-sm text-red-500">
                                {{ errors.confirmPassword }}
                            </p>
                        </div>

                        <button type="submit" class="hover:bg-primary-dark rounded-lg bg-primary px-6 py-2 text-white">
                            Mettre à jour le mot de passe
                        </button>
                    </form>
                </div>

                <!-- Authentification à deux facteurs -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold">Authentification à deux facteurs</h2>
                            <p class="mt-1 text-sm text-gray-600">Ajoutez une couche de sécurité supplémentaire à votre compte</p>
                        </div>
                        <button
                            @click="toggle2FA"
                            :class="[
                                'rounded-lg px-4 py-2',
                                user.two_factor_enabled ? 'bg-red-500 text-white hover:bg-red-600' : 'hover:bg-primary-dark bg-primary text-white',
                            ]"
                        >
                            {{ user.two_factor_enabled ? 'Désactiver' : 'Activer' }}
                        </button>
                    </div>
                </div>

                <!-- Confidentialité -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-xl font-bold">Confidentialité</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Profil public</h3>
                                <p class="text-sm text-gray-600">Rendre votre profil visible pour les autres utilisateurs</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="privacySettings.publicProfile" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Historique des commandes</h3>
                                <p class="text-sm text-gray-600">Partager votre historique d'achats avec les vendeurs</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="privacySettings.shareOrderHistory" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Suppression du compte -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-bold text-red-600">Zone dangereuse</h2>
                    <p class="mb-4 text-gray-600">
                        La suppression de votre compte est irréversible. Toutes vos données seront définitivement supprimées.
                    </p>
                    <button @click="confirmDeleteAccount" class="rounded-lg border border-red-500 px-6 py-2 text-red-500 hover:bg-red-50">
                        Supprimer mon compte
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    user: {
        two_factor_enabled: boolean;
    };
}>();

const passwordForm = ref({
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
});

const privacySettings = ref({
    publicProfile: false,
    shareOrderHistory: false,
});

const errors = ref<Record<string, string>>({});

const updatePassword = () => {
    router.put(route('password.update'), passwordForm.value, {
        onError: (err) => {
            errors.value = err;
        },
        onSuccess: () => {
            passwordForm.value = {
                currentPassword: '',
                newPassword: '',
                confirmPassword: '',
            };
        },
    });
};

const toggle2FA = () => {
    if (props.user.two_factor_enabled) {
        if (confirm("Êtes-vous sûr de vouloir désactiver l'authentification à deux facteurs ?")) {
            router.delete(route('two-factor.disable'));
        }
    } else {
        router.get(route('two-factor.enable'));
    }
};

const confirmDeleteAccount = () => {
    if (confirm('Êtes-vous absolument sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
        router.delete(route('profile.destroy'));
    }
};
</script>
