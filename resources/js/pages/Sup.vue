<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { sup } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import TransportationList from '@/pages/Sup/TransportationList.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'SUP',
        href: sup().url,
    },
];

const props = defineProps(['ts']);
</script>

<template>
    <Head title="SUP" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <TransportationList :ts="props.ts"/>
            </div>
        </div>
    </AppLayout>
</template>
