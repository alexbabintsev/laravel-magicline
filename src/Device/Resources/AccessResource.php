<?php

namespace AlexBabintsev\Magicline\Device\Resources;

use AlexBabintsev\Magicline\Device\DTOs\Access\CardReaderIdentificationRequest;
use AlexBabintsev\Magicline\Device\DTOs\Access\CardReaderIdentificationResponse;
use AlexBabintsev\Magicline\Device\Http\MagiclineDeviceClient;

class AccessResource
{
    public function __construct(private readonly MagiclineDeviceClient $client)
    {
    }

    public function cardReaderIdentification(CardReaderIdentificationRequest $request): CardReaderIdentificationResponse
    {
        $response = $this->client->post('access/card-reader/identification', $request->toApiPayload());

        return CardReaderIdentificationResponse::from($response);
    }
}