<script setup lang="ts">
import PageBanner from '@/components/site/PageBanner.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';

const cguSections = [
  {
    title: 'ARTICLE 1 : Objet',
    content: `Les présentes « conditions générales d'utilisation » ont pour objet l'encadrement juridique de l'utilisation du site SOMBATEK et de ses services.\n\nCe contrat est conclu entre : SOMBATEK,  ses partenaires et ses utilisateurs  \n\nToute personne physique ou morale souhaitant accéder au site et à ses services, ci-après est appelé « l'Utilisateur ».\n\nLes differents utilisateurs de SOMBATEK sont :\n• Les Boutiques\n• Les clients\n• Les démargeurs ou conseillés commerciaux \n\nLes conditions générales d'utilisation doivent être acceptées par tout Utilisateur et partenaire, son accès au site vaut acceptation de ces conditions.`
  },
  {
    title: 'ARTICLE 2 : Mentions légales',
    content: `Pour les personnes morales :\n\nLe site SOMBATEK  est édité par la société ONDRIS Tech, sous incumbation de la CENC, dont le siège social est situé à Tchimbamba / Pointe-Noire / Republique du Congo\n\nLa société est représentée par Mr Daniel ONDELE.\n\nPour les personnes physiques : \n\nLe site Nom du site est édité par Nom et prénom du responsable, domicilié au Adresse postale.`
  },
  {
    title: 'ARTICLE 3 : accès aux services',
    content: `L'Utilisateur du site SOMBATEK a accès aux services suivants :\n- Pour les boutiques :\n- Pour les clients : \n- Pour les démarcheurs : \n\nTout utilisateur ayant accès à internet peut accéder gratuitement et depuis n'importe où au site. Les frais supportés par l'Utilisateur pour y accéder (connexion internet, matériel informatique, etc.) ne sont pas à la charge de l'Éditeur.\n\nLes services suivants ne sont pas accessible pour l'Utilisateur que s'il est membre du site (c'est-à-dire qu'ile st identifié à l'aide de ses identifiants de connexion) :\n• Mise en ligne des articles\n• Achat d'article\n• Abonnement aux boutiques \n\nLe site et ses différents services peuvent être interrompus ou suspendus par ONDRIS Tech, notamment à l'occasion d'une maintenance, sans obligation de préavis ou de justification.`
  },
  {
    title: 'ARTICLE 4 : Responsabilité de l\'Utilisateur',
    content: `L'Utilisateur est responsable des risques liés à l'utilisation de son identifiant de connexion et de son mot de passe. \n\nLe mot de passe de l'Utilisateur doit rester secret. En cas de divulgation de mot de passe, ONDRIS Tech  décline toute responsabilité.\n\nL'Utilisateur assume l'entière responsabilité de l'utilisation qu'il fait des informations et contenus présents sur le site SOMBATEK.\n\nTout usage du service par l'Utilisateur ayant directement ou indirectement pour conséquence des dommages doit faire l'objet d'une indemnisation au profit du site.\n\nLe site permet aux membres de publier sur le site :\n• Article commercial;\n• Commentaire;\n• Etc.\n\nLe membre s'engage à tenir des propos respectueux des autres et de la loi et accepte que ces publications soient modérées ou refusées par l'administration SOMBATEK, sans obligation de justification. \n\nEn publiant sur le site, l'Utilisateur cède à la société ONDRIS Tech  le droit non exclusif et gratuit de représenter, reproduire, adapter, modifier, diffuser et distribuer sa publication, directement ou par un tiers autorisé.\n\nONDRIS Tech s'engage toutefois à citer le membre en cas d'utilisation de  sa publication`
  },
  {
    title: 'ARTICLE 5 : Responsabilité de l\'Éditeur',
    content: `Tout dysfonctionnement du serveur ou du réseau ne peut engager la responsabilité de l'Éditeur.\n\nDe même, la responsabilité du site ne peut être engagée en cas de force majeure ou du fait imprévisible et insurmontable d'un tiers.\n\nLe site SOMBATEK s'engage à mettre en œuvre tous les moyens nécessaires pour garantir la sécurité et la confidentialité des données. Toutefois, il n'apporte pas une garantie de sécurité totale.\n\nL'Éditeur se réserve la faculté d'une non-garantie de la fiabilité des sources, bien que les informations diffusées sur le site soient réputées fiables.`
  },
  {
    title: 'ARTICLE 6 : Propriété intellectuelle',
    content: `Les contenus du site SOMBATEK (logos, textes, éléments graphiques, vidéos, etc.) sont protégés par le droit d'auteur, en vertu du Code de la propriété intellectuelle.\n\nL'utilisateur devra obtenir l'autorisation de l'éditeur du site avant toute reproduction, copie ou publication de ces différents contenus.\n\nCes derniers peuvent être utilisés par les utilisateurs à des fins privées \n\nL'utilisateur est entièrement responsable de tout contenu qu'il met en ligne et il s'engage à ne pas porter atteinte à un tiers.\n\nL'Éditeur du site se réserve le droit de modérer ou de supprimer librement et à tout moment les contenus mis en ligne par les utilisateurs, et ce sans justification.`
  },
  {
    title: 'ARTICLE 7 : Données personnelles',
    content: `L'Utilisateur doit obligatoirement fournir des informations personnelles pour procéder à son inscription sur le site. \n\nL'adresse électronique (e-mail) de l'utilisateur pourra notamment être utilisée par le site SOMBATEK pour la communication d'informations diverses et la gestion du compte.\n\nSOMBATEK garantit le respect de la vie privée de l'utilisateur, conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés.\n\nLe site est déclaré auprès de la CNIL sous le numéro suivant : Numéro.\n\nEn vertu des articles 39 et 40 de la loi en date du 6 janvier 1978, l'Utilisateur dispose d'un droit d'accès, de rectification, de suppression et d'opposition de ses données personnelles. L'Utilisateur exerce ce droit via :\n\n• Son espace personnel sur le site ;\n• Un formulaire de contact ;\n• Par mail à Adresse mail ;\n• Par voie postale au Votre adresse.`
  },
  {
    title: 'ARTICLE 8 : Liens hypertextes',
    content: `Les domaines vers lesquels mènent les liens hypertextes présents sur le site n'engagent pas la responsabilité de l'Éditeur de SOMBATEK, qui n'a pas de contrôle sur ces liens.\n\nIl est possible pour un tiers de créer un lien vers une page du site SOMBATEK sans autorisation expresse de l'éditeur.`
  },
  {
    title: 'ARTICLE 9 : Évolution des conditions générales d\'utilisation',
    content: `Le site SOMBATEK se réserve le droit de modifier les clauses de ces conditions générales d'utilisation à tout moment et sans justification.`
  },
  {
    title: 'ARTICLE 10 : Durée du contrat',
    content: `La durée du présent contrat est indéterminée. Le contrat produit ses effets à l'égard de l'utilisateur à compter du début de l'utilisation du service.`
  },
  {
    title: 'ARTICLE 11 : Droit applicable et juridiction compétente',
    content: `Le présent contrat dépend de la législation  \nEn cas de litige non résolu à l'amiable entre l'utilisateur et l'éditeur, les tribunaux de Nom de ville sont compétents pour régler le contentieux.`
  }
];
</script>

<template>
  <SiteLayout title="Conditions Générales d'Utilisation">
    <PageBanner
      title="Conditions Générales d'Utilisation"
      description="Veuillez lire attentivement les conditions générales d'utilisation de la plateforme SOMBATEK."
      :breadcrumbs="[{ title: 'Accueil', href: '/' }, { title: 'CGU' }]"
    />
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="space-y-10">
        <section v-for="section in cguSections" :key="section.title" class="section-transition">
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