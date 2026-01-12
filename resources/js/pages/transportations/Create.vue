<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BDUIRenderer from '@/components/bdui/BDUIRenderer.vue';
import { useTransportationForm } from '@/composables/useTransportationForm';
import { Head } from '@inertiajs/vue3';
import { onMounted, watch, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';


const {
    currentStep,
    formData,
    stepData,
    errors,
    isLoading,
    currentStepIndex,
    steps,
    loadStep,
    nextStep,
    previousStep,
    confirmTransportation,
    initializeForm,
} = useTransportationForm();

const props = defineProps<{
    draftId: string;
    drafts?: Array<{
        id: string;
        name: string;
        step: string;
        updated_at: string;
    }>;
    draftList?: Array<{
        id: string;
        name: string;
        updated_at: string;
    }>;
}>();

function loadDraft() {
    return 22;
}

const currentStepData = computed(() => stepData.value[currentStep.value]);

const progress = computed(() => {
    return ((currentStepIndex.value + 1) / steps.length) * 100;
});

const handleFieldUpdate = (code: string, value: any) => {
    formData.value[code] = value;
};

const handleButtonClick = async (buttonId: string, actionType: string) => {
    switch (actionType) {
        case 'nextStep':
            await nextStep();
            break;
        case 'previousStep':
            await previousStep();
            break;
        case 'confirm':
            await confirmTransportation();
            break;
    }
};

onMounted(async () => {
    await initializeForm(props.draftId);
});

// Watch for step changes and load new step data
watch(
    currentStep,
    async (newStep) => {
        if (!stepData.value[newStep]) {
            await loadStep(newStep);
        }
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Create Transportation" />

    <AppLayout>
        <div class="container mx-auto max-w-7xl px-4 py-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Create Transportation</CardTitle>
                                <div class="text-sm text-muted-foreground">
                                    Step {{ currentStepIndex + 1 }} of {{ steps.length }}
                                </div>
                            </div>
                            <!-- Draft Selector -->
                            <div v-if="draftList?.length > 0" class="mt-4">
                                <label class="mb-2 block text-sm font-medium">Load Saved Draft:</label>
                                <select
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    @change="(e) => loadDraft((e.target as HTMLSelectElement).value)"
                                >
                                    <option value="">-- Select a draft --</option>
                                    <option
                                        v-for="draft in draftList"
                                        :key="draft.id"
                                        :value="draft.id"
                                    >
                                        {{ draft.name }} ({{ new Date(draft.updated_at).toLocaleDateString() }})
                                    </option>
                                </select>
                            </div>
                            <Progress :model-value="progress" class="mt-4" />
                        </CardHeader>
                        <CardContent>
                            <BDUIRenderer
                                v-if="currentStepData"
                                :step="currentStepData"
                                :form-data="formData"
                                :errors="errors"
                                @update-field="handleFieldUpdate"
                                @button-click="handleButtonClick"
                            />
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <Card v-if="currentStepData?.sidebar">
                        <CardHeader>
                            <CardTitle v-if="currentStepData.sidebar.progress">
                                {{ currentStepData.sidebar.progress.header }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <!-- Progress -->
                            <div v-if="currentStepData.sidebar.progress">
                                <div class="mb-2 text-sm font-medium">
                                    {{ currentStepData.sidebar.progress.stepName }}
                                </div>
                                <Progress
                                    :model-value="currentStepData.sidebar.progress.progress"
                                />
                            </div>

                            <!-- Advice -->
                            <div
                                v-if="currentStepData.sidebar.advice"
                                class="rounded-lg border p-4"
                            >
                                <h3 class="mb-2 font-semibold">
                                    {{ currentStepData.sidebar.advice.header }}
                                </h3>
                                <p
                                    v-for="(text, index) in currentStepData.sidebar.advice.text"
                                    :key="index"
                                    class="text-sm text-muted-foreground"
                                >
                                    <template
                                        v-for="(element, elIndex) in text.elements"
                                        :key="elIndex"
                                    >
                                        <span
                                            :class="
                                                element.styles?.includes('bold')
                                                    ? 'font-bold'
                                                    : ''
                                            "
                                        >
                                            {{ element.text }}
                                        </span>
                                        <br v-if="element.breakAfterText" />
                                    </template>
                                </p>
                                <div class="mt-4 h-32 w-full">
                                    <img
                                        v-if="currentStepData.sidebar.advice.imageLink"
                                        :src="'/img/' + currentStepData.sidebar.advice.imageLink"
                                        alt=""
                                        class="w-auto h-full mx-auto rounded object-cover"
                                    />
                                </div>
                            </div>

                            <!-- Help -->
                            <div
                                v-if="currentStepData.sidebar.help"
                                class="rounded-lg border p-4 hidden"
                            >
                                <h3 class="mb-2 font-semibold">
                                    {{ currentStepData.sidebar.help.title }}
                                </h3>
                                <p class="mb-4 text-sm text-muted-foreground">
                                    {{ currentStepData.sidebar.help.previewText }}
                                </p>
                                <!-- TODO: Implement help form -->
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
