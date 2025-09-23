<?php

namespace alexbabintsev\Magicline\Tests\Unit\Resources;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\CheckinVouchers;
use alexbabintsev\Magicline\Tests\TestCase;

class CheckinVouchersTest extends TestCase
{
    protected CheckinVouchers $resource;

    protected MagiclineClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MagiclineClient::class);
        $this->resource = new CheckinVouchers($this->client);
    }

    public function test_redeem_voucher()
    {
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
    }
}
