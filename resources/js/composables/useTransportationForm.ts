import type { BDUIStep } from '@/types/bdui';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { CREATE_TRANSPORTATION_URL } from '@/pages/Routes/routes';
import { RESPONSE } from '@/pages/Response/response';
import { useTransportationSteps } from '@/composables/useTransportationSteps';

export interface FormData {
    [key: string]: any;
}

export function useTransportationForm() {
    const currentStep = ref<string>('transportationStep');
    const formData = ref<FormData>({});
    const stepData = ref<Record<string, BDUIStep>>({});
    const errors = ref<Record<string, string>>({});
    const isLoading = ref(false);
    const draftId = ref<string>('');

    const { steps, getRoute } = useTransportationSteps(draftId.value);
    // const steps = ['transportationStep', 'pickupAddressStep', 'deliveryAddressStep', 'cargoStep', 'confirmStep'];
    const currentStepIndex = computed(() => steps.indexOf(currentStep.value));

    // Load step data
    const loadStep = async (stepId: string) => {
        try {
            const uri = CREATE_TRANSPORTATION_URL.GET_FORM_SCHEMA_BY_STEP + stepId;
            const response = await fetch(uri);
            if (response.ok) {
                const data: BDUIStep = await response.json();
                stepData.value[stepId] = data;
                if (data.draftId) {
                    draftId.value = data.draftId;
                }
                return data;
            }
        } catch (error) {
            console.error('Error loading step:', error);
        }
    };

    // Save draft
    const saveDraft = async (): Promise<object> => {
        if (!draftId.value) {
            errors.value['draftId'] = 'No draft ID provided';
            return errors;
        }

        errors.value = {};

        try {
            const response = await fetch(getRoute(currentStep.value), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    ...formData.value,
                }),
            });

            const result = await response.json();

            if (!response.ok) {
                // Handle validation errors
                if (result.errors) {
                    // Convert Laravel validation errors format to simple key-value pairs
                    const formattedErrors: Record<string, string> = {};
                    for (const [index, error] of Object.entries(result.errors)) {
                        formattedErrors[error.source.pointer] = error.detail;

                        // if (Array.isArray(messages) && messages.length > 0) {
                        //     formattedErrors[key] = messages[0] as string;
                        // } else if (typeof messages === 'string') {
                        //     formattedErrors[key] = messages;
                        // }
                    }
                    errors.value = formattedErrors;
                }
                return false;
            }
            console.log('result', result);

            draftId.value = result.id;
            return result;
        } catch (error) {
            console.error('Error saving draft:', error);
            return false;
        }
    };

    // Navigate to step
    const goToStep = async (stepId: string) => {
        if (!stepData.value[stepId]) {
            await loadStep(stepId);
        }
        currentStep.value = stepId;
    };

    // Next step
    const nextStep = async (): Promise<boolean> => {
        const saved = await saveDraft();
        console.log('saved', saved);
        // If validation failed, don't proceed to next step
        if (!saved.id) {
            return false;
        }

        const nextIndex = currentStepIndex.value + 1;
        if (nextIndex < steps.length) {
            await goToStep(steps[nextIndex]);
            return true;
        }
        return false;
    };

    // Previous step
    const previousStep = async () => {
        await saveDraft();

        const prevIndex = currentStepIndex.value - 1;
        if (prevIndex >= 0) {
            await goToStep(steps[prevIndex]);
            return true;
        }
        return false;
    };

    // Confirm transportation
    const confirmTransportation = async () => {
        isLoading.value = true;
        errors.value = {};

        try {
            await router.post(CREATE_TRANSPORTATION_URL.CONFIRM_TRANSPORTATION, {
                id: draftId.value,
            });
        } catch (error: any) {
            if (error.response?.data?.errors) {
                errors.value = error.response.data.errors;
            }
            throw error;
        } finally {
            isLoading.value = false;
        }
    };

    // Initialize form
    const initializeForm = async (initialDraftId?: string) => {
        if (initialDraftId) {
            draftId.value = initialDraftId;
            // Load draft data
            try {
                const uri = CREATE_TRANSPORTATION_URL.GET_TRANSPORTATION_DRAFT_BY_ID + initialDraftId;
                const response = await fetch(uri);
                if (response.status === RESPONSE.HTTP_OK) {
                    const draft = await response.json();
                    formData.value = draft.data || {};
                    currentStep.value = draft.step || steps[0];
                }
            } catch (error) {
                console.error('Error loading draft:', error);
            }
        }

        // Load first step
        await loadStep(currentStep.value);
    };

    return {
        currentStep,
        formData,
        stepData,
        errors,
        isLoading,
        draftId,
        currentStepIndex,
        steps,
        loadStep,
        saveDraft,
        goToStep,
        nextStep,
        previousStep,
        confirmTransportation,
        initializeForm,
    };
}
