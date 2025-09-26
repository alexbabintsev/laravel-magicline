<?php

namespace AlexBabintsev\Magicline\Connect\Support;

class LocaleResolver
{
    /**
     * Country code to language mapping for fallback
     */
    private static array $countryToLanguage = [
        'DE' => 'de',
        'AT' => 'de',
        'CH' => 'de', // Default for Switzerland
        'US' => 'en',
        'GB' => 'en',
        'FR' => 'fr',
        'IT' => 'it',
        'ES' => 'es',
        'PT' => 'pt',
        'NL' => 'nl',
        'BE' => 'nl', // Default for Belgium
        'PL' => 'pl',
        'CZ' => 'cs',
        'SK' => 'sk',
        'HU' => 'hu',
        'SI' => 'sl',
        'HR' => 'hr',
        'RS' => 'sr',
        'RO' => 'ro',
        'BG' => 'bg',
        'GR' => 'el',
        'TR' => 'tr',
        'RU' => 'ru',
        'UA' => 'uk',
        'DK' => 'da',
        'SE' => 'sv',
        'NO' => 'no',
        'FI' => 'fi',
    ];

    /**
     * Resolve effective language from customer data
     * Priority: locale > language > countryCode fallback
     */
    public static function resolveLanguage(?string $locale, ?string $language, ?string $countryCode): ?string
    {
        // 1. Locale has highest priority (e.g., "de_CH", "fr_CH")
        if ($locale && self::isValidLocale($locale)) {
            return self::extractLanguageFromLocale($locale);
        }

        // 2. Language has medium priority (e.g., "de", "en")
        if ($language && self::isValidLanguage($language)) {
            return $language;
        }

        // 3. Country code fallback (e.g., "CH" -> "de")
        if ($countryCode && isset(self::$countryToLanguage[$countryCode])) {
            return self::$countryToLanguage[$countryCode];
        }

        return null;
    }

    /**
     * Resolve effective locale from customer data
     * Priority: locale > language+countryCode combination > defaults
     */
    public static function resolveLocale(?string $locale, ?string $language, ?string $countryCode): ?string
    {
        // 1. Locale provided directly
        if ($locale && self::isValidLocale($locale)) {
            return $locale;
        }

        // 2. Combine language and country
        if ($language && $countryCode) {
            $combined = $language.'_'.$countryCode;
            if (self::isValidLocale($combined)) {
                return $combined;
            }
        }

        // 3. Language only, try to find common locale
        if ($language) {
            return self::getCommonLocaleForLanguage($language);
        }

        // 4. Country only, use default locale
        if ($countryCode) {
            return self::getDefaultLocaleForCountry($countryCode);
        }

        return null;
    }

    /**
     * Validate locale format (e.g., "de_CH", "en_US")
     */
    public static function isValidLocale(string $locale): bool
    {
        return preg_match('/^[a-z]{2}_[A-Z]{2}$/', $locale) === 1;
    }

    /**
     * Validate language format (e.g., "de", "en")
     */
    public static function isValidLanguage(string $language): bool
    {
        return preg_match('/^[a-z]{2}$/', $language) === 1;
    }

    /**
     * Extract language from locale (e.g., "de_CH" -> "de")
     */
    public static function extractLanguageFromLocale(string $locale): string
    {
        return explode('_', $locale)[0];
    }

    /**
     * Extract country from locale (e.g., "de_CH" -> "CH")
     */
    public static function extractCountryFromLocale(string $locale): string
    {
        return explode('_', $locale)[1];
    }

    /**
     * Get common locale for language
     */
    private static function getCommonLocaleForLanguage(string $language): ?string
    {
        $commonLocales = [
            'de' => 'de_DE',
            'en' => 'en_US',
            'fr' => 'fr_FR',
            'it' => 'it_IT',
            'es' => 'es_ES',
            'pt' => 'pt_PT',
            'nl' => 'nl_NL',
            'pl' => 'pl_PL',
            'cs' => 'cs_CZ',
            'sk' => 'sk_SK',
            'hu' => 'hu_HU',
            'sl' => 'sl_SI',
            'hr' => 'hr_HR',
            'sr' => 'sr_RS',
            'ro' => 'ro_RO',
            'bg' => 'bg_BG',
            'el' => 'el_GR',
            'tr' => 'tr_TR',
            'ru' => 'ru_RU',
            'uk' => 'uk_UA',
            'da' => 'da_DK',
            'sv' => 'sv_SE',
            'no' => 'no_NO',
            'fi' => 'fi_FI',
        ];

        return $commonLocales[$language] ?? null;
    }

    /**
     * Get default locale for country
     */
    private static function getDefaultLocaleForCountry(string $countryCode): ?string
    {
        $countryLocales = [
            'DE' => 'de_DE',
            'AT' => 'de_AT',
            'CH' => 'de_CH', // Default, could be fr_CH or it_CH
            'US' => 'en_US',
            'GB' => 'en_GB',
            'FR' => 'fr_FR',
            'IT' => 'it_IT',
            'ES' => 'es_ES',
            'PT' => 'pt_PT',
            'NL' => 'nl_NL',
            'BE' => 'nl_BE', // Default, could be fr_BE
            'PL' => 'pl_PL',
            'CZ' => 'cs_CZ',
            'SK' => 'sk_SK',
            'HU' => 'hu_HU',
            'SI' => 'sl_SI',
            'HR' => 'hr_HR',
            'RS' => 'sr_RS',
            'RO' => 'ro_RO',
            'BG' => 'bg_BG',
            'GR' => 'el_GR',
            'TR' => 'tr_TR',
            'RU' => 'ru_RU',
            'UA' => 'uk_UA',
            'DK' => 'da_DK',
            'SE' => 'sv_SE',
            'NO' => 'no_NO',
            'FI' => 'fi_FI',
        ];

        return $countryLocales[$countryCode] ?? null;
    }

    /**
     * Prepare customer data with resolved language/locale
     */
    public static function prepareCustomerData(array $customer): array
    {
        $locale = $customer['locale'] ?? null;
        $language = $customer['language'] ?? null;
        $countryCode = $customer['countryCode'] ?? $customer['address']['country'] ?? null;

        $resolved = [
            'language' => self::resolveLanguage($locale, $language, $countryCode),
            'locale' => self::resolveLocale($locale, $language, $countryCode),
        ];

        // Add resolved values to customer data (only if resolved)
        if ($resolved['language']) {
            $customer['language'] = $resolved['language'];
        }

        if ($resolved['locale']) {
            $customer['locale'] = $resolved['locale'];
        }

        return $customer;
    }
}
