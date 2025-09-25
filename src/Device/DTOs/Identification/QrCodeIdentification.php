<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Identification;

class QrCodeIdentification extends BaseIdentification
{
    protected function __construct(array $data)
    {
        $this->type = 'QR_CODE';
        $this->value = $data['value'];
    }

    /**
     * Create QR code identification from value
     */
    public static function create(string $value): self
    {
        return self::from(['value' => $value]);
    }

    /**
     * Create QR code identification from JSON string
     */
    public static function fromJson(string $jsonData): self
    {
        return self::from(['value' => $jsonData]);
    }

    /**
     * Create QR code identification from array
     */
    public static function fromArray(array $data): self
    {
        return self::from(['value' => json_encode($data)]);
    }

    /**
     * Create QR code identification from simple string
     */
    public static function fromString(string $simpleString): self
    {
        return self::from(['value' => $simpleString]);
    }

    /**
     * Check if QR code contains JSON data
     */
    public function isJson(): bool
    {
        json_decode($this->value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Get QR code data as array (if it's JSON)
     */
    public function getDataArray(): ?array
    {
        if (!$this->isJson()) {
            return null;
        }

        return json_decode($this->value, true);
    }

    /**
     * Get customer UUID from QR code (if available)
     */
    public function getCustomerUuid(): ?string
    {
        $data = $this->getDataArray();
        return $data['uuid'] ?? null;
    }

    /**
     * Get customer number from QR code (if available)
     */
    public function getCustomerNumber(): ?string
    {
        $data = $this->getDataArray();
        return $data['customer_number'] ?? null;
    }

    /**
     * Get tenant from QR code (if available)
     */
    public function getTenant(): ?string
    {
        $data = $this->getDataArray();
        return $data['tenant'] ?? null;
    }
}