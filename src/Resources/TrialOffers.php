<?php

namespace alexbabintsev\Magicline\Resources;

class TrialOffers extends BaseResource
{
    public function getBookableClasses(?int $offset = null, ?int $sliceSize = null): array
    {
        $query = $this->validatePagination($offset, $sliceSize);

        return $this->client->get('/v1/trial-offers/bookable-trial-offers/classes', $query);
    }

    public function getBookableAppointments(?int $offset = null, ?int $sliceSize = null): array
    {
        $query = $this->validatePagination($offset, $sliceSize);

        return $this->client->get('/v1/trial-offers/bookable-trial-offers/appointments/bookable', $query);
    }
}
