<script setup lang="ts">
import { inject, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { ChevronDown } from 'lucide-vue-next';

interface Props {
    id?: string;
    class?: string;
}

const props = defineProps<Props>();

const value = inject<ReturnType<typeof computed<string>>>('selectValue');
const isOpen = inject<ReturnType<typeof ref<boolean>>>('selectOpen');
const toggle = inject<() => void>('selectToggleFn');

const handleClick = (e: MouseEvent) => {
    e.stopPropagation();
    if (toggle) {
        toggle();
    }
};
</script>

<template>
    <Button
        :id="props.id"
        type="button"
        variant="outline"
        role="combobox"
        :aria-expanded="isOpen"
        :class="cn('w-full justify-between', props.class)"
        @click="handleClick"
    >
        <slot />
        <ChevronDown
            :class="cn(
                'ml-2 h-4 w-4 shrink-0 opacity-50 transition-transform',
                isOpen && 'rotate-180',
            )"
        />
    </Button>
</template>
