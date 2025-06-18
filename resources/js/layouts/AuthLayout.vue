<script setup lang="ts">
import AuthSplitLayout from './auth/AuthSplitLayout.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthSplitLayout :title="title" :description="description">
        <slot />
    </AuthSplitLayout>
</template>

<script lang="ts">
export default {
    name: 'AuthLayout',
};
</script>
