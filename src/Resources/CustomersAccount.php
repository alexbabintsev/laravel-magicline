<?php

namespace alexbabintsev\Magicline\Resources;

class CustomersAccount extends BaseResource
{
    public function getBalances(int $customerId): array
    {
        return $this->client->get("/v1/customers/{$customerId}/account/balances");
    }
}
