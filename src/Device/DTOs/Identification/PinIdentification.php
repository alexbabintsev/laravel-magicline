<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Identification;

class PinIdentification extends BaseIdentification
{
    protected function __construct(array $data)
    {
        $this->type = 'PIN';
        $this->value = $data['value'];
    }

    /**
     * Create PIN identification
     */
    public static function create(string $pin): self
    {
        return self::from(['value' => $pin]);
    }

    /**
     * Create PIN identification from integer
     */
    public static function fromInt(int $pin): self
    {
        return self::from(['value' => (string) $pin]);
    }

    /**
     * Get PIN as integer
     */
    public function getAsInt(): int
    {
        return (int) $this->value;
    }

    /**
     * Get PIN length
     */
    public function getLength(): int
    {
        return strlen($this->value);
    }

    /**
     * Check if PIN is numeric
     */
    public function isNumeric(): bool
    {
        return ctype_digit($this->value);
    }

    /**
     * Validate PIN format (typically 4-6 digits)
     */
    public function isValidFormat(): bool
    {
        return $this->isNumeric() && $this->getLength() >= 4 && $this->getLength() <= 6;
    }
}