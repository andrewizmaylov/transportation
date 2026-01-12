<script setup lang="ts">
import { inject, computed } from 'vue';

interface Props {
    placeholder?: string;
    displayValue?: string;
}

const props = defineProps<Props>();

const selectValue = inject<ReturnType<typeof computed<string>>>('selectValue');

const displayValue = computed(() => {
    // Use provided displayValue if available, otherwise use placeholder
    if (props.displayValue) {
        return props.displayValue;
    }
    return selectValue?.value || props.placeholder || 'Select...';
});
</script>

<template>
    <span class="truncate">{{ displayValue }}</span>
</template>
