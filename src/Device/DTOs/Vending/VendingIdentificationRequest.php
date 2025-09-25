<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Vending;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;
use AlexBabintsev\Magicline\Device\DTOs\Identification\BaseIdentification;

class VendingIdentificationRequest extends BaseDto
{
    public BaseIdentification $identification;
    public string $transactionId;

    protected function __construct(array $data)
    {
        $identificationData = $data['identification'];
        $this->identification = BaseIdentification::createFromType(
            $identificationData['type'],
            $identificationData
        );
        $this->transactionId = $data['transactionId'];
    }

    /**
     * Create vending identification request
     */
    public static function create(BaseIdentification $identification, string $transactionId): self
    {
        return self::from([
            'identification' => $identification->toArray(),
            'transactionId' => $transactionId
        ]);
    }

    /**
     * Create vending identification request with generated UUID
     */
    public static function createWithGeneratedId(BaseIdentification $identification): self
    {
        return self::create($identification, self::generateTransactionId());
    }

    /**
     * Generate unique transaction ID
     */
    public static function generateTransactionId(): string
    {
        return (string) \Illuminate\Support\Str::uuid();
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
     * Convert to API payload
     */
    public function toApiPayload(): array
    {
        return [
            'identification' => $this->identification->toApiArray(),
            'transactionId' => $this->transactionId
        ];
    }
}