<?php

namespace AlexBabintsev\Magicline\Device\DTOs\Time;

use AlexBabintsev\Magicline\DataTransferObjects\BaseDto;
use AlexBabintsev\Magicline\Device\DTOs\Identification\BaseIdentification;

class TimeIdentificationRequest extends BaseDto
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

    public static function create(BaseIdentification $identification, bool $shouldExecuteAction = true): self
    {
        return self::from([
            'identification' => $identification->toArray(),
            'shouldExecuteAction' => $shouldExecuteAction
        ]);
    }

    public static function dryRun(BaseIdentification $identification): self
    {
        return self::create($identification, false);
    }

    public function isDryRun(): bool
    {
        return !$this->shouldExecuteAction;
    }

    public function getIdentification(): BaseIdentification
    {
        return $this->identification;
    }

    public function toApiPayload(): array
    {
        return [
            'identification' => $this->identification->toApiArray(),
            'shouldExecuteAction' => $this->shouldExecuteAction
        ];
    }
}