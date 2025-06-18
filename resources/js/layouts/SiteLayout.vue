<script setup lang="ts">
import Footer from '@/components/site/FooterSection.vue';
import Navbar from '@/components/site/Navbar.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Sombatek',
    },
});
</script>

<template>
    <Head :title="title" />
    <div class="flex min-h-screen flex-col">
        <Navbar />
        <main class="flex-grow">
            <slot />
        </main>
        <Footer />
    </div>
</template>

<script lang="ts">
export default {
    name: 'SiteLayout',
};
</script>
