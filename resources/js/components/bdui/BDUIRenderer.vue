<script setup lang="ts">
import type { BDUIStep, Row, ComponentReference } from '@/types/bdui';
import BDUIInput from './BDUIInput.vue';
import BDUINumber from './BDUINumber.vue';
import BDUIDateTime from './BDUIDateTime.vue';
import BDUISelect from './BDUISelect.vue';
import BDUITextarea from './BDUITextarea.vue';
import BDUIButton from './BDUIButton.vue';
import BDUITypography from './BDUITypography.vue';
import BDUIMultiRow from './BDUIMultiRow.vue';
import BDUIMultiGeo from './BDUIMultiGeo.vue';
import BDUIDelimiter from './BDUIDelimiter.vue';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    step?: BDUIStep;
    rows?: Row[];
    source?: any;
    formData: Record<string, any>;
    errors?: Record<string, string>;
    rowIndex?: number;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update-field', code: string, value: any): void;
    (e: 'button-click', buttonId: string, actionType: string): void;
}>();

const rowsToRender = computed(() => {
    return props.rows || props.step?.rows || [];
});

const source = computed(() => {
    return props.source || props.step?.source;
});

const getComponentById = (id: string, type: string) => {
    if (!source.value) return null;

    const typeMap: Record<string, string> = {
        input: 'input',
        number: 'number',
        dateTime: 'dateTime',
        select: 'select',
        textarea: 'textarea',
        button: 'button',
        typography: 'typography',
        multiRow: 'multiRow',
        multiGeo: 'multiGeo',
        delimiter: 'delimiter',
    };

    const sourceKey = typeMap[type];
    if (!sourceKey || !source.value[sourceKey]) return null;

    return source.value[sourceKey].find((item: any) => item.id === id);
};

const getGridPositionClass = (gridPosition?: any) => {
    if (!gridPosition) return '';
    return cn(
        gridPosition.xs && `col-span-${gridPosition.xs}`,
        gridPosition.m && `md:col-span-${gridPosition.m}`,
        gridPosition.l && `lg:col-span-${gridPosition.l}`,
        gridPosition.xsHidden && 'hidden',
        gridPosition.mHidden && 'md:hidden',
        gridPosition.lHidden && 'lg:hidden',
        gridPosition.mOffset && `md:col-start-${gridPosition.mOffset + 1}`,
        gridPosition.lOffset && `lg:col-start-${gridPosition.lOffset + 1}`,
    );
};

const getFlexboxClass = (flexbox?: any) => {
    if (!flexbox) return '';
    return cn(
        'flex',
        flexbox.direction === 'col-row' && 'grid md:grid-cols-4 grid-cols-2 gap-3',
        flexbox.justify === 'start' && 'justify-start',
        flexbox.justify === 'end' && 'justify-end',
        flexbox.justify === 'center' && 'justify-center',
        flexbox.justify === 'between' && 'justify-between',
        flexbox.justify === 'around' && 'justify-around',
        flexbox.wrap === 'wrap' && 'flex-wrap',
        flexbox.columnGap && `gap-x-[${flexbox.columnGap}]`,
        flexbox.rowGap && `gap-y-[${flexbox.rowGap}]`,
    );
};

const getGridClass = (grid?: any) => {
    if (!grid) return '';
    
    const classes: string[] = ['grid'];
    
    // Grid columns
    if (grid.cols) {
        if (typeof grid.cols === 'number') {
            // Use arbitrary values for dynamic numbers to ensure Tailwind includes them
            classes.push(`grid-cols-[repeat(${grid.cols},minmax(0,1fr))]`);
        } else if (grid.cols === 'auto-fit') {
            classes.push('grid-cols-[repeat(auto-fit,minmax(0,1fr))]');
        } else if (grid.cols === 'auto-fill') {
            classes.push('grid-cols-[repeat(auto-fill,minmax(0,1fr))]');
        } else {
            // Try to use standard Tailwind classes for common values
            const standardCols = ['1', '2', '3', '4', '5', '6', '12'];
            if (standardCols.includes(String(grid.cols))) {
                classes.push(`grid-cols-${grid.cols}`);
            } else {
                classes.push(`grid-cols-[repeat(${grid.cols},minmax(0,1fr))]`);
            }
        }
    } else {
        // Default to auto-fit if no cols specified
        classes.push('grid-cols-[repeat(auto-fit,minmax(0,1fr))]');
    }
    
    // Gap handling
    if (grid.gap) {
        classes.push(`gap-[${grid.gap}]`);
    } else {
        if (grid.columnGap) {
            classes.push(`gap-x-[${grid.columnGap}]`);
        }
        if (grid.rowGap) {
            classes.push(`gap-y-[${grid.rowGap}]`);
        }
    }
    
    return cn(...classes);
};

const handleFieldUpdate = (code: string, value: any) => {
    emit('update-field', code, value);
};

const handleButtonClick = (buttonId: string, actionType: string) => {
    emit('button-click', buttonId, actionType);
};
</script>

<template>
    <div class="grid grid-cols-1 gap-4">
        <template v-for="(row, rowIdx) in rowsToRender" :key="rowIdx">
            <template v-for="(column, colIdx) in row.columns" :key="colIdx">
                <div
                    :class="cn(
                        getGridPositionClass(column.gridPosition),
                        column.grid ? getGridClass(column.grid) : getFlexboxClass(column.flexbox),
                    )"
                >
                    <template
                        v-for="(component, compIdx) in column.components"
                        :key="compIdx"
                    >
                        <!-- Input -->
                        <BDUIInput
                            v-if="component.type === 'input'"
                            :field="getComponentById(component.id, component.type)"
                            :model-value="formData[getComponentById(component.id, component.type)?.code]"
                            :error="errors?.[getComponentById(component.id, component.type)?.code]"
                            @update:model-value="
                                handleFieldUpdate(
                                    getComponentById(component.id, component.type)?.code,
                                    $event,
                                )
                            "
                        />

                        <!-- Number -->
                        <BDUINumber
                            v-else-if="component.type === 'number'"
                            :field="getComponentById(component.id, component.type)"
                            :model-value="formData[getComponentById(component.id, component.type)?.code]"
                            :error="errors?.[getComponentById(component.id, component.type)?.code]"
                            @update:model-value="
                                handleFieldUpdate(
                                    getComponentById(component.id, component.type)?.code,
                                    $event,
                                )
                            "
                        />

                        <!-- DateTime -->
                        <BDUIDateTime
                            v-else-if="component.type === 'dateTime'"
                            :field="getComponentById(component.id, component.type)"
                            :model-value="formData[getComponentById(component.id, component.type)?.code]"
                            :error="errors?.[getComponentById(component.id, component.type)?.code]"
                            @update:model-value="
                                handleFieldUpdate(
                                    getComponentById(component.id, component.type)?.code,
                                    $event,
                                )
                            "
                        />

                        <!-- Select -->
                        <BDUISelect
                            v-else-if="component.type === 'select'"
                            :field="getComponentById(component.id, component.type)"
                            :model-value="formData[getComponentById(component.id, component.type)?.code]"
                            :error="errors?.[getComponentById(component.id, component.type)?.code]"
                            :form-data="formData"
                            @update:model-value="
                                handleFieldUpdate(
                                    getComponentById(component.id, component.type)?.code,
                                    $event,
                                )
                            "
                        />

                        <!-- Textarea -->
                        <BDUITextarea
                            v-else-if="component.type === 'textarea'"
                            :field="getComponentById(component.id, component.type)"
                            :model-value="formData[getComponentById(component.id, component.type)?.code]"
                            :error="errors?.[getComponentById(component.id, component.type)?.code]"
                            @update:model-value="
                                handleFieldUpdate(
                                    getComponentById(component.id, component.type)?.code,
                                    $event,
                                )
                            "
                        />

                        <!-- Button -->
                        <BDUIButton
                            v-else-if="component.type === 'button'"
                            :button="getComponentById(component.id, component.type)"
                            @click="
                                handleButtonClick(
                                    component.id,
                                    getComponentById(component.id, component.type)?.actionType,
                                )
                            "
                        />

                        <!-- Typography -->
                        <BDUITypography
                            v-else-if="component.type === 'typography'"
                            :typography="getComponentById(component.id, component.type)"
                        />

                        <!-- MultiRow -->
                        <BDUIMultiRow
                            v-else-if="component.type === 'multiRow'"
                            :field="getComponentById(component.id, component.type)"
                            :model-value="formData[getComponentById(component.id, component.type)?.code]"
                            :error="errors?.[getComponentById(component.id, component.type)?.code]"
                            :errors="errors"
                            :form-data="formData"
                            :step-source="source"
                            :on-update="handleFieldUpdate"
                            @update:model-value="
                                handleFieldUpdate(
                                    getComponentById(component.id, component.type)?.code,
                                    $event,
                                )
                            "
                        />

                        <!-- MultiGeo -->
                        <BDUIMultiGeo
                            v-else-if="component.type === 'multiGeo'"
                            :field="getComponentById(component.id, component.type)"
                            :model-value="formData[getComponentById(component.id, component.type)?.code]"
                            :error="errors?.[getComponentById(component.id, component.type)?.code]"
                            @update:model-value="
                                handleFieldUpdate(
                                    getComponentById(component.id, component.type)?.code,
                                    $event,
                                )
                            "
                        />

                        <!-- Delimiter -->
                        <BDUIDelimiter
                            v-else-if="component.type === 'delimiter'"
                            :delimiter="getComponentById(component.id, component.type)"
                        />
                    </template>
                </div>
            </template>
        </template>
    </div>
</template>
