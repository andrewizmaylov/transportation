<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Actions;

class GetTranslatedRegionNameByLocale
{
    public static function translate($translations, $locale = null): string
    {
        $translations = is_string($translations)
            ? json_decode($translations, true)
            : $translations;

        $locale = $locale ? strtolower($locale) : strtolower(app()->getLocale());
        $locale = $locale === 'en' ? 'br' : $locale;

        if (! isset($translations[$locale])) {
            $locale = 'ru';
            \Log::debug('Getting translated region name from locale ' . $locale . json_encode($translations));
        }

        return $translations[$locale];
    }
}
