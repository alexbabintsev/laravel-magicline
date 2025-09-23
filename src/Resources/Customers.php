<?php

namespace AlexBabintsev\Magicline\Resources;

class Customers extends BaseResource
{
    public function list(?int $offset = null, ?int $sliceSize = null): array
    {
        $query = $this->validatePagination($offset, $sliceSize);

        return $this->client->get('/v1/customers', $query);
    }

    public function find(int $customerId): array
    {
        return $this->client->get("/v1/customers/{$customerId}");
    }
}
