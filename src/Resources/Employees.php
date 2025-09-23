<?php

namespace AlexBabintsev\Magicline\Resources;

class Employees extends BaseResource
{
    public function list(?int $offset = null, ?int $sliceSize = null): array
    {
        $query = $this->validatePagination($offset, $sliceSize);

        return $this->client->get('/v1/employees', $query);
    }
}
