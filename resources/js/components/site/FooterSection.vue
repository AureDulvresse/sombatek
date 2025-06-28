<template>
    <footer class="bg-gradient-to-br from-slate-900 via-gray-900 to-slate-800 border-t border-slate-700/50"
        aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">Footer</h2>

        <!-- Main footer content -->
        <div class="mx-auto max-w-7xl px-6 pt-16 pb-8 sm:pt-24 lg:px-8 lg:pt-20">
            <div class="xl:grid xl:grid-cols-3 xl:gap-12">
                <!-- Company info section -->
                <div class="space-y-8 xl:col-span-1">
                    <Link :href="route('home')" class="inline-block group">
                    <div class="transition-transform group-hover:scale-105">
                        <AppLogo />
                    </div>
                    </Link>

                    <div class="space-y-4">
                        <p class="text-base leading-7 text-slate-300 max-w-md">
                            Votre plateforme de commerce en ligne de confiance, offrant une expérience d'achat
                            exceptionnelle avec les meilleurs produits et services.
                        </p>

                        <!-- Key features -->
                        <div class="flex flex-wrap gap-2">
                            <span v-for="feature in keyFeatures" :key="feature"
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                {{ feature }}
                            </span>
                        </div>
                    </div>

                    <!-- Contact info -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 text-slate-300">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm">contact@sombatek.com</span>
                        </div>
                        <div class="flex items-center space-x-3 text-slate-300">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm">+242 06 123 45 67</span>
                        </div>
                    </div>

                    <!-- Social links -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-white">Suivez-nous</h4>
                        <div class="flex space-x-4">
                            <a v-for="item in socialLinks" :key="item.name" :href="item.href"
                                :aria-label="`Suivez-nous sur ${item.name}`"
                                class="group relative p-2 rounded-lg bg-slate-800/50 text-slate-400 transition-all duration-300 hover:bg-emerald-500/10 hover:text-emerald-400 hover:scale-110 hover:shadow-lg hover:shadow-emerald-500/20">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path :d="item.icon" />
                                </svg>
                                <span
                                    class="absolute -top-8 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-slate-700 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                                    {{ item.name }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Links sections -->
                <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0 lg:grid-cols-4">
                    <!-- Navigation -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Navigation</h3>
                        <ul role="list" class="space-y-4">
                            <li v-for="item in navigation" :key="item.name">
                                <Link :href="route(item.href)"
                                    class="group text-sm text-slate-300 transition-colors duration-200 hover:text-emerald-400 flex items-center space-x-2">
                                <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-emerald-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <span>{{ item.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Support</h3>
                        <ul role="list" class="space-y-4">
                            <li v-for="item in supportLinks" :key="item.name">
                                <Link :href="route(item.href)"
                                    class="group text-sm text-slate-300 transition-colors duration-200 hover:text-emerald-400 flex items-center space-x-2">
                                <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-emerald-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <span>{{ item.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Légal</h3>
                        <ul role="list" class="space-y-4">
                            <li v-for="item in legalLinks" :key="item.name">
                                <Link :href="route(item.href)"
                                    class="group text-sm text-slate-300 transition-colors duration-200 hover:text-emerald-400 flex items-center space-x-2">
                                <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-emerald-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <span>{{ item.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Company -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Entreprise</h3>
                        <ul role="list" class="space-y-4">
                            <li v-for="item in companyLinks" :key="item.name">
                                <Link :href="route(item.href)"
                                    class="group text-sm text-slate-300 transition-colors duration-200 hover:text-emerald-400 flex items-center space-x-2">
                                <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-emerald-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <span>{{ item.name }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Newsletter subscription -->
            <div class="mt-16 pt-8 border-t border-slate-700/50">
                <div class="max-w-md mx-auto xl:max-w-none xl:mx-0">
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Newsletter</h3>
                    <p class="text-sm text-slate-300 mb-4">
                        Recevez nos dernières offres et actualités directement dans votre boîte mail.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label for="email-address" class="sr-only">Adresse email</label>
                            <input id="email-address" name="email" type="email" autocomplete="email" required
                                v-model="emailSubscription"
                                class="w-full min-w-0 px-4 py-3 text-base text-gray-900 placeholder-gray-500 bg-white border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="Votre adresse email" />
                        </div>
                        <button @click="subscribeToNewsletter" type="button"
                            class="flex-shrink-0 px-6 py-3 text-base font-medium text-white bg-emerald-600 border border-transparent rounded-lg shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors duration-200">
                            S'abonner
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom section -->
            <div class="mt-16 pt-8 border-t border-slate-700/50">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-6">
                        <p class="text-xs text-slate-400">
                            &copy; {{ currentYear }} Sombatek. Tous droits réservés.
                        </p>
                        <div class="flex items-center space-x-4 text-xs text-slate-400">
                            <span>Made with ❤️ in Congo</span>
                            <span>•</span>
                            <span>Version {{ appVersion }}</span>
                        </div>
                    </div>

                    <!-- Back to top button -->
                    <button @click="scrollToTop" type="button"
                        class="group p-2 rounded-lg bg-slate-800/50 text-slate-400 transition-all duration-300 hover:bg-emerald-500/10 hover:text-emerald-400 hover:scale-110"
                        aria-label="Retour en haut de la page">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-y-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLogo from '../admin/sidebar/AppLogo.vue';

// Reactive data
const emailSubscription = ref('');

// Computed properties
const currentYear = computed(() => new Date().getFullYear());
const appVersion = computed(() => '2.1.0'); // You can get this from your app config

// Navigation data
const navigation = [
    { name: 'Accueil', href: 'home' },
    { name: 'Boutiques', href: 'shops.index' },
    { name: 'Produits', href: 'products.index' },
    { name: 'Promotions', href: 'promotions.index' },
];

const supportLinks = [
    { name: 'Centre d\'aide', href: 'help' },
    { name: 'Contact', href: 'contact' },
    { name: 'FAQ', href: 'faq' },
    { name: 'Support technique', href: 'help' },
];

const legalLinks = [
    { name: 'Politique de confidentialité', href: 'privacy' },
    { name: 'Conditions d\'utilisation', href: 'terms' },
    { name: 'Politique des cookies', href: 'cookies' },
    { name: 'Mentions légales', href: 'legal' },
];

const companyLinks = [
    { name: 'Qui sommes-nous', href: 'about' },
    { name: 'Carrières', href: 'careers' },
    { name: 'Partenaires', href: 'partners' },
    { name: 'Presse', href: 'press' },
];

const socialLinks = [
    {
        name: 'Facebook',
        href: 'https://facebook.com/sombatek',
        icon: 'M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z',
    },
    {
        name: 'Instagram',
        href: 'https://instagram.com/sombatek',
        icon: 'M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z',
    },
    {
        name: 'Twitter',
        href: 'https://twitter.com/sombatek',
        icon: 'M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84',
    },
    {
        name: 'LinkedIn',
        href: 'https://linkedin.com/company/sombatek',
        icon: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
    },
    {
        name: 'YouTube',
        href: 'https://youtube.com/@sombatek',
        icon: 'M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z',
    },
];

const keyFeatures = [
    'Livraison rapide',
    'Service client 24/7',
    'Paiement sécurisé',
    'Garantie qualité'
];

// Methods
const subscribeToNewsletter = () => {
    if (!emailSubscription.value) {
        alert('Veuillez entrer une adresse email valide');
        return;
    }

    // Here you would typically make an API call to subscribe the user
    console.log('Subscribing email:', emailSubscription.value);
    alert('Merci pour votre inscription à notre newsletter !');
    emailSubscription.value = '';
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

defineExpose({});
</script>

<script lang="ts">
export default {
    name: 'FooterSection',
};
</script>
