<?php

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\CheckinVouchers;

beforeEach(function () {
    $this->mockClient = Mockery::mock(MagiclineClient::class);
    $this->resource = new CheckinVouchers($this->mockClient);
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

    $this->mockClient
        ->shouldReceive('post')
        ->once()
        ->with('/v1/checkin-vouchers/redeem', $data)
        ->andReturn($expectedResponse);

    $result = $this->resource->redeem($data);

    expect($result)->toBe($expectedResponse);
});
