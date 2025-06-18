<script setup lang="ts">
// No props needed for this component
</script>

<script lang="ts">
export default {
    name: 'NewsletterSection',
};
</script>

<template>
    <div class="relative overflow-hidden py-24">
        <!-- Background with gradient and pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-green-600 via-green-700 to-emerald-800"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white"></div>
            <div class="absolute right-0 bottom-0 h-96 w-96 translate-x-1/2 translate-y-1/2 rounded-full bg-white"></div>
        </div>

        <div class="relative mx-auto max-w-4xl px-6 text-center lg:px-8">
            <div class="animate-fade-in-up">
                <h2 class="mb-6 text-4xl font-bold text-white lg:text-5xl">
                    Ne manquez rien de nos
                    <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent"> nouveautés </span>
                </h2>

                <p class="mx-auto mb-12 max-w-2xl text-xl text-green-100">
                    Soyez les premiers informés de nos offres exclusives, nouveaux produits et promotions spéciales.
                </p>

                <form class="mx-auto flex max-w-md flex-col gap-4 sm:flex-row">
                    <input
                        type="email"
                        placeholder="Votre adresse email"
                        required
                        class="flex-1 rounded-2xl border border-white/20 bg-white/10 px-6 py-4 text-white placeholder-green-200 backdrop-blur-md transition-all duration-300 focus:border-white/40 focus:ring-4 focus:ring-white/30 focus:outline-none"
                    />
                    <button
                        type="submit"
                        class="transform rounded-2xl bg-white px-8 py-4 font-bold text-green-600 transition-all duration-300 hover:scale-105 hover:bg-green-50 hover:shadow-xl"
                    >
                        S'abonner
                    </button>
                </form>

                <p class="mt-4 text-sm text-green-200">✨ Rejoignez plus de 50,000 clients satisfaits</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
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

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}
</style>
