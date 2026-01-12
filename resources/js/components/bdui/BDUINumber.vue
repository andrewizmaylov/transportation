<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import type { BDUINumber } from '@/types/bdui';
import { computed } from 'vue';

interface Props {
    field: BDUINumber;
    modelValue?: { value: number; unit?: string } | number;
    error?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: { value: number; unit?: string }): void;
}>();

const displayValue = computed(() => {
    if (typeof props.modelValue === 'number') {
        return props.modelValue;
    }
    return props.modelValue?.value ?? '';
});

const unit = computed(() => {
    if (typeof props.modelValue === 'object' && props.modelValue?.unit) {
        return props.modelValue.unit;
    }
    return props.field.units?.default?.value || '';
});

const unitLabel = computed(() => {
    if (typeof props.modelValue === 'object' && props.modelValue?.unit) {
        const unitOption = props.field.units?.default;
        if (unitOption && unitOption.value === props.modelValue.unit) {
            return unitOption.label;
        }
    }
    return props.field.units?.default?.label || '';
});

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const numValue = parseFloat(target.value) || 0;
    emit('update:modelValue', {
        value: numValue,
        unit: unit.value,
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
    return cn(fp.grow && `flex-${fp.grow}`, fp.basis && `basis-[${fp.basis}]`);
});
</script>

<template>
    <div :class="cn('flex flex-col gap-2', marginClass, flexClass)">
        <Label v-if="field.label?.text" :for="field.id">
            {{ field.label.text }} {{ unitLabel }}
        </Label>
        <input
            :id="field.id"
            :value="displayValue"
            type="number"
            :maxlength="field.maxLength"
            step="1"
            min="1"
            :class="
                cn(
                    'h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30',
                    'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                    'aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40',
                    error && 'border-destructive',
                    props.class,
                )
            "
            @input="handleInput"
        />
        <InputError v-if="error" :message="error" />
    </div>
</template>
<style>
.custom-sizes {
    @apply gap-x-[14px] gap-x-[16px] gap-x-[18px] gap-x-[20px] gap-x-[22px] gap-x-[24px] gap-y-[14px] gap-y-[16px] gap-y-[18px] gap-y-[20px] gap-y-[22px] gap-y-[24px]
}
</style>
