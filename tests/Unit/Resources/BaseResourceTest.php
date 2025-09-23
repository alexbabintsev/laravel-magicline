<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\BaseResource;
use alexbabintsev\Magicline\Tests\TestCase;
use Illuminate\Http\Client\Factory;

class BaseResourceTest extends TestCase
{
    protected BaseResource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $client = new MagiclineClient(
            httpFactory: app(Factory::class),
            apiKey: 'test-api-key',
            baseUrl: 'https://test.magicline.com',
            timeout: 30,
            retryConfig: ['times' => 1, 'sleep' => 100],
            loggingEnabled: false
        );

        $this->resource = new class($client) extends BaseResource
        {
            public function test_build_query(array $params = []): array
            {
                return $this->buildQuery($params);
            }

            public function test_validate_pagination(?int $offset = null, ?int $sliceSize = null): array
            {
                return $this->validatePagination($offset, $sliceSize);
            }
        };
    }

    public function test_build_query_filters_null_values()
    {
        $params = ['key1' => 'value1', 'key2' => null, 'key3' => 'value3'];

        $result = $this->resource->test_build_query($params);

        expect($result)->toBe(['key1' => 'value1', 'key3' => 'value3']);
        expect($result)->not->toHaveKey('key2');
    }

    public function test_validate_pagination_with_valid_values()
    {
        $result = $this->resource->test_validate_pagination(10, 50);

        expect($result)->toBe(['offset' => '10', 'sliceSize' => 50]);
    }

    public function test_validate_pagination_with_null_values()
    {
        $result = $this->resource->test_validate_pagination();

        expect($result)->toBe([]);
    }

    public function test_validate_pagination_throws_exception_for_small_slice_size()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Slice size must be between 10 and 100');

        $this->resource->test_validate_pagination(0, 5);
    }

    public function test_validate_pagination_throws_exception_for_large_slice_size()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Slice size must be between 10 and 100');

        $this->resource->test_validate_pagination(0, 150);
    }

    public function test_validate_pagination_with_only_offset()
    {
        $result = $this->resource->test_validate_pagination(25, null);

        expect($result)->toBe(['offset' => '25']);
    }

    public function test_validate_pagination_with_only_slice_size()
    {
        $result = $this->resource->test_validate_pagination(null, 75);

        expect($result)->toBe(['sliceSize' => 75]);
    }
}
