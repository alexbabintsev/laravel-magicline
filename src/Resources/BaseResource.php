<?php

namespace alexbabintsev\Magicline\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;

abstract class BaseResource
{
    public function __construct(
        protected MagiclineClient $client
    ) {}

    protected function buildQuery(array $params = []): array
    {
        return array_filter($params, fn ($value) => $value !== null);
    }

    protected function validatePagination(?int $offset = null, ?int $sliceSize = null): array
    {
        $query = [];

        if ($offset !== null) {
            $query['offset'] = (string) $offset;
        }

        if ($sliceSize !== null) {
            if ($sliceSize < 10 || $sliceSize > 100) {
                throw new \InvalidArgumentException('Slice size must be between 10 and 100');
            }
            $query['sliceSize'] = $sliceSize;
        }

        return $query;
    }
}
