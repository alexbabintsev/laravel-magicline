<?php

namespace alexbabintsev\Magicline\Resources;

class CheckinVouchers extends BaseResource
{
    public function redeem(array $data): array
    {
        return $this->client->post('/v1/checkin-vouchers/redeem', $data);
    }
}
