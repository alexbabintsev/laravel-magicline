<?php

namespace AlexBabintsev\Magicline\Resources;

class Finance extends BaseResource
{
    public function getDebtCollectionConfiguration(): array
    {
        return $this->client->get('/v1/debt-collection/configuration');
    }
}
