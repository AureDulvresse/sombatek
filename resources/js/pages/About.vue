<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

const aboutSections = [
  {
    title: 'Notre mission',
    content: `Sombatek est une plateforme de commerce en ligne innovante, conçue pour connecter boutiques, clients et partenaires commerciaux dans toute l'Afrique centrale.\n\nNotre mission est de faciliter l'accès à des produits de qualité, de soutenir l'entrepreneuriat local et de promouvoir la digitalisation du commerce.`
  },
  {
    title: 'Nos valeurs',
    content: `• Confiance et sécurité\n• Innovation\n• Proximité avec nos utilisateurs\n• Soutien à l'économie locale\n• Respect de la diversité et de l'inclusion` 
  },
  {
    title: 'Qui sommes-nous ?',
    content: `Sombatek est édité par la société ONDRIS Tech, incubée à la CENC, basée à Pointe-Noire, République du Congo.\n\nNotre équipe est composée de passionnés de technologie, de commerce et de service client, engagés à offrir la meilleure expérience possible à nos utilisateurs.`
  },
  {
    title: 'Pourquoi choisir Sombatek ?',
    content: `• Large choix de produits et de boutiques\n• Plateforme sécurisée et facile d'utilisation\n• Accompagnement des vendeurs et partenaires\n• Service client réactif et à l'écoute` 
  }
];
</script>

<template>
  <SiteLayout title="À propos de Sombatek">
    <PageBanner
      title="À propos de Sombatek"
      description="Découvrez notre histoire, notre mission et nos valeurs."
      :breadcrumbs="[{ title: 'Accueil', href: '/' }, { title: 'À propos' }]"
    />
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="space-y-10">
        <section v-for="section in aboutSections" :key="section.title" class="section-transition">
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