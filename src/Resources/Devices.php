<?php

namespace alexbabintsev\Magicline\Resources;

class Devices extends BaseResource
{
    public function list(): array
    {
        return $this->client->get('/v1/devices');
    }

    public function activate(string $deviceId): array
    {
        return $this->client->post("/v1/devices/{$deviceId}/activate");
    }
}
