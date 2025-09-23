<?php

namespace AlexBabintsev\Magicline\Resources;

class Appointments extends BaseResource
{
    public function getBookable(?int $offset = null, ?int $sliceSize = null): array
    {
        $query = $this->validatePagination($offset, $sliceSize);

        return $this->client->get('/v1/appointments/bookable', $query);
    }

    public function book(array $data): array
    {
        return $this->client->post('/v1/appointments', $data);
    }

    public function cancel(int $appointmentId): array
    {
        return $this->client->delete("/v1/appointments/{$appointmentId}");
    }
}
