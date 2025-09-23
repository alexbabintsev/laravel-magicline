<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\BaseResource;
use Illuminate\Http\Client\Factory;

beforeEach(function () {
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
});

test('build query filters null values', function () {
    $params = ['key1' => 'value1', 'key2' => null, 'key3' => 'value3'];

    $result = $this->resource->test_build_query($params);

    expect($result)->toBe(['key1' => 'value1', 'key3' => 'value3'])
        ->and($result)->not->toHaveKey('key2');
});

test('validate pagination with valid values', function () {
    $result = $this->resource->test_validate_pagination(10, 50);

    expect($result)->toBe(['offset' => '10', 'sliceSize' => 50]);
});

test('validate pagination with null values', function () {
    $result = $this->resource->test_validate_pagination();

    expect($result)->toBe([]);
});

test('validate pagination throws exception for small slice size', function () {
    expect(fn () => $this->resource->test_validate_pagination(0, 5))
        ->toThrow(\InvalidArgumentException::class, 'Slice size must be between 10 and 100');
});

test('validate pagination throws exception for large slice size', function () {
    expect(fn () => $this->resource->test_validate_pagination(0, 150))
        ->toThrow(\InvalidArgumentException::class, 'Slice size must be between 10 and 100');
});

test('validate pagination with only offset', function () {
    $result = $this->resource->test_validate_pagination(25, null);

    expect($result)->toBe(['offset' => '25']);
});

test('validate pagination with only slice size', function () {
    $result = $this->resource->test_validate_pagination(null, 75);

    expect($result)->toBe(['sliceSize' => 75]);
});
