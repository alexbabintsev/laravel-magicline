<?php

namespace alexbabintsev\Magicline\Resources;

class MembershipsSelfService extends BaseResource
{
    public function getContractData(int $customerId): array
    {
        return $this->client->get("/v1/memberships/{$customerId}/self-service/contract-data");
    }

    public function cancelOrdinaryContract(int $customerId, array $data): array
    {
        return $this->client->post("/v1/memberships/{$customerId}/self-service/ordinary-contract-cancelation", $data);
    }
}
