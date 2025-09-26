<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Identification;

class CardNumberIdentification extends BaseIdentification
{
    public string $format;

    public const FORMAT_DECIMAL = 'DECIMAL';

    public const FORMAT_HEX_MSB = 'HEX_MSB';

    public const FORMAT_HEX_LSB = 'HEX_LSB';

    protected function __construct(array $data)
    {
        $this->type = 'NUMBER';
        $this->value = $data['value'];
        $this->format = $data['format'] ?? self::FORMAT_DECIMAL;
    }

    /**
     * Create decimal card number identification
     */
    public static function decimal(string $cardNumber): self
    {
        return self::from([
            'value' => $cardNumber,
            'format' => self::FORMAT_DECIMAL,
        ]);
    }

    /**
     * Create hex MSB card number identification
     */
    public static function hexMsb(string $cardNumber): self
    {
        return self::from([
            'value' => $cardNumber,
            'format' => self::FORMAT_HEX_MSB,
        ]);
    }

    /**
     * Create hex LSB card number identification
     */
    public static function hexLsb(string $cardNumber): self
    {
        return self::from([
            'value' => $cardNumber,
            'format' => self::FORMAT_HEX_LSB,
        ]);
    }

    /**
     * Get the card format
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Check if format is decimal
     */
    public function isDecimal(): bool
    {
        return $this->format === self::FORMAT_DECIMAL;
    }

    /**
     * Check if format is hex
     */
    public function isHex(): bool
    {
        return in_array($this->format, [self::FORMAT_HEX_MSB, self::FORMAT_HEX_LSB]);
    }

    /**
     * Validate if card number is valid decimal format
     */
    public function isValidDecimal(): bool
    {
        return ctype_digit($this->value);
    }

    /**
     * Validate if card number is valid hex format
     */
    public function isValidHex(): bool
    {
        return ctype_xdigit($this->value);
    }

    /**
     * Convert to array for API requests
     */
    public function toApiArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'format' => $this->format,
        ];
    }
}
