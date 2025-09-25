<?php

namespace AlexBabintsev\Magicline\Device\DTOs;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

class Money extends BaseDto
{
    public float $amount;
    public string $currency;

    /**
     * Create Money instance with validation
     */
    public static function create(float $amount, string $currency): self
    {
        return self::from([
            'amount' => $amount,
            'currency' => strtoupper($currency)
        ]);
    }

    /**
     * Format money as string
     */
    public function format(string $currency = null): string
    {
        $displayCurrency = $currency ?? $this->currency;
        return number_format($this->amount, 2) . ' ' . $displayCurrency;
    }

    /**
     * Check if currency is Euro
     */
    public function isEuro(): bool
    {
        return $this->currency === 'EUR';
    }

    /**
     * Convert to cents (for integer operations)
     */
    public function toCents(): int
    {
        return (int) round($this->amount * 100);
    }

    /**
     * Create from cents
     */
    public static function fromCents(int $cents, string $currency): self
    {
        return self::create($cents / 100, $currency);
    }

    /**
     * Check if amount is positive
     */
    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    /**
     * Check if amount is negative
     */
    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    /**
     * Check if amount is zero
     */
    public function isZero(): bool
    {
        return abs($this->amount) < 0.001; // Handle floating point precision
    }
}