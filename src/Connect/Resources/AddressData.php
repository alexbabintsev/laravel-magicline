<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class AddressData extends BaseConnectResource
{
    /**
     * Get province information for Italy by zip code
     * Used for address validation in Italian addresses
     *
     * @param string $zipCode Italian zip code
     */
    public function getItalianProvince(string $zipCode): array
    {
        if (empty($zipCode)) {
            throw new \InvalidArgumentException('Zip code is required');
        }

        return $this->client->get('/v1/addressdata/italy/province', ['zipCode' => $zipCode]);
    }

    /**
     * Get list of countries
     * Returns internationalization data for countries
     */
    public function getCountries(): array
    {
        return $this->client->get('/v1/i18n/countries');
    }

    /**
     * Get countries with locale support
     *
     * @param string|null $locale Locale for country names (e.g., 'de_DE', 'en_US')
     */
    public function getCountriesWithLocale(?string $locale = null): array
    {
        $params = [];
        if ($locale) {
            $params['locale'] = $locale;
        }

        return $this->client->get('/v1/i18n/countries', $params);
    }

    /**
     * Validate address data
     * Client-side validation helper for address completeness
     *
     * @param array $address Address data to validate
     * @param array $requiredFields List of required fields
     */
    public function validateAddress(array $address, array $requiredFields = ['country', 'city', 'zip']): array
    {
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($address[$field])) {
                $errors[] = "Field '{$field}' is required";
            }
        }

        // Validate specific formats
        if (isset($address['zip']) && !empty($address['zip'])) {
            if (!$this->validateZipCode($address['zip'], $address['country'] ?? '')) {
                $errors[] = 'Invalid zip code format';
            }
        }

        if (isset($address['country']) && !empty($address['country'])) {
            if (!$this->validateCountryCode($address['country'])) {
                $errors[] = 'Invalid country code';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate zip code format by country
     *
     * @param string $zipCode Zip code to validate
     * @param string $countryCode Country code
     */
    private function validateZipCode(string $zipCode, string $countryCode): bool
    {
        $patterns = [
            'DE' => '/^\d{5}$/',           // Germany: 12345
            'AT' => '/^\d{4}$/',           // Austria: 1234
            'CH' => '/^\d{4}$/',           // Switzerland: 1234
            'US' => '/^\d{5}(-\d{4})?$/',  // USA: 12345 or 12345-6789
            'GB' => '/^[A-Z]{1,2}\d[A-Z\d]?\s?\d[A-Z]{2}$/i', // UK: SW1A 1AA
            'FR' => '/^\d{5}$/',           // France: 12345
            'IT' => '/^\d{5}$/',           // Italy: 12345
            'ES' => '/^\d{5}$/',           // Spain: 12345
            'NL' => '/^\d{4}\s?[A-Z]{2}$/i', // Netherlands: 1234 AB
        ];

        if (!isset($patterns[$countryCode])) {
            return true; // No validation pattern, assume valid
        }

        return preg_match($patterns[$countryCode], $zipCode) === 1;
    }

    /**
     * Validate country code format
     *
     * @param string $countryCode Country code to validate
     */
    private function validateCountryCode(string $countryCode): bool
    {
        return preg_match('/^[A-Z]{2}$/', $countryCode) === 1;
    }
}