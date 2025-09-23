<?php

namespace AlexBabintsev\Magicline\Resources;

class CustomersCommunication extends BaseResource
{
    public function createThread(int $customerId, array $data): array
    {
        return $this->client->post("/v1/communications/{$customerId}/threads", $data);
    }

    public function addToThread(int $customerId, string $threadId, array $data): array
    {
        return $this->client->post("/v1/communications/{$customerId}/threads/{$threadId}", $data);
    }
}
