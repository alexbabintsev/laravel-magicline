<?php

namespace alexbabintsev\Magicline\Resources;

class Memberships extends BaseResource
{
    public function getOffers(): array
    {
        return $this->client->get('/v1/memberships/membership-offers');
    }
}
