<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class Validation extends BaseConnectResource
{
    /**
     * Validate tax ID
     * Supports different document types and country-specific validation
     *
     * @param  array  $params  Validation parameters
     */
    public function validateTaxId(array $params): array
    {
        $this->validateRequired($params, ['taxId', 'countryCode']);

        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/validation/taxId', $query);
    }

    /**
     * Get trial session validation configuration
     * Returns mandatory field configuration for forms
     *
     * @param  int  $studioId  Studio ID
     */
    public function getTrialSessionConfig(int $studioId): array
    {
        $this->validateStudioId($studioId);

        return $this->client->get('/v1/trialsession/config/validation', ['studioId' => $studioId]);
    }

    /**
     * Validate document identification
     * Client-side validation helper for different document types
     *
     * @param  string  $documentType  Document type (ID_CARD, PASSPORT, etc.)
     * @param  string  $documentNumber  Document number
     * @param  string|null  $countryCode  Country code for specific validation
     */
    public function validateDocumentIdentification(
        string $documentType,
        string $documentNumber,
        ?string $countryCode = null
    ): array {
        if (empty($documentType) || empty($documentNumber)) {
            return [
                'valid' => false,
                'errors' => ['Both documentType and documentNumber are required'],
            ];
        }

        $validTypes = ['ID_CARD', 'PASSPORT', 'DRIVERS_LICENSE', 'RESIDENCE_PERMIT', 'NATIONAL_ID_NUMBER'];
        if (! in_array($documentType, $validTypes)) {
            return [
                'valid' => false,
                'errors' => ['Invalid document type'],
            ];
        }

        // Special validation for Turkish TC Kimlik
        if ($documentType === 'NATIONAL_ID_NUMBER' && $countryCode === 'TR') {
            return $this->validateTurkishTCKimlik($documentNumber);
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Validate Turkish TC Kimlik number
     *
     * @param  string  $tcKimlik  TC Kimlik number
     */
    private function validateTurkishTCKimlik(string $tcKimlik): array
    {
        // Remove any non-numeric characters
        $tcKimlik = preg_replace('/\D/', '', $tcKimlik);

        if (strlen($tcKimlik) !== 11) {
            return [
                'valid' => false,
                'errors' => ['Turkish TC Kimlik must be 11 digits'],
            ];
        }

        if ($tcKimlik[0] === '0') {
            return [
                'valid' => false,
                'errors' => ['Turkish TC Kimlik cannot start with 0'],
            ];
        }

        // Calculate checksum
        $digits = str_split($tcKimlik);
        $sum1 = 0;
        $sum2 = 0;

        for ($i = 0; $i < 9; $i++) {
            if ($i % 2 === 0) {
                $sum1 += (int) $digits[$i];
            } else {
                $sum2 += (int) $digits[$i];
            }
        }

        $checksum1 = (($sum1 * 7) - $sum2) % 10;
        $checksum2 = ($sum1 + $sum2 + (int) $digits[9]) % 10;

        if ($checksum1 !== (int) $digits[9] || $checksum2 !== (int) $digits[10]) {
            return [
                'valid' => false,
                'errors' => ['Invalid Turkish TC Kimlik checksum'],
            ];
        }

        return ['valid' => true, 'errors' => []];
    }
}
