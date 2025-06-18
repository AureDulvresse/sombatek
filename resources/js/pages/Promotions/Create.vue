<template>
    <AppLayout title="Nouvelle promotion">
        <template #header>
            <h2 class="text-xl leading-tight font-semibold text-gray-800">Nouvelle promotion</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Code -->
                            <div>
                                <InputLabel for="code" value="Code" />
                                <TextInput id="code" v-model="form.code" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.code" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Description" />
                                <TextArea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Type de réduction -->
                            <div>
                                <InputLabel for="discount_type" value="Type de réduction" />
                                <SelectInput id="discount_type" v-model="form.discount_type" class="mt-1 block w-full" required>
                                    <option value="percentage">Pourcentage</option>
                                    <option value="fixed">Montant fixe</option>
                                </SelectInput>
                                <InputError :message="form.errors.discount_type" class="mt-2" />
                            </div>

                            <!-- Valeur de la réduction -->
                            <div>
                                <InputLabel for="discount_value" :value="form.discount_type === 'percentage' ? 'Pourcentage (%)' : 'Montant (€)'" />
                                <TextInput
                                    id="discount_value"
                                    v-model="form.discount_value"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.discount_value" class="mt-2" />
                            </div>

                            <!-- Montant minimum de commande -->
                            <div>
                                <InputLabel for="min_order_amount" value="Montant minimum de commande (€)" />
                                <TextInput
                                    id="min_order_amount"
                                    v-model="form.min_order_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.min_order_amount" class="mt-2" />
                            </div>

                            <!-- Réduction maximale -->
                            <div>
                                <InputLabel for="max_discount" value="Réduction maximale (€)" />
                                <TextInput
                                    id="max_discount"
                                    v-model="form.max_discount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.max_discount" class="mt-2" />
                            </div>

                            <!-- Limite d'utilisation -->
                            <div>
                                <InputLabel for="usage_limit" value="Limite d'utilisation" />
                                <TextInput id="usage_limit" v-model="form.usage_limit" type="number" min="1" class="mt-1 block w-full" />
                                <InputError :message="form.errors.usage_limit" class="mt-2" />
                            </div>

                            <!-- Date d'expiration -->
                            <div>
                                <InputLabel for="expires_at" value="Date d'expiration" />
                                <TextInput id="expires_at" v-model="form.expires_at" type="datetime-local" class="mt-1 block w-full" />
                                <InputError :message="form.errors.expires_at" class="mt-2" />
                            </div>

                            <!-- Statut -->
                            <div>
                                <label class="flex items-center">
                                    <Checkbox name="is_active" v-model:checked="form.is_active" />
                                    <span class="ml-2 text-sm text-gray-600">Actif</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <Link
                                :href="route('promotions.index')"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-300 px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase ring-gray-300 transition hover:bg-gray-400 focus:border-gray-500 focus:ring focus:outline-none active:bg-gray-500 disabled:opacity-25"
                            >
                                Annuler
                            </Link>

                            <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Créer </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    code: '',
    description: '',
    discount_type: 'percentage',
    discount_value: '',
    min_order_amount: '',
    max_discount: '',
    usage_limit: '',
    expires_at: '',
    is_active: true,
});

const submit = () => {
    form.post(route('promotions.store'));
};
</script>
