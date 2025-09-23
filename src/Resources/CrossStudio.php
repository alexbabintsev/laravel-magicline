<?php

namespace alexbabintsev\Magicline\Resources;

class CrossStudio extends BaseResource
{
    public function getCustomersBy(array $criteria): array
    {
        return $this->client->get('/v1/cross-studio/customers/by', $criteria);
    }
}
