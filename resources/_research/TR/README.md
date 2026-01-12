# Transportation BDUI Structure

This folder contains the BDUI (Backend Driven UI) structure for the transportation creation form, following the CIAN.ru approach.

## Overview

The transportation creation form consists of 4 steps:

1. **Step 1** (`step01.json`): Basic transportation information
   - Transportation name
   - Pickup interval (from/to dates)

2. **Step 2** (`step02.json`): Pickup Address information
   - Pickup address with all details
   - Contact information

3. **Step 3** (`step03.json`): Delivery Address information
   - Delivery address with all details
   - Contact information

4. **Step 4** (`step03.json`): Cargo information
   - Multi-row component for adding multiple cargos
   - Each cargo includes: name, dimensions (length, width, height), weight, price, and currency

5. **Step 5** (`step04.json`): Review and confirmation
   - Review all entered information
   - Confirm transportation (changes status from NEW to CONFIRMED/PROCESSING)

## Structure

Each JSON file follows the CIAN BDUI structure:

### Main Sections

1. **`source`**: Defines all component definitions organized by type
   - `button`: Navigation and action buttons
   - `input`: Text input fields
   - `number`: Numeric input fields with units
   - `dateTime`: Date and time pickers
   - `select`: Dropdown select fields
   - `textarea`: Multi-line text inputs
   - `multiGeo`: Address/geolocation fields
   - `multiRow`: Repeating row components (for cargos)
   - `typography`: Static text elements
   - `reviewCard`: Review card components

2. **`rows`**: Defines the layout using a grid system
   - Each row contains columns
   - Columns have grid positioning (responsive: xs, m, l)
   - Components are referenced by their ID

3. **`currentStepId`**: Identifier for the current step

4. **`previousStepId`**: Identifier for the previous step (for navigation)

5. **`eventMapping`**: Defines events and analytics tracking
   - `load`: Events triggered on step load
   - `source`: Event definitions

6. **`sidebar`**: Sidebar content
   - `advice`: Helpful tips
   - `progress`: Progress indicator
   - `help`: Help section

7. **`draftId`**: Draft identifier for saving progress

## Component Types

### Field Components
- **`input`**: Text input with validation
- **`number`**: Numeric input with units (cm, kg, etc.)
- **`dateTime`**: Date/time picker
- **`select`**: Dropdown with options from API
- **`textarea`**: Multi-line text
- **`multiGeo`**: Address/geolocation picker
- **`multiRow`**: Repeating rows (for cargos)

### Static Components
- **`typography`**: Text elements with styling
- **`button`**: Action buttons
- **`reviewCard`**: Review/display cards

## Validation

Each field component includes:
- **`conditions.required`**: Required field validation
- **`conditions.validity`**: Custom validation rules with expressions
- **`conditions.editability`**: Edit permissions

## Responsive Design

Components use a grid system with breakpoints:
- **xs**: Extra small (mobile)
- **m**: Medium (tablet)
- **l**: Large (desktop)

Components can be hidden on specific breakpoints using `xsHidden`, `mHidden`, `lHidden`.

## Data Flow

1. User fills form fields
2. Data is saved automatically (draft)
3. Navigation between steps preserves data
4. Final confirmation creates the transportation record
5. Status changes from `NEW` to `CONFIRMED`/`PROCESSING`

## Integration Notes

- Form data should be persisted as draft between steps
- Address fields use geocoding API (similar to CIAN's multiGeo)
- Currency and country/city selects should fetch from API
- Multi-row cargo component allows dynamic addition/removal
- Confirmation step should validate all required fields before submission

## Next Steps

To implement this in the frontend:
1. Create a BDUI renderer component that reads these JSON files
2. Map component types to Vue components
3. Implement form state management
4. Add API integration for data persistence
5. Implement validation logic based on conditions
6. Add step navigation logic
