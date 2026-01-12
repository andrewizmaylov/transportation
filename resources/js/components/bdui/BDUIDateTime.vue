<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { BDUIDateTime } from '@/types/bdui';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    field: BDUIDateTime;
    modelValue?: string;
    error?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const inputType = computed(() => {
    return props.field.showTime ? 'datetime-local' : 'date';
});

const minDate = computed(() => {
    if (props.field.minDate === 'today') {
        const now = new Date();
        return now.toISOString().split('T')[0];
    }
    return undefined;
});

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
        <Input
            :id="field.id"
            :type="inputType"
            :value="modelValue"
            :placeholder="field.placeholder"
            :min="minDate"
            :class="cn(error && 'border-destructive')"
            @update:model-value="emit('update:modelValue', $event)"
        />
        <InputError v-if="error" :message="error" />
    </div>
</template>
