<?php

namespace AlexBabintsev\Magicline\Connect\Resources;

use AlexBabintsev\Magicline\Connect\Http\MagiclineConnectClient;

abstract class BaseConnectResource
{
    public function __construct(
        protected readonly MagiclineConnectClient $client
    ) {}

    /**
     * Build query parameters from array, filtering out null values
     */
    protected function buildQueryParams(array $params): array
    {
        return array_filter($params, fn($value) => $value !== null);
    }

    /**
     * Validate required parameters
     */
    protected function validateRequired(array $data, array $required): void
    {
        $missing = [];

        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new \InvalidArgumentException(
                'Missing required fields: ' . implode(', ', $missing)
            );
        }
    }

    /**
     * Filter out null/empty values from array recursively
     */
    protected function filterEmptyValues(array $data): array
    {
        return array_filter($data, function ($value) {
            if (is_array($value)) {
                $filtered = $this->filterEmptyValues($value);
                return !empty($filtered);
            }
            return $value !== null && $value !== '';
        });
    }

    /**
     * Validate studio ID (common requirement)
     */
    protected function validateStudioId(int $studioId): void
    {
        if ($studioId <= 0) {
            throw new \InvalidArgumentException('Studio ID must be a positive integer');
        }
    }

    /**
     * Format date for API requests
     */
    protected function formatDate(string|\DateTime $date): string
    {
        if (is_string($date)) {
            return $date;
        }

        return $date->format('Y-m-d');
    }
}