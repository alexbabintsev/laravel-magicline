<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CheckinVouchers;
use Mockery;

beforeEach(function () {
    $this->client = Mockery::mock(MagiclineClient::class);
    $this->resource = new CheckinVouchers($this->client);
});

test('redeem voucher', function () {
    $data = [
        'voucherCode' => 'VOUCHER123',
        'customerId' => 456,
    ];

    $expectedResponse = [
        'success' => true,
        'message' => 'Voucher redeemed successfully',
    ];

    $this->client
        ->shouldReceive('post')
        ->once()
        ->with('/v1/checkin-vouchers/redeem', $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->redeem($data);

    expect($result)->toBe($expectedResponse);
});
