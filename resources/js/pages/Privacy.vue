<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

const privacySections = [
  {
    title: 'Collecte des données',
    content: `Nous collectons des informations personnelles lors de l'inscription, de la navigation et de l'utilisation de nos services (nom, email, adresse, etc.). Certaines données sont collectées automatiquement (cookies, logs, etc.).`
  },
  {
    title: 'Utilisation des données',
    content: `Les données collectées servent à :\n• Gérer les comptes utilisateurs\n• Améliorer l'expérience sur la plateforme\n• Envoyer des communications importantes\n• Sécuriser les transactions et prévenir la fraude`
  },
  {
    title: 'Protection des données',
    content: `SOMBATEK met en œuvre des mesures techniques et organisationnelles pour protéger les données personnelles contre tout accès, modification, divulgation ou destruction non autorisés.`
  },
  {
    title: 'Droits des utilisateurs',
    content: `Conformément à la loi, chaque utilisateur dispose d'un droit d'accès, de rectification, de suppression et d'opposition sur ses données personnelles.\nPour exercer ces droits, contactez-nous via votre espace personnel ou par email à contact@sombatek.com.`
  },
  {
    title: 'Cookies',
    content: `Le site utilise des cookies pour améliorer la navigation et analyser l'audience. Vous pouvez configurer vos préférences dans votre navigateur.`
  }
];
</script>

<template>
  <SiteLayout title="Politique de confidentialité">
    <PageBanner
      title="Politique de confidentialité"
      description="Comment nous collectons, utilisons et protégeons vos données personnelles."
      :breadcrumbs="[{ title: 'Accueil', href: '/' }, { title: 'Confidentialité' }]"
    />
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="space-y-10">
        <section v-for="section in privacySections" :key="section.title" class="section-transition">
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