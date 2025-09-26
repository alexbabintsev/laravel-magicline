<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Vending;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;

class VendingIdentificationResponse extends BaseDto
{
    public string $text;

    public bool $authorized;

    public float $consumptionCredit;

    public string $transactionId;

    /**
     * Check if customer is authorized
     */
    public function isAuthorized(): bool
    {
        return $this->authorized;
    }

    /**
     * Get reason text (usually for unauthorized cases)
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get customer's consumption credit
     */
    public function getConsumptionCredit(): float
    {
        return $this->consumptionCredit;
    }

    /**
     * Get transaction ID
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Check if customer has sufficient credit for amount
     */
    public function hasSufficientCredit(float $amount): bool
    {
        return $this->consumptionCredit >= $amount;
    }

    /**
     * Get formatted consumption credit
     */
    public function getFormattedCredit(string $currency = 'EUR'): string
    {
        return number_format($this->consumptionCredit, 2).' '.$currency;
    }
}
