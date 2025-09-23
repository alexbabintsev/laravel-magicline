<?php

namespace alexbabintsev\Magicline\Resources;

class Classes extends BaseResource
{
    public function list(?int $offset = null, ?int $sliceSize = null): array
    {
        $query = $this->validatePagination($offset, $sliceSize);

        return $this->client->get('/v1/classes', $query);
    }

    public function book(int $classId, array $data): array
    {
        return $this->client->post("/v1/classes/{$classId}/book", $data);
    }

    public function cancel(int $classId, int $bookingId): array
    {
        return $this->client->delete("/v1/classes/{$classId}/bookings/{$bookingId}");
    }
}
