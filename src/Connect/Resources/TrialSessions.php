<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class TrialSessions extends BaseConnectResource
{
    /**
     * Get available trial session slots
     *
     * @param array $params Query parameters (startDate, endDate, studioId)
     */
    public function getAvailableSlots(array $params): array
    {
        $this->validateRequired($params, ['studioId', 'startDate', 'endDate']);

        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/trialsession', $query);
    }

    /**
     * Book a trial session
     *
     * @param array $data Booking data including leadCustomer and slot information
     */
    public function book(array $data): array
    {
        $this->validateRequired($data, ['studioId', 'startDateTime', 'leadCustomer']);

        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v1/trialsession/book', $data);
    }

    /**
     * Get trial session validation configuration
     *
     * @param int $studioId Studio ID
     */
    public function getValidationConfig(int $studioId): array
    {
        $this->validateStudioId($studioId);

        return $this->client->get('/v1/trialsession/config/validation', ['studioId' => $studioId]);
    }
}