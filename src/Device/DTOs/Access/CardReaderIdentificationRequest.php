<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Access;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;
use AlexBabintsev\Magicline\Device\DTOs\Identification\BaseIdentification;

class CardReaderIdentificationRequest extends BaseDto
{
    public BaseIdentification $identification;
    public bool $shouldExecuteAction;

    protected function __construct(array $data)
    {
        $identificationData = $data['identification'];
        $this->identification = BaseIdentification::createFromType(
            $identificationData['type'],
            $identificationData
        );
        $this->shouldExecuteAction = $data['shouldExecuteAction'] ?? true;
    }

    /**
     * Create card reader identification request
     */
    public static function create(BaseIdentification $identification, bool $shouldExecuteAction = true): self
    {
        return self::from([
            'identification' => $identification->toArray(),
            'shouldExecuteAction' => $shouldExecuteAction
        ]);
    }

    /**
     * Create dry run request (validation only)
     */
    public static function dryRun(BaseIdentification $identification): self
    {
        return self::create($identification, false);
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
     * Convert to API payload
     */
    public function toApiPayload(): array
    {
        return [
            'identification' => $this->identification->toApiArray(),
            'shouldExecuteAction' => $this->shouldExecuteAction
        ];
    }
}