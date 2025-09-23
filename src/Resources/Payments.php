<?php

namespace alexbabintsev\Magicline\Resources;

class Payments extends BaseResource
{
    public function createUserSession(array $data): array
    {
        return $this->client->post('/v1/payments/user-session', $data);
    }
}
