<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { BDUIMultiGeo } from '@/types/bdui';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    field: BDUIMultiGeo;
    modelValue?: {
        address?: string;
        coordinates?: { lat: number; lng: number };
        cityId?: number;
        countryId?: number;
    };
    error?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: any): void;
}>();

// Simplified version - in production, integrate with a geocoding service
const addressValue = computed(() => {
    if (typeof props.modelValue === 'string') {
        return props.modelValue;
    }
    return props.modelValue?.address || '';
});

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', {
        address: target.value,
        coordinates: props.modelValue?.coordinates,
    });
};

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
            :value="addressValue"
            :placeholder="field.placeholder"
            :class="cn(error && 'border-destructive')"
            @input="handleInput"
        />
        <!-- TODO: Add map integration for geocoding -->
        <InputError v-if="error" :message="error" />
    </div>
</template>
