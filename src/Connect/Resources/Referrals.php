<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class Referrals extends BaseConnectResource
{
    /**
     * Get referral information
     * Used for trial session booking to track referral sources
     */
    public function list(array $params = []): array
    {
        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/referral', $query);
    }

    /**
     * Get referrals for specific studio
     *
     * @param  int  $studioId  Studio ID
     * @param  array  $params  Additional query parameters
     */
    public function getForStudio(int $studioId, array $params = []): array
    {
        $this->validateStudioId($studioId);

        $params['studioId'] = $studioId;
        $query = $this->buildQueryParams($params);

        return $this->client->get('/v1/referral', $query);
    }
}
