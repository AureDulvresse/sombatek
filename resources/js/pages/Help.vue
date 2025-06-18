<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar :cart-items-count="cartItemsCount" />

        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Centre d'aide</h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Bienvenue dans notre centre d'aide. Vous trouverez ici toutes les informations nécessaires pour utiliser notre plateforme.
                </p>

                <div class="mt-10 space-y-8">
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="text-xl font-semibold text-gray-900">Comment commander ?</h2>
                        <p class="mt-4 text-gray-600">Pour passer une commande, suivez ces étapes simples :</p>
                        <ol class="mt-4 list-decimal space-y-2 pl-5 text-gray-600">
                            <li>Parcourez notre catalogue de produits</li>
                            <li>Ajoutez les produits souhaités à votre panier</li>
                            <li>Vérifiez votre panier et procédez au paiement</li>
                            <li>Suivez votre commande dans votre espace client</li>
                        </ol>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="text-xl font-semibold text-gray-900">Livraison</h2>
                        <p class="mt-4 text-gray-600">
                            Nous livrons dans toute la France métropolitaine. Les délais de livraison varient selon votre localisation et le mode de
                            livraison choisi.
                        </p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="text-xl font-semibold text-gray-900">Retours et remboursements</h2>
                        <p class="mt-4 text-gray-600">
                            Vous disposez de 14 jours pour retourner un produit. Les retours sont gratuits et le remboursement est effectué sous 14
                            jours.
                        </p>
                    </div>
                </div>

                <div class="mt-10">
                    <h2 class="text-xl font-semibold text-gray-900">Besoin d'aide supplémentaire ?</h2>
                    <p class="mt-4 text-gray-600">Si vous n'avez pas trouvé la réponse à votre question, n'hésitez pas à nous contacter.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('contact')"
                            class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Nous contacter
                        </Link>
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
import { Link } from '@inertiajs/vue3';

defineProps({
    cartItemsCount: {
        type: Number,
        default: 0,
    },
});
</script>
