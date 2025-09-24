<?php

use AlexBabintsev\Magicline\Connect\Support\LocaleResolver;

test('resolves language from locale', function () {
    $language = LocaleResolver::resolveLanguage('de_CH', null, null);

    expect($language)->toBe('de');
});

test('resolves language from language parameter', function () {
    $language = LocaleResolver::resolveLanguage(null, 'fr', null);

    expect($language)->toBe('fr');
});

test('resolves language from country code fallback', function () {
    $language = LocaleResolver::resolveLanguage(null, null, 'CH');

    expect($language)->toBe('de');
});

test('locale has priority over language', function () {
    $language = LocaleResolver::resolveLanguage('fr_CH', 'de', 'CH');

    expect($language)->toBe('fr');
});

test('resolves locale from locale parameter', function () {
    $locale = LocaleResolver::resolveLocale('de_CH', null, null);

    expect($locale)->toBe('de_CH');
});

test('combines language and country to locale', function () {
    $locale = LocaleResolver::resolveLocale(null, 'de', 'CH');

    expect($locale)->toBe('de_CH');
});

test('validates locale format', function () {
    expect(LocaleResolver::isValidLocale('de_CH'))->toBeTrue();
    expect(LocaleResolver::isValidLocale('en_US'))->toBeTrue();
    expect(LocaleResolver::isValidLocale('invalid'))->toBeFalse();
    expect(LocaleResolver::isValidLocale('de_ch'))->toBeFalse(); // lowercase country
});

test('validates language format', function () {
    expect(LocaleResolver::isValidLanguage('de'))->toBeTrue();
    expect(LocaleResolver::isValidLanguage('en'))->toBeTrue();
    expect(LocaleResolver::isValidLanguage('invalid'))->toBeFalse();
    expect(LocaleResolver::isValidLanguage('DE'))->toBeFalse(); // uppercase
});

test('extracts language from locale', function () {
    $language = LocaleResolver::extractLanguageFromLocale('de_CH');

    expect($language)->toBe('de');
});

test('extracts country from locale', function () {
    $country = LocaleResolver::extractCountryFromLocale('de_CH');

    expect($country)->toBe('CH');
});

test('prepares customer data with resolved values', function () {
    $customer = [
        'name' => 'John Doe',
        'locale' => 'fr_CH',
        'language' => 'de', // Should be overridden by locale
        'countryCode' => 'DE', // Should not be used
    ];

    $prepared = LocaleResolver::prepareCustomerData($customer);

    expect($prepared['language'])->toBe('fr');
    expect($prepared['locale'])->toBe('fr_CH');
    expect($prepared['name'])->toBe('John Doe'); // Other fields preserved
});