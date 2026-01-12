export interface BDUISource {
    button?: BDUIButton[];
    input?: BDUIInput[];
    number?: BDUINumber[];
    dateTime?: BDUIDateTime[];
    select?: BDUISelect[];
    textarea?: BDUITextarea[];
    multiGeo?: BDUIMultiGeo[];
    multiRow?: BDUIMultiRow[];
    typography?: BDUITypography[];
    reviewCard?: BDUIReviewCard[];
    oneChoice?: BDUIOneChoice[];
    delimiter?: BDUIDelimiter[];
    accordion?: BDUIAccordion[];
    banner?: BDUIBanner[];
}

export interface BDUIButton {
    id: string;
    category: string;
    margin?: Margin;
    text: string;
    actionType: 'nextStep' | 'previousStep' | 'confirm' | string;
    size?: 'small' | 'medium' | 'large';
    theme?: 'fill_primary' | 'fill_white_primary' | 'fill_secondary' | string;
    fullWidth?: boolean;
    beforeIcon?: Icon;
    eventMapping?: EventMapping;
}

export interface BDUIInput {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    flexProperties?: FlexProperties;
    code: string;
    label: Label;
    placeholder?: string;
    maxLength?: number;
    value?: any;
}

export interface BDUINumber {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    flexProperties?: FlexProperties;
    code: string;
    label: Label;
    units?: Units;
    decimalDigits?: number;
    maxLength?: number;
    value?: NumberValue;
}

export interface BDUIDateTime {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    flexProperties?: FlexProperties;
    code: string;
    label: Label;
    placeholder?: string;
    showTime?: boolean;
    minDate?: string | 'today' | 'pickupFrom';
}

export interface BDUISelect {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    flexProperties?: FlexProperties;
    code: string;
    label: Label;
    placeholder?: string;
    source?: string;
    optionsKey?: string;
    optionsLabel?: string;
    dependsOn?: string;
}

export interface BDUITextarea {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    flexProperties?: FlexProperties;
    code: string;
    label: Label;
    placeholder?: string;
    minRows?: number;
    maxRows?: number;
}

export interface BDUIMultiGeo {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    flexProperties?: FlexProperties;
    code: string;
    label: Label;
    placeholder?: string;
    params?: {
        zoomLevel?: number;
        suggestSchema?: Record<string, number>;
    };
    callbacks?: {
        onChange?: {
            type: string;
            callbackId: string;
        };
    };
}

export interface BDUIMultiRow {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    code: string;
    rows: Row[];
    isRowCollapsable?: boolean;
    additionButton?: BDUIButton;
    deletionButton?: BDUIButton;
}

export interface BDUITypography {
    id: string;
    category: string;
    margin?: Margin;
    flexProperties?: FlexProperties;
    source: TypographySource;
    elements: TypographyElement[];
    icon?: {
        id: string;
        type: string;
    };
}

export interface BDUIReviewCard {
    id: string;
    category: string;
    margin?: Margin;
    code: string;
    template: {
        title: string;
        fields: Array<{
            label: string;
            value: string;
        }>;
    };
}

export interface BDUIOneChoice {
    id: string;
    category: string;
    conditions?: Conditions;
    margin?: Margin;
    maxWidth?: number;
    flexProperties?: FlexProperties;
    code: string;
    label: Label;
    style?: string;
    source: Array<{
        value: string;
        label: string;
    }>;
}

export interface BDUIDelimiter {
    id: string;
    category: string;
    margin?: Margin;
    flexProperties?: FlexProperties;
}

export interface BDUIAccordion {
    id: string;
    category: string;
    margin?: Margin;
    header: string;
    paragraphs?: Array<{
        elements: Array<{
            text: string;
            breakAfterText?: boolean;
            styles?: string[];
        }>;
    }>;
}

export interface BDUIBanner {
    id: string;
    category: string;
    margin?: Margin;
    title: string;
    description?: Array<{
        elements: Array<{
            text: string;
            breakAfterText?: boolean;
            styles?: string[];
        }>;
    }>;
    button?: BDUIButton;
    canClose?: boolean;
    closeOnSuccess?: boolean;
    backgroundColor?: string;
    textColor?: string;
}

export interface Margin {
    top?: number;
    bottom?: number;
    left?: number;
    right?: number;
}

export interface FlexProperties {
    grow?: number;
    basis?: string;
    shrink?: number;
}

export interface Label {
    text: string;
}

export interface Units {
    default: {
        value: string;
        label: string;
    };
}

export interface NumberValue {
    value: number;
    unit?: string;
}

export interface Conditions {
    required?: Array<{
        expression: string;
        error: string;
    }>;
    validity?: Array<{
        expression: string;
        error: string;
    }>;
    editability?: Array<{
        expression: string;
    }>;
}

export interface EventMapping {
    click?: EventData;
}

export interface EventData {
    id: string;
    category: string;
    action: string;
    label: string;
    page: {
        siteType: string;
        pageType: string;
        extra: Record<string, any>;
    };
    shouldEnrich?: boolean;
}

export interface Icon {
    link: string;
    width: number;
    height: number;
}

export interface TypographySource {
    text?: TypographyText[];
    icon?: Icon[];
    link?: TypographyLink[];
}

export interface TypographyText {
    id: string;
    breakAfter: boolean;
    text: string;
    style: string;
    color: string;
}

export interface TypographyLink {
    id: string;
    breakAfter: boolean;
    text: string;
    link: string;
    linkOptions?: {
        noRef?: boolean;
        noFollow?: boolean;
        newTab?: boolean;
    };
    style: string;
}

export interface TypographyElement {
    id: string;
    type: string;
}

export interface Row {
    columns: Column[];
}

export interface Column {
    components: ComponentReference[];
    gridPosition?: GridPosition;
    flexbox?: Flexbox;
    grid?: Grid;
}

export interface ComponentReference {
    id: string;
    type: string;
}

export interface GridPosition {
    xs?: number;
    m?: number;
    l?: number;
    xsHidden?: boolean;
    mHidden?: boolean;
    lHidden?: boolean;
    mOffset?: number;
    lOffset?: number;
}

export interface Flexbox {
    justify?: 'start' | 'end' | 'center' | 'between' | 'around';
    wrap?: 'wrap' | 'nowrap';
    columnGap?: string;
    rowGap?: string;
}

export interface Grid {
    cols?: number | string; // e.g., 4 or "4" for grid-cols-4, or "auto-fit" for auto-fit
    columnGap?: string;
    rowGap?: string;
    gap?: string; // Shorthand for both gaps
}

export interface BDUISidebar {
    advice?: {
        text: Array<{
            elements: Array<{
                text: string;
                breakAfterText?: boolean;
                styles?: string[];
            }>;
        }>;
        header: string;
        imageLink?: string;
    };
    progress?: {
        header: string;
        progress: number;
        stepName: string;
    };
    help?: {
        title: string;
        successTitle: string;
        source: TypographySource;
        elements: TypographyElement[];
        successButtonText: string;
        confirmButtonText: string;
        cancelButtonText: string;
        previewText: string;
    };
}

export interface BDUIEventMapping {
    load?: Array<{
        id: string;
        type: string;
    }>;
    source?: Record<string, any[]>;
}

export interface BDUIStep {
    source: BDUISource;
    rows: Row[];
    currentStepId: string;
    previousStepId?: string;
    eventMapping?: BDUIEventMapping;
    sidebar?: BDUISidebar;
    draftId?: string;
}
