<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';

defineProps<{
    promotionCode: string | null;
    promotionDiscount: number;
}>();

const emit = defineEmits<{
    (e: 'promotion-applied', code: string): void;
    (e: 'promotion-removed'): void;
}>();

const toast = useToast();
const isApplying = ref(false);
const isRemoving = ref(false);

const form = useForm({
    code: '',
});

const applyPromotion = async () => {
    if (!form.code) {
        toast.error('Veuillez entrer un code promotion');
        return;
    }

    isApplying.value = true;

    try {
        const response = await axios.post(route('cart.apply-promotion'), {
            code: form.code,
        });

        if (response.data.success) {
            emit('promotion-applied', form.code);
            toast.success(response.data.message);
            form.reset();
        }
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erreur lors de l'application du code promotion");
    } finally {
        isApplying.value = false;
    }
};

const removePromotion = async () => {
    isRemoving.value = true;

    try {
        const response = await axios.post(route('cart.remove-promotion'));

        if (response.data.success) {
            emit('promotion-removed');
            toast.success(response.data.message);
        }
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Erreur lors de la suppression du code promotion');
    } finally {
        isRemoving.value = false;
    }
};
</script>

<template>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Code promotion</h3>

        <div v-if="promotionCode" class="mb-4">
            <div class="flex items-center justify-between rounded-lg bg-green-50 p-3 dark:bg-green-900">
                <div class="flex items-center space-x-3">
                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Code appliqué : {{ promotionCode }}</p>
                        <p class="text-xs text-green-600 dark:text-green-400">Réduction : {{ promotionDiscount.toFixed(2) }} €</p>
                    </div>
                </div>
                <button
                    @click="removePromotion"
                    :disabled="isRemoving"
                    class="rounded-full p-1 text-green-600 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-800"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form @submit.prevent="applyPromotion" class="space-y-4">
            <div>
                <label for="promotion-code" class="sr-only">Code promotion</label>
                <div class="relative">
                    <input
                        type="text"
                        id="promotion-code"
                        v-model="form.code"
                        :disabled="isApplying || !!promotionCode"
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-green-500 dark:focus:ring-green-500"
                        placeholder="Entrez votre code promotion"
                    />
                    <button
                        type="submit"
                        :disabled="isApplying || !form.code || !!promotionCode"
                        class="absolute top-1/2 right-2.5 -translate-y-1/2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-4 focus:ring-green-300 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    >
                        <span v-if="isApplying" class="flex items-center">
                            <svg class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Application...
                        </span>
                        <span v-else>Appliquer</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
