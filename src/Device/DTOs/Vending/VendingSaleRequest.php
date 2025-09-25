<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Vending;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;
use AlexBabintsev\Magicline\Device\DTOs\Identification\BaseIdentification;

class VendingSaleRequest extends BaseDto
{
    public BaseIdentification $identification;
    public string $transactionId;
    public string $productId;
    public float $price;
    public bool $shouldExecuteAction;

    protected function __construct(array $data)
    {
        $identificationData = $data['identification'];
        $this->identification = BaseIdentification::createFromType(
            $identificationData['type'],
            $identificationData
        );
        $this->transactionId = $data['transactionId'];
        $this->productId = $data['productId'];
        $this->price = $data['price'];
        $this->shouldExecuteAction = $data['shouldExecuteAction'] ?? true;
    }

    /**
     * Create vending sale request
     */
    public static function create(
        BaseIdentification $identification,
        string $transactionId,
        string $productId,
        float $price,
        bool $shouldExecuteAction = true
    ): self {
        return self::from([
            'identification' => $identification->toArray(),
            'transactionId' => $transactionId,
            'productId' => $productId,
            'price' => $price,
            'shouldExecuteAction' => $shouldExecuteAction
        ]);
    }

    /**
     * Create dry run sale request (validation only)
     */
    public static function dryRun(
        BaseIdentification $identification,
        string $transactionId,
        string $productId,
        float $price
    ): self {
        return self::create($identification, $transactionId, $productId, $price, false);
    }

    /**
     * Check if this is a dry run
     */
    public function isDryRun(): bool
    {
        return !$this->shouldExecuteAction;
    }

    /**
     * Get the identification method
     */
    public function getIdentification(): BaseIdentification
    {
        return $this->identification;
    }

    /**
     * Get transaction ID
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Get product ID (shelf location)
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * Get product price
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice(string $currency = 'EUR'): string
    {
        return number_format($this->price, 2) . ' ' . $currency;
    }

    /**
     * Convert to API payload
     */
    public function toApiPayload(): array
    {
        return [
            'identification' => $this->identification->toApiArray(),
            'transactionId' => $this->transactionId,
            'productId' => $this->productId,
            'price' => $this->price,
            'shouldExecuteAction' => $this->shouldExecuteAction
        ];
    }
}