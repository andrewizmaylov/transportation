<template>
    <div class="relative h-full w-full p-2 border border-sidebar-border/70 dark:border-sidebar-border overflow-hidden">
        <PlaceholderPattern />
        <h2>{{ transportation.name }}</h2>
    </div>
</template>
<script setup>
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

defineProps(['transportation'])
</script>
