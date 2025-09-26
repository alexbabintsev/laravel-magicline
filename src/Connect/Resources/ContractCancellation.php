<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

class ContractCancellation extends BaseConnectResource
{
    /**
     * Submit manual cancellation request
     *
     * @param  array  $data  Cancellation request data
     */
    public function submitManualRequest(array $data): array
    {
        $data = $this->filterEmptyValues($data);

        return $this->client->post('/v1/contracts/cancel-request', $data);
    }
}
