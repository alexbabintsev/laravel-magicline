<?php

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CheckinVouchers;

beforeEach(function () {
    $this->client = $this->createMock(MagiclineClient::class);
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
        ->expects($this->once())
        ->method('post')
        ->with('/v1/checkin-vouchers/redeem', $data)
        ->willReturn($expectedResponse);

    $result = $this->resource->redeem($data);

    expect($result)->toBe($expectedResponse);
});
