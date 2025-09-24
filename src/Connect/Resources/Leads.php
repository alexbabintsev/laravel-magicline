<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class Leads extends BaseConnectResource
{
    /**
     * Create a new lead
     *
     * @param array $data Lead data including customer information
     */
    public function create(array $data): array
    {
        $this->validateRequired($data, ['customer']);

        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v1/lead', $data);
    }

    /**
     * Get lead customer data by identity token (UUID)
     *
     * @param string $uuid Identity token UUID
     */
    public function getByUuid(string $uuid): array
    {
        if (empty($uuid)) {
            throw new \InvalidArgumentException('UUID is required');
        }

        return $this->client->get("/v1/lead/customer/{$uuid}");
    }
}