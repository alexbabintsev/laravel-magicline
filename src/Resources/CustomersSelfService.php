<?php

namespace AlexBabintsev\Magicline\Resources;

class CustomersSelfService extends BaseResource
{
    public function getContactData(int $customerId): array
    {
        return $this->client->get("/v1/customers/{$customerId}/self-service/contact-data");
    }

    public function createContactDataAmendment(int $customerId, array $data): array
    {
        return $this->client->post("/v1/customers/{$customerId}/self-service/contact-data", $data);
    }
}
