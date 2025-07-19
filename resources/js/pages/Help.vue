<template>
    <SiteLayout title="Centre d'aide">
        <PageBanner title="Centre d'aide" description="Trouvez les réponses à vos questions."
            :breadcrumbs="[{ title: 'Accueil', href: '/' }, { title: 'Centre d\'aide' }]" />
        <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Comment pouvons-nous vous aider ?</h1>
            <p class="mt-4 text-gray-600">Explorez nos articles pour trouver des réponses à vos questions.</p>
        </div>
        <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="space-y-8">
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

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-900/5"></div>
                <h2 class="text-xl font-semibold text-gray-900">Livraison</h2>
                <p class="mt-4 text-gray-600">
                    Nous livrons dans les villes de Pointe-Noire, Brazzaville et Dolisie. Les délais de livraison
                    varient selon votre localisation et le mode de
                    livraison choisi.
                </p>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="text-xl font-semibold text-gray-900">Retours et remboursements</h2>
                <p class="mt-4 text-gray-600">
                    Vous disposez de 14 jours pour retourner un produit. Les retours sont gratuits et le remboursement
                    est effectué sous 14
                    jours.
                </p>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-xl font-semibold text-gray-900">Besoin d'aide supplémentaire ?</h2>
            <p class="mt-4 text-gray-600">Si vous n'avez pas trouvé la réponse à votre question, n'hésitez pas à nous
                contacter.</p>
            <div class="mt-6">
                <Link :href="route('contact')"
                    class="rounded-md bg-green-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                Nous contacter
                </Link>
            </div>
        </div>
    </SiteLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps({
    cartItemsCount: {
        type: Number,
        default: 0,
    },
});
</script>
