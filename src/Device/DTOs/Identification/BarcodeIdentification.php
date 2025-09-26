<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Identification;

class BarcodeIdentification extends BaseIdentification
{
    protected function __construct(array $data)
    {
        $this->type = 'BARCODE';
        $this->value = $data['value'];
    }

    /**
     * Create barcode identification
     */
    public static function create(string $barcodeValue): self
    {
        return self::from(['value' => $barcodeValue]);
    }

    /**
     * Get barcode length
     */
    public function getLength(): int
    {
        return strlen($this->value);
    }

    /**
     * Check if barcode is numeric
     */
    public function isNumeric(): bool
    {
        return ctype_digit($this->value);
    }

    /**
     * Check if barcode is alphanumeric
     */
    public function isAlphanumeric(): bool
    {
        return ctype_alnum($this->value);
    }
}
