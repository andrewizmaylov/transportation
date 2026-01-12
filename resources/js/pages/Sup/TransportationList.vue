<script setup>
import { computed } from 'vue';
import TransportationCard from '@/pages/Sup/TransportationCard.vue';

const props = defineProps(['ts']);
const transportations = computed(() => props.ts.map((ts) => ts.active));
</script>
<template>
    <div class="h-full w-full ">
        <h1>something would be happened here</h1>
        <div class="grid grid-cols-3 gap-4 w-full">
            <TransportationCard
                v-for="t in ts"
                :key="t.id"
                :transportation="t"
            >
                {{ t.name }}
            </TransportationCard>
        </div>

    </div>
</template>
