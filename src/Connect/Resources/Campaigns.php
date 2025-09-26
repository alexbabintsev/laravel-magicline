<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class Campaigns extends BaseConnectResource
{
    /**
     * Get list of marketing campaigns
     * Used for lead generation to track source campaigns
     */
    public function list(array $params = []): array
    {
        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/campaign', $query);
    }

    /**
     * Get campaigns for specific studio
     *
     * @param  int  $studioId  Studio ID
     * @param  array  $params  Additional query parameters
     */
    public function getForStudio(int $studioId, array $params = []): array
    {
        $this->validateStudioId($studioId);

        $params['studioId'] = $studioId;
        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/campaign', $query);
    }
}
