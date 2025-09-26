<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class RateBundles extends BaseConnectResource
{
    /**
     * Get rate bundles for a studio
     *
     * @param  int  $studioId  Studio ID
     * @param  array  $params  Additional query parameters
     */
    public function getForStudio(int $studioId, array $params = []): array
    {
        $this->validateStudioId($studioId);

        $params['studioId'] = $studioId;
        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/rate-bundle', $query);
    }

    /**
     * Get list of rate bundles
     *
     * @param  array  $params  Query parameters
     */
    public function list(array $params = []): array
    {
        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/rate-bundle', $query);
    }
}
