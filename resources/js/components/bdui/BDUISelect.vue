<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { BDUISelect } from '@/types/bdui';
import { cn } from '@/lib/utils';
import { computed, ref, watch, onMounted } from 'vue';

interface Props {
    field: BDUISelect;
    modelValue?: string | number;
    error?: string;
    options?: Array<Record<string, any>>;
    formData?: Record<string, any>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();

const localOptions = ref<Array<Record<string, any>>>(props.options || []);
const isLoading = ref(false);
const searchQuery = ref('');
const isSearchable = computed(() => {
    // Make searchable if there are more than 5 options
    return localOptions.value.length > 5;
});

// Load options from API
const loadOptions = async () => {
    if (!props.field.source || props.options) {
        return;
    }

    isLoading.value = true;
    
    try {
        let url = `/api/${props.field.source}`;
        
        // Handle dependencies (e.g., cities depend on country)
        if (props.field.dependsOn && props.formData) {
            const dependsOnValue = props.formData[props.field.dependsOn];
            if (dependsOnValue) {
                url += `?${props.field.dependsOn}=${dependsOnValue}`;
            } else {
                // If dependency is not set, clear options
                localOptions.value = [];
                isLoading.value = false;
                return;
            }
        }

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Failed to load options: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Loaded options data:', data);
        
        localOptions.value = Array.isArray(data) ? data : data.data || [];
    } catch (err) {
        console.error('Error loading options:', err);
        localOptions.value = [];
    } finally {
        isLoading.value = false;
    }
};

// Load options on mount
onMounted(() => {
    loadOptions();
});

// Watch for dependency changes (e.g., when country changes, reload cities)
watch(
    () => props.field.dependsOn && props.formData?.[props.field.dependsOn],
    () => {
        if (props.field.dependsOn) {
            loadOptions();
        }
    },
    { immediate: false },
);

// Watch for source changes
watch(
    () => props.field.source,
    () => {
        if (props.field.source && !props.options) {
            loadOptions();
        }
    },
);

// Watch for options prop changes (if passed from parent)
watch(
    () => props.options,
    (newOptions) => {
        if (newOptions) {
            localOptions.value = newOptions;
        }
    },
    { immediate: true },
);

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

// Filter options based on search query
const filteredOptions = computed(() => {
    if (!searchQuery.value.trim()) {
        return localOptions.value;
    }

    const query = searchQuery.value.toLowerCase();
    const labelKey = props.field.optionsLabel || 'name';
    
    return localOptions.value.filter((option) => {
        const label = String(option[labelKey] || '').toLowerCase();
        return label.includes(query);
    });
});

// Get display value for selected option
const displayValue = computed(() => {
    if (!props.modelValue) {
        return '';
    }
    
    const selectedOption = localOptions.value.find(
        (opt) => String(opt[props.field.optionsKey || 'id']) === String(props.modelValue),
    );
    
    return selectedOption
        ? selectedOption[props.field.optionsLabel || 'name']
        : props.field.placeholder || 'Select...';
});
</script>

<template>
    <div :class="cn('flex flex-col gap-2', marginClass, flexClass)">
        <Label v-if="field.label?.text" :for="field.id">
            {{ field.label.text }}
        </Label>
        <div class="relative">
            <Select
                :model-value="String(modelValue || '')"
                :searchable="isSearchable"
                @update:model-value="emit('update:modelValue', $event)"
            >
                <SelectTrigger
                    :id="field.id"
                    :class="cn(error && 'border-destructive')"
                >
                    <SelectValue :display-value="displayValue" :placeholder="field.placeholder || 'Select...'" />
                </SelectTrigger>
                <SelectContent
                    :searchable="isSearchable"
                    :search-placeholder="`Search ${field.label?.text || 'options'}...`"
                    @update:search-query="searchQuery = $event"
                >
                    <template v-if="isLoading">
                        <div class="px-2 py-1.5 text-sm text-muted-foreground">
                            Loading...
                        </div>
                    </template>
                    <template v-else-if="localOptions.length === 0">
                        <div class="px-2 py-1.5 text-sm text-muted-foreground">
                            No options available
                        </div>
                    </template>
                    <template v-else-if="filteredOptions.length === 0">
                        <div class="px-2 py-1.5 text-sm text-muted-foreground">
                            No results found for "{{ searchQuery }}"
                        </div>
                    </template>
                    <SelectItem
                        v-for="option in filteredOptions"
                        v-else
                        :key="option[field.optionsKey || 'id']"
                        :value="String(option[field.optionsKey || 'id'])"
                    >
                        {{ option[field.optionsLabel || 'name'] }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
        <InputError v-if="error" :message="error" />
    </div>
</template>
