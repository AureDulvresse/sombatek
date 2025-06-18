<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar :cart-items-count="cartItemsCount" />

        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Contactez-nous</h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">Notre équipe est à votre disposition pour répondre à toutes vos questions.</p>

                <form @submit.prevent="submit" class="mt-10 space-y-8">
                    <div>
                        <label for="name" class="block text-sm leading-6 font-medium text-gray-900">Nom</label>
                        <div class="mt-2">
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm leading-6 font-medium text-gray-900">Email</label>
                        <div class="mt-2">
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm leading-6 font-medium text-gray-900">Sujet</label>
                        <div class="mt-2">
                            <input
                                type="text"
                                id="subject"
                                v-model="form.subject"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-sm leading-6 font-medium text-gray-900">Message</label>
                        <div class="mt-2">
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="4"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Envoyer le message
                        </button>
                    </div>
                </form>

                <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="text-xl font-semibold text-gray-900">Adresse</h2>
                        <p class="mt-4 text-gray-600">
                            123 Rue du Commerce<br />
                            75001 Paris<br />
                            France
                        </p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="text-xl font-semibold text-gray-900">Contact</h2>
                        <p class="mt-4 text-gray-600">
                            Email : contact@sombatek.com<br />
                            Téléphone : +33 1 23 45 67 89
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>

<script setup>
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post(route('contact.store'));
};

defineProps({
    cartItemsCount: {
        type: Number,
        default: 0,
    },
});
</script>
