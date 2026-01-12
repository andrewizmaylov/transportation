<script setup lang="ts">
import type { BDUITypography } from '@/types/bdui';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    typography: BDUITypography;
}

const props = defineProps<Props>();

const marginClass = computed(() => {
    const m = props.typography.margin;
    if (!m) return '';
    return cn(
        m.top && `mt-${m.top}`,
        m.bottom && `mb-${m.bottom}`,
        m.left && `ml-${m.left}`,
        m.right && `mr-${m.right}`,
    );
});

const flexClass = computed(() => {
    const fp = props.typography.flexProperties;
    if (!fp) return '';
    return cn(
        fp.grow && `flex-${fp.grow}`,
        fp.basis && `basis-[${fp.basis}]`,
    );
});

const textStyleClass = computed(() => {
    const style = props.typography.source.text?.[0]?.style || 'UIText1';
    switch (style) {
        case 'UIHeading1':
            return 'text-3xl font-bold';
        case 'UIHeading2':
            return 'text-2xl font-semibold';
        case 'UIHeading3':
            return 'text-xl font-semibold';
        case 'UIText1':
            return 'text-base';
        case 'UIText2':
            return 'text-sm';
        case 'UIText3':
            return 'text-xs';
        default:
            return 'text-base';
    }
});

const textColorClass = computed(() => {
    const color = props.typography.source.text?.[0]?.color || 'black_100';
    switch (color) {
        case 'black_100':
            return 'text-foreground';
        case 'gray40_100':
        case 'gray60_100':
            return 'text-muted-foreground';
        default:
            return 'text-foreground';
    }
});
</script>

<template>
    <div :class="cn('flex items-center gap-2', marginClass, flexClass)">
        <img
            v-if="typography.icon && typography.source.icon"
            :src="typography.source.icon[0]?.link"
            :width="typography.source.icon[0]?.width"
            :height="typography.source.icon[0]?.height"
            alt=""
            class="flex-shrink-0"
        />
        <div :class="cn(textStyleClass, textColorClass)">
            <template v-for="(text, index) in typography.source.text" :key="index">
                <span v-if="!text.breakAfter">{{ text.text }}</span>
                <span v-else>{{ text.text }}<br /></span>
            </template>
            <template
                v-for="(link, index) in typography.source.link"
                :key="`link-${index}`"
            >
                <a
                    :href="link.link"
                    :target="link.linkOptions?.newTab ? '_blank' : undefined"
                    :rel="
                        link.linkOptions?.noRef && link.linkOptions?.noFollow
                            ? 'noreferrer nofollow'
                            : undefined
                    "
                    class="text-primary underline hover:text-primary/80"
                >
                    {{ link.text }}
                </a>
            </template>
        </div>
    </div>
</template>
