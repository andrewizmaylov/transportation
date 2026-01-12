<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { BDUIButton } from '@/types/bdui';
import { cn } from '@/lib/utils';
import { computed } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    button: BDUIButton;
    loading?: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'click'): void;
}>();

const variant = computed(() => {
    const theme = props.button.theme || 'fill_primary';
    switch (theme) {
        case 'fill_primary':
            return 'default';
        case 'fill_white_primary':
            return 'outline';
        case 'fill_secondary':
            return 'secondary';
        default:
            return 'default';
    }
});

const size = computed(() => {
    const s = props.button.size || 'medium';
    switch (s) {
        case 'small':
            return 'sm';
        case 'medium':
            return 'default';
        case 'large':
            return 'lg';
        default:
            return 'default';
    }
});

const marginClass = computed(() => {
    const m = props.button.margin;
    if (!m) return '';
    return cn(
        m.top && `mt-${m.top}`,
        m.bottom && `mb-${m.bottom}`,
        m.left && `ml-${m.left}`,
        m.right && `mr-${m.right}`,
    );
});
</script>

<template>
    <Button
        :id="button.id"
        :variant="variant"
        :size="size"
        :class="cn(
            button.fullWidth && 'w-full',
            marginClass,
        )"
        :disabled="loading"
        @click="emit('click')"
    >
        <ArrowLeft
            v-if="button.beforeIcon && (button.actionType === 'previousStep')"
            class="mr-2 h-4 w-4"
        />
        {{ button.text }}
    </Button>
</template>
