<?php

namespace AlexBabintsev\Magicline\Device\Resources;

use AlexBabintsev\Magicline\Device\DTOs\Time\TimeIdentificationRequest;
use AlexBabintsev\Magicline\Device\DTOs\Time\TimeIdentificationResponse;
use AlexBabintsev\Magicline\Device\Http\MagiclineDeviceClient;

class TimeResource
{
    public function __construct(private readonly MagiclineDeviceClient $client)
    {
    }

    public function identification(TimeIdentificationRequest $request): TimeIdentificationResponse
    {
        $response = $this->client->post('time/identification', $request->toApiPayload());

        return TimeIdentificationResponse::from($response);
    }
}