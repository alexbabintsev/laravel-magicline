<?php

namespace AlexBabintsev\Magicline\Device\Resources;

use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingIdentificationRequest;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingIdentificationResponse;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingSaleRequest;
use AlexBabintsev\Magicline\Device\DTOs\Vending\VendingSaleResponse;
use AlexBabintsev\Magicline\Device\Http\MagiclineDeviceClient;

class VendingResource
{
    public function __construct(private readonly MagiclineDeviceClient $client)
    {
    }

    public function identification(VendingIdentificationRequest $request): VendingIdentificationResponse
    {
        $response = $this->client->post('vending/identification', $request->toApiPayload());

        return VendingIdentificationResponse::from($response);
    }

    public function sale(VendingSaleRequest $request): VendingSaleResponse
    {
        $response = $this->client->post('vending/sale', $request->toApiPayload());

        return VendingSaleResponse::from($response);
    }
}