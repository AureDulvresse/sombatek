<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

const cookiesSections = [
  {
    title: 'Qu\'est-ce qu\'un cookie ?',
    content: `Un cookie est un petit fichier texte enregistré sur votre appareil lors de la visite d'un site web. Il permet de reconnaître votre appareil lors de vos visites ultérieures.`
  },
  {
    title: 'Utilisation des cookies',
    content: `SOMBATEK utilise des cookies pour :\n• Faciliter la navigation\n• Mémoriser vos préférences\n• Analyser l'audience et améliorer les services\n• Sécuriser l'accès à certaines fonctionnalités`
  },
  {
    title: 'Paramétrage des cookies',
    content: `Vous pouvez à tout moment choisir de désactiver les cookies via les paramètres de votre navigateur. Toutefois, certaines fonctionnalités du site pourraient ne plus fonctionner correctement.`
  },
  {
    title: 'Gestion des cookies',
    content: `Pour en savoir plus sur la gestion des cookies, consultez la rubrique d'aide de votre navigateur ou rendez-vous sur le site de la CNIL.`
  }
];
</script>

<template>
  <SiteLayout title="Politique des cookies">
    <PageBanner
      title="Politique des cookies"
      description="Informations sur l'utilisation des cookies sur SOMBATEK."
      :breadcrumbs="[{ title: 'Accueil', href: '/' }, { title: 'Cookies' }]"
    />
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="space-y-10">
        <section v-for="section in cookiesSections" :key="section.title" class="section-transition">
          <h2 class="mb-2 text-xl font-bold text-emerald-700 dark:text-emerald-400">{{ section.title }}</h2>
          <div class="prose prose-sm max-w-none text-gray-700 dark:prose-invert dark:text-gray-200 whitespace-pre-line">
            {{ section.content }}
          </div>
        </section>
      </div>
    </div>
  </SiteLayout>
</template>

<style scoped>
.prose {
  font-size: 1rem;
  line-height: 1.7;
}
</style> 