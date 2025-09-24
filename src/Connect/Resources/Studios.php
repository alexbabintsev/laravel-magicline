<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class Studios extends BaseConnectResource
{
    /**
     * Get list of studios (v2 endpoint)
     * Used for lead generation and studio selection
     */
    public function list(array $params = []): array
    {
        $query = $this->buildQueryParams($params);

        return $this->client->get('/v2/studio', $query);
    }

    /**
     * Get studio communication settings
     * Required for detailed communication preferences
     *
     * @param int $studioId Studio ID
     */
    public function getCommunicationSettings(int $studioId): array
    {
        $this->validateStudioId($studioId);

        return $this->client->get("/v1/studio/{$studioId}/communication-settings");
    }

    /**
     * Get studios for contract cancellation
     * Returns studios available for cancellation in specific countries
     */
    public function getForCancellation(): array
    {
        return $this->client->get('/v1/contracts/studios');
    }

    /**
     * Get studio cancellation information
     * Returns cancellation reasons and other studio-specific info
     *
     * @param int $studioId Studio ID
     */
    public function getCancellationInfo(int $studioId): array
    {
        $this->validateStudioId($studioId);

        return $this->client->get("/v1/contracts/studios/{$studioId}");
    }

    /**
     * Get studio cancellation reasons (deprecated endpoint)
     * Use getCancellationInfo() instead for new implementations
     *
     * @param int $studioId Studio ID
     */
    public function getCancellationReasons(int $studioId): array
    {
        $this->validateStudioId($studioId);

        return $this->client->get("/v1/contracts/studios/{$studioId}/cancellation-reasons");
    }
}