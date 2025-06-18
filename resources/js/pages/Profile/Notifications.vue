<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <h1 class="mb-8 text-3xl font-bold">Préférences de notification</h1>

            <div class="space-y-6">
                <!-- Notifications par email -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-xl font-bold">Notifications par email</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Commandes</h3>
                                <p class="text-sm text-gray-600">Recevoir des mises à jour sur vos commandes</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="emailPreferences.orders" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Promotions</h3>
                                <p class="text-sm text-gray-600">Recevoir des offres spéciales et des réductions</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="emailPreferences.promotions" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Nouveaux produits</h3>
                                <p class="text-sm text-gray-600">Être informé des nouveaux produits disponibles</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="emailPreferences.newProducts" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notifications push -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-xl font-bold">Notifications push</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Statut des commandes</h3>
                                <p class="text-sm text-gray-600">Recevoir des notifications sur l'état de vos commandes</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="pushPreferences.orderStatus" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Livraisons</h3>
                                <p class="text-sm text-gray-600">Être informé des mises à jour de livraison</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="pushPreferences.delivery" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">Offres spéciales</h3>
                                <p class="text-sm text-gray-600">Recevoir des notifications sur les promotions</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" v-model="pushPreferences.specialOffers" class="peer sr-only" />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                                ></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Fréquence des notifications -->
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-xl font-bold">Fréquence des notifications</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Résumé quotidien</label>
                            <select v-model="notificationPreferences.dailyDigest" class="w-full rounded-lg border p-2">
                                <option value="never">Jamais</option>
                                <option value="daily">Une fois par jour</option>
                                <option value="twice">Deux fois par jour</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">Résumé hebdomadaire</label>
                            <select v-model="notificationPreferences.weeklyDigest" class="w-full rounded-lg border p-2">
                                <option value="never">Jamais</option>
                                <option value="monday">Lundi</option>
                                <option value="friday">Vendredi</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="savePreferences" class="hover:bg-primary-dark rounded-lg bg-primary px-6 py-2 text-white">
                        Enregistrer les préférences
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const emailPreferences = ref({
    orders: true,
    promotions: false,
    newProducts: false,
});

const pushPreferences = ref({
    orderStatus: true,
    delivery: true,
    specialOffers: false,
});

const notificationPreferences = ref({
    dailyDigest: 'never',
    weeklyDigest: 'friday',
});

const savePreferences = () => {
    router.put(route('notifications.update'), {
        email: emailPreferences.value,
        push: pushPreferences.value,
        frequency: notificationPreferences.value,
    });
};
</script>
