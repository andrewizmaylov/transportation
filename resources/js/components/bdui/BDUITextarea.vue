<script setup lang="ts">
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { BDUITextarea } from '@/types/bdui';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    field: BDUITextarea;
    modelValue?: string;
    error?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const marginClass = computed(() => {
    const m = props.field.margin;
    if (!m) return '';
    return cn(
        m.top && `mt-${m.top}`,
        m.bottom && `mb-${m.bottom}`,
        m.left && `ml-${m.left}`,
        m.right && `mr-${m.right}`,
    );
});

const flexClass = computed(() => {
    const fp = props.field.flexProperties;
    if (!fp) return '';
    return cn(
        fp.grow && `flex-${fp.grow}`,
        fp.basis && `basis-[${fp.basis}]`,
    );
});
</script>

<template>
    <div :class="cn('flex flex-col gap-2', marginClass, flexClass)">
        <Label v-if="field.label?.text" :for="field.id">
            {{ field.label.text }}
        </Label>
        <textarea
            :id="field.id"
            :value="modelValue"
            :placeholder="field.placeholder"
            :rows="field.minRows || 3"
            :class="cn(
                'flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                error && 'border-destructive',
            )"
            @input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        />
        <InputError v-if="error" :message="error" />
    </div>
</template>
