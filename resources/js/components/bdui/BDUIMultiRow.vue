<script setup lang="ts">
import type { BDUIMultiRow, Row } from '@/types/bdui';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { computed, ref } from 'vue';
import { Plus, Trash2 } from 'lucide-vue-next';
import BDUIRenderer from './BDUIRenderer.vue';

interface Props {
    field: BDUIMultiRow;
    modelValue?: Array<Record<string, any>>;
    error?: string;
    errors?: Record<string, string>; // All errors from parent
    formData: Record<string, any>;
    onUpdate?: (code: string, value: any) => void;
    stepSource?: any; // Source from parent step for nested components
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: Array<Record<string, any>>): void;
}>();

const rows = ref<Array<Record<string, any>>>(
    props.modelValue && props.modelValue.length > 0
        ? [...props.modelValue]
        : [{}],
);

const canAddRow = computed(() => {
    if (!props.field.additionButton?.visibilityCondition) return true;
    // Simplified - in production, evaluate the expression properly
    return rows.value.length < 10; // reasonable limit
});

const canRemoveRow = computed(() => {
    if (!props.field.deletionButton?.visibilityCondition) return true;
    return rows.value.length > 1;
});

const addRow = () => {
    // Add a new empty row
    rows.value.push({});
    emit('update:modelValue', rows.value);

    // Also update parent form data if onUpdate is provided
    if (props.onUpdate && props.field.code) {
        props.onUpdate(props.field.code, rows.value);
    }
};

const removeRow = (index: number) => {
    if (rows.value.length > 1) {
        rows.value.splice(index, 1);
        emit('update:modelValue', rows.value);

        // Also update parent form data if onUpdate is provided
        if (props.onUpdate && props.field.code) {
            props.onUpdate(props.field.code, rows.value);
        }
    }
};

const updateRow = (index: number, fieldCode: string, value: any) => {
    if (!rows.value[index]) {
        rows.value[index] = {};
    }
    rows.value[index][fieldCode] = value;
    emit('update:modelValue', rows.value);

    // Update parent form data with the full array
    if (props.onUpdate && props.field.code) {
        props.onUpdate(props.field.code, rows.value);
    }
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

// Get errors for a specific row index
const getRowErrors = (rowIndex: number) => {
    if (!props.errors || !props.field.code) return {};

    const rowErrors: Record<string, string> = {};
    const errorPrefix = `${props.field.code}.${rowIndex}.`;

    for (const [key, value] of Object.entries(props.errors)) {
        if (key.startsWith(errorPrefix)) {
            // Extract field code from error key (e.g., "cargos.0.length" -> "length")
            const fieldCode = key.substring(errorPrefix.length);
            rowErrors[fieldCode] = value;
        }
    }

    return rowErrors;
};

// Get rows for a specific index (first row uses field.rows, others use additionalRow)
const getRowsForIndex = (index: number) => {
    if (index === 0) {
        // First row uses the main rows structure
        return props.field.rows;
    }

    // Additional rows use the additionalRow structure
    if (props.field.additionButton?.additionalRow) {
        const additionalRow = props.field.additionButton.additionalRow;

        if (additionalRow.columns && Array.isArray(additionalRow.columns)) {
            // Convert additionalRow.columns to rows format
            // Structure can be mixed: some items are columns directly, some have nested columns
            const rows: Row[] = [];

            for (const item of additionalRow.columns) {
                if (item.columns && Array.isArray(item.columns)) {
                    // Item has nested columns - it's a row
                    rows.push({ columns: item.columns });
                } else if (item.components) {
                    // Item is a column directly - wrap it in a row
                    rows.push({ columns: [item] });
                }
            }

            return rows.length > 0 ? rows : props.field.rows;
        }
    }

    // Fallback to regular rows if additionalRow is not defined
    return props.field.rows;
};
</script>

<template>
    <div :class="cn('flex flex-col gap-4', marginClass)">
        <div
            v-for="(row, rowIndex) in rows"
            :key="rowIndex"
            class="flex flex-col gap-4 rounded-lg border p-4"
        >
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-medium">
                    {{ field.label?.text || 'Cargo' }} {{ rowIndex + 1 }}
                </h4>
                <Button
                    v-if="canRemoveRow && rows.length > 1"
                    variant="ghost"
                    size="sm"
                    @click="removeRow(rowIndex)"
                >
                    <Trash2 class="h-4 w-4" />
                    <span class="ml-2">{{
                        field.deletionButton?.text || 'Remove'
                    }}</span>
                </Button>
            </div>

            <BDUIRenderer
                :rows="getRowsForIndex(rowIndex)"
                :source="stepSource"
                :form-data="{ ...formData, ...row }"
                :errors="getRowErrors(rowIndex)"
                :row-index="rowIndex"
                @update-field="
                    (code, value) => updateRow(rowIndex, code, value)
                "
            />
        </div>

        <Button
            v-if="canAddRow && field.additionButton"
            variant="outline"
            size="sm"
            @click="addRow"
        >
            <Plus class="h-4 w-4" />
            <span class="ml-2">{{ field.additionButton.text }}</span>
        </Button>

        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
    </div>
</template>
