# BDUI Components

This directory contains the Vue components for rendering BDUI (Backend Driven UI) forms based on JSON configuration files.

## Components

### Field Components

- **BDUIInput.vue** - Text input fields
- **BDUINumber.vue** - Numeric input with units (cm, kg, etc.)
- **BDUIDateTime.vue** - Date and time pickers
- **BDUISelect.vue** - Dropdown select fields
- **BDUITextarea.vue** - Multi-line text inputs
- **BDUIMultiGeo.vue** - Address/geolocation picker
- **BDUIMultiRow.vue** - Repeating row component (for cargos)

### Static Components

- **BDUIButton.vue** - Action buttons (next, previous, confirm)
- **BDUITypography.vue** - Text elements with styling

### Renderer

- **BDUIRenderer.vue** - Main renderer that processes BDUI JSON and renders appropriate components

## Usage

```vue
<script setup>
import BDUIRenderer from '@/components/dbui/BDUIRenderer.vue';
import { useTransportationForm } from '@/composables/useTransportationForm';

const { formData, stepData, errors } = useTransportationForm();

const handleFieldUpdate = (code, value) => {
  formData[code] = value;
};
</script>

<template>
  <BDUIRenderer
    :step="stepData"
    :form-data="formData"
    :errors="errors"
    @update-field="handleFieldUpdate"
  />
</template>
```

## Features

- **Automatic Validation** - Fields validate based on conditions in JSON
- **Responsive Layout** - Grid system with breakpoints (xs, m, l)
- **Type Safety** - Full TypeScript support
- **Form State Management** - Integrated with `useTransportationForm` composable
- **Error Display** - Shows validation errors inline

## Component Props

### BDUIRenderer

- `step` (BDUIStep) - The step configuration
- `formData` (Record<string, any>) - Current form values
- `errors` (Record<string, string>) - Validation errors
- `rows` (Row[]) - Optional rows override
- `source` (any) - Optional source override

### Field Components

All field components accept:
- `field` - Field configuration from JSON
- `modelValue` - Current value
- `error` - Error message (optional)

## Events

- `update-field` - Emitted when a field value changes
- `button-click` - Emitted when a button is clicked

## Styling

Components use Tailwind CSS and follow the existing design system. They integrate with:
- UI components from `@/components/ui`
- Utility functions from `@/lib/utils`
- Design tokens from the theme
