<script setup lang="ts">
import AdvertisementSection from '@/components/site/home/AdvertisementSection.vue';
import BestSellersSection from '@/components/site/home/BestSellersSection.vue';
import FlashDealsSection from '@/components/site/home/FlashDealsSection.vue';
import HeroSection from '@/components/site/home/HeroSection.vue';
import NewArrivalsSection from '@/components/site/home/NewArrivalsSection.vue';
import NewsletterSection from '@/components/site/home/NewsletterSection.vue';
import PopularCategoriesSection from '@/components/site/home/PopularCategoriesSection.vue';
import SeasonalDealsSection from '@/components/site/home/SeasonalDealsSection.vue';
import ShopByCategorySection from '@/components/site/home/ShopByCategorySection.vue';
import TopShopsSection from '@/components/site/home/TopShopsSection.vue';
import TrendingProductsSection from '@/components/site/home/TrendingProductsSection.vue';
import TrustSection from '@/components/site/home/TrustSection.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

interface Props {
    categories: Array<any>;
    newArrivals: Array<any>;
    bestSellers: Array<any>;
    seasonalDeals: Array<any>;
    flashDeals: Array<any>;
    trendingProducts: Array<any>;
    topShops: Array<any>;
    advertisements: Array<{
        id: number;
        title: string;
        description: string;
        image: string;
        link: string;
        type: 'product' | 'shop';
        start_date: string;
        end_date: string;
    }>;
}

defineProps<Props>();
</script>

<template>
    <SiteLayout title="Accueil">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100">
            <!-- Hero Section avec recherche et bannière principale -->
            <HeroSection class="relative z-10" />

            <!-- Flash Deals - Offres éclair avec compte à rebours -->
            <FlashDealsSection :products="flashDeals" class="relative z-0" />

            <!-- Section publicitaire -->
            <AdvertisementSection :advertisements="advertisements" class="relative z-0" />

            <!-- Catégories populaires - Navigation rapide -->
            <PopularCategoriesSection :categories="categories" class="relative z-0" />

            <!-- Nouveaux arrivages - Produits récemment ajoutés -->
            <NewArrivalsSection :products="newArrivals" class="relative z-0" />

            <!-- Meilleures ventes - Produits les plus populaires -->
            <BestSellersSection :products="bestSellers" class="relative z-0" />

            <!-- Produits tendance - Basé sur les recherches et interactions -->
            <TrendingProductsSection :products="trendingProducts" class="relative z-0" />

            <!-- Offres saisonnières - Promotions thématiques -->
            <SeasonalDealsSection :products="seasonalDeals" class="relative z-0" />

            <!-- Navigation par catégorie - Exploration approfondie -->
            <ShopByCategorySection :categories="categories" class="relative z-0" />

            <!-- Meilleures boutiques - Boutiques les mieux notées -->
            <TopShopsSection :top-shops="topShops" class="relative z-0" />

            <!-- Section de confiance - Garanties et avantages -->
            <TrustSection class="relative z-0" />

            <!-- Newsletter - Inscription à la newsletter -->
            <NewsletterSection class="relative z-0" />
        </div>
    </SiteLayout>
</template>

<style scoped>
/* Animations de base */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes gradient {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes bounceSlow {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Classes d'animation */
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}

.animate-fade-in-right {
    animation: fadeInRight 0.8s ease-out forwards;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}

.animate-bounce-slow {
    animation: bounceSlow 3s ease-in-out infinite;
}

/* Effets visuels */
.floating-shape {
    filter: blur(40px);
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #10b981, #059669);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #059669, #047857);
}

/* Utilitaires */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Transitions fluides */
.section-transition {
    transition: all 0.3s ease-in-out;
}

/* Effets de survol */
.hover-scale {
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.02);
}

/* Ombres dynamiques */
.shadow-hover {
    transition: box-shadow 0.3s ease;
}

.shadow-hover:hover {
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
}
</style>
