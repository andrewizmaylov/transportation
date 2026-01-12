<script setup lang="ts">
import type { BDUIDelimiter } from '@/types/bdui';
import { cn } from '@/lib/utils';
import { computed } from 'vue';
import { Separator } from '@/components/ui/separator';

interface Props {
    delimiter: BDUIDelimiter;
}

const props = defineProps<Props>();

const marginClass = computed(() => {
    const m = props.delimiter.margin;
    if (!m) return '';
    return cn(
        m.top && `mt-${m.top}`,
        m.bottom && `mb-${m.bottom}`,
        m.left && `ml-${m.left}`,
        m.right && `mr-${m.right}`,
    );
});

const flexClass = computed(() => {
    const fp = props.delimiter.flexProperties;
    if (!fp) return '';
    return cn(
        fp.grow && `flex-${fp.grow}`,
        fp.basis && `basis-[${fp.basis}]`,
    );
});
</script>

<template>
    <Separator :class="cn(marginClass, flexClass)" />
</template>
