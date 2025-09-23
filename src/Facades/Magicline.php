<?php

namespace alexbabintsev\Magicline\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \alexbabintsev\Magicline\Resources\Appointments appointments()
 * @method static \alexbabintsev\Magicline\Resources\CheckinVouchers checkinVouchers()
 * @method static \alexbabintsev\Magicline\Resources\Classes classes()
 * @method static \alexbabintsev\Magicline\Resources\CrossStudio crossStudio()
 * @method static \alexbabintsev\Magicline\Resources\Customers customers()
 * @method static \alexbabintsev\Magicline\Resources\CustomersAccount customersAccount()
 * @method static \alexbabintsev\Magicline\Resources\CustomersCommunication customersCommunication()
 * @method static \alexbabintsev\Magicline\Resources\CustomersSelfService customersSelfService()
 * @method static \alexbabintsev\Magicline\Resources\Devices devices()
 * @method static \alexbabintsev\Magicline\Resources\Employees employees()
 * @method static \alexbabintsev\Magicline\Resources\Finance finance()
 * @method static \alexbabintsev\Magicline\Resources\Memberships memberships()
 * @method static \alexbabintsev\Magicline\Resources\MembershipsSelfService membershipsSelfService()
 * @method static \alexbabintsev\Magicline\Resources\Payments payments()
 * @method static \alexbabintsev\Magicline\Resources\Studios studios()
 * @method static \alexbabintsev\Magicline\Resources\TrialOffers trialOffers()
 *
 * @see \alexbabintsev\Magicline\Magicline
 */
class Magicline extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \alexbabintsev\Magicline\Magicline::class;
    }
}
