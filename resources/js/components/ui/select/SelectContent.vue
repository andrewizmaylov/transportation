<script setup lang="ts">
import { inject, ref, onMounted, onUnmounted, nextTick, watch, provide } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    searchable?: boolean;
    searchPlaceholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    searchable: false,
    searchPlaceholder: 'Search...',
});

const emit = defineEmits<{
    (e: 'update:search-query', value: string): void;
}>();

const isOpen = inject<ReturnType<typeof ref<boolean>>>('selectOpen');
const close = inject<() => void>('selectCloseFn');
const searchInputRef = ref<HTMLInputElement | null>(null);
const searchQuery = ref('');

// Focus search input when dropdown opens
watch(
    () => isOpen?.value,
    (open) => {
        if (open && props.searchable) {
            nextTick(() => {
                searchInputRef.value?.focus();
            });
        } else {
            // Clear search when closing
            searchQuery.value = '';
            emit('update:search-query', '');
        }
    },
);

const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.select-content')) {
        if (close) {
            close();
        }
    }
};

const handleSearchInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    searchQuery.value = target.value;
    emit('update:search-query', target.value);
};

const handleKeyDown = (event: KeyboardEvent) => {
    // Prevent closing on Escape if there's a search query
    if (event.key === 'Escape' && searchQuery.value) {
        event.stopPropagation();
        searchQuery.value = '';
        emit('update:search-query', '');
        searchInputRef.value?.focus();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Provide search query to child components (if needed)
provide('selectSearchQuery', searchQuery);
</script>

<template>
    <div
        v-if="isOpen"
        class="select-content"
        :class="cn(
            'absolute top-full z-50 mt-1 min-w-[8rem] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md',
        )"
        @click.stop
    >
        <div v-if="searchable" class="border-b p-1">
            <input
                ref="searchInputRef"
                :value="searchQuery"
                :placeholder="searchPlaceholder"
                type="text"
                class="flex h-8 w-full rounded-md border border-input bg-transparent px-2 py-1 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50"
                @input="handleSearchInput"
                @keydown="handleKeyDown"
                @click.stop
            />
        </div>
        <div class="max-h-[300px] overflow-y-auto p-1">
            <slot :search-query="searchQuery" />
        </div>
    </div>
</template>
