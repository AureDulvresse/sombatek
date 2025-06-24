<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

const legalSections = [
  {
    title: 'Éditeur du site',
    content: `Le site SOMBATEK est édité par la société ONDRIS Tech, incubée à la CENC, dont le siège social est situé à Tchimbamba / Pointe-Noire / République du Congo.\n\nReprésentant légal : Mr Daniel ONDELE.`
  },
  {
    title: 'Hébergement',
    content: `Le site est hébergé par un prestataire professionnel.\n\nNom de l'hébergeur : [À compléter]\nAdresse : [À compléter]\nContact : [À compléter]`
  },
  {
    title: 'Responsable de la publication',
    content: `Le responsable de la publication est Mr Daniel ONDELE, en qualité de représentant légal de la société ONDRIS Tech.`
  },
  {
    title: 'Contact',
    content: `Pour toute question, vous pouvez nous contacter via le formulaire de contact du site ou par email à : contact@sombatek.com.`
  },
  {
    title: 'Propriété intellectuelle',
    content: `Tous les contenus présents sur le site SOMBATEK (textes, images, logos, vidéos, etc.) sont protégés par le droit d'auteur. Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de l'éditeur.`
  }
];
</script>

<template>
  <SiteLayout title="Mentions légales">
    <PageBanner
      title="Mentions légales"
      description="Informations légales et éditeur du site SOMBATEK."
      :breadcrumbs="[{ title: 'Accueil', href: '/' }, { title: 'Mentions légales' }]"
    />
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="space-y-10">
        <section v-for="section in legalSections" :key="section.title" class="section-transition">
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