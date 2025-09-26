<?php

namespace AlexBabintsev\Magicline\Device;

use AlexBabintsev\Magicline\Device\Http\MagiclineDeviceClient;
use AlexBabintsev\Magicline\Device\Resources\AccessResource;
use AlexBabintsev\Magicline\Device\Resources\TimeResource;
use AlexBabintsev\Magicline\Device\Resources\VendingResource;
use Illuminate\Http\Client\Factory;
use Psr\Log\LoggerInterface;

class MagiclineDevice
{
    private readonly MagiclineDeviceClient $client;

    private ?AccessResource $access = null;

    private ?VendingResource $vending = null;

    private ?TimeResource $time = null;

    public function __construct(
        Factory $httpFactory,
        string $baseUrl,
        string $bearerToken,
        ?LoggerInterface $logger = null,
        int $timeout = 30,
        int $retryTimes = 3,
        int $retryDelay = 1000,
        bool $enableLogging = true
    ) {
        $this->client = new MagiclineDeviceClient(
            $httpFactory,
            $baseUrl,
            $bearerToken,
            $timeout,
            $retryTimes,
            $retryDelay,
            $logger,
            $enableLogging
        );
    }

    public function access(): AccessResource
    {
        return $this->access ??= new AccessResource($this->client);
    }

    public function vending(): VendingResource
    {
        return $this->vending ??= new VendingResource($this->client);
    }

    public function time(): TimeResource
    {
        return $this->time ??= new TimeResource($this->client);
    }
}
