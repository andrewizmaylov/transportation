<script setup lang="ts">
import { inject, computed } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    value: string;
    class?: string;
}

const props = defineProps<Props>();

const selectValue = inject<ReturnType<typeof computed<string>>>('selectValue');
const close = inject<() => void>('selectCloseFn');

const isSelected = computed(() => selectValue?.value === props.value);

const handleClick = () => {
    if (selectValue) {
        selectValue.value = props.value;
    }
    if (close) {
        close();
    }
};
</script>

<template>
    <div
        :class="cn(
            'relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground',
            isSelected && 'bg-accent text-accent-foreground',
            props.class,
        )"
        @click="handleClick"
    >
        <slot />
    </div>
</template>
