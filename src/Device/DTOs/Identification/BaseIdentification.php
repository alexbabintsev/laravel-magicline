<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Identification;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

abstract class BaseIdentification extends BaseDto
{
    public string $type;
    public string $value;

    /**
     * Get the identification type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the identification value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Create appropriate identification instance based on type
     */
    public static function createFromType(string $type, array $data): self
    {
        return match ($type) {
            'NUMBER' => CardNumberIdentification::from($data),
            'QR_CODE' => QrCodeIdentification::from($data),
            'BARCODE' => BarcodeIdentification::from($data),
            'PIN' => PinIdentification::from($data),
            'WALLET_PASS' => WalletPassIdentification::from($data),
            default => throw new \InvalidArgumentException("Unknown identification type: {$type}")
        };
    }

    /**
     * Convert to array for API requests
     */
    public function toApiArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}