<?php

namespace AlexBabintsev\Magicline\Resources;

class Studios extends BaseResource
{
    public function getUtilization(): array
    {
        return $this->client->get('/v1/studios/utilization');
    }
}
