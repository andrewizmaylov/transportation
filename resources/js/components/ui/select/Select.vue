<script setup lang="ts">
import { provide, computed, ref } from 'vue';

interface Props {
    modelValue?: string;
    searchable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    searchable: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const isOpen = ref(false);

const value = computed({
    get: () => props.modelValue || '',
    set: (val) => {
        emit('update:modelValue', val);
        isOpen.value = false; // Close on selection
    },
});

const open = () => {
    isOpen.value = true;
};

const close = () => {
    isOpen.value = false;
};

const toggle = () => {
    isOpen.value = !isOpen.value;
};

provide('selectValue', value);
provide('selectOpen', isOpen);
provide('selectOpenFn', open);
provide('selectCloseFn', close);
provide('selectToggleFn', toggle);
provide('selectSearchable', computed(() => props.searchable));
</script>

<template>
    <div class="relative">
        <slot />
    </div>
</template>
