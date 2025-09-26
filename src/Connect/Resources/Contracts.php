<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class Contracts extends BaseConnectResource
{
    /**
     * Create contract preview
     *
     * @param  array  $data  Preview data
     */
    public function preview(array $data): array
    {
        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v1/preview', $data);
    }

    /**
     * Create a new contract and customer
     *
     * @param  array  $data  Contract and customer data
     */
    public function create(array $data): array
    {
        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v1/rate-bundle', $data);
    }

    /**
     * Get active contracts for member
     *
     * @param  array  $data  Member identification data
     */
    public function getActiveContracts(array $data): array
    {
        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v1/contracts', $data);
    }

    /**
     * Cancel a contract
     *
     * @param  array  $data  Cancellation data
     */
    public function cancel(array $data): array
    {
        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v1/contracts/cancel', $data);
    }
}
