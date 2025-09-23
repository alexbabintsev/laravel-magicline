<?php

namespace AlexBabintsev\Magicline\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \AlexBabintsev\Magicline\Resources\Appointments appointments()
 * @method static \AlexBabintsev\Magicline\Resources\CheckinVouchers checkinVouchers()
 * @method static \AlexBabintsev\Magicline\Resources\Classes classes()
 * @method static \AlexBabintsev\Magicline\Resources\CrossStudio crossStudio()
 * @method static \AlexBabintsev\Magicline\Resources\Customers customers()
 * @method static \AlexBabintsev\Magicline\Resources\CustomersAccount customersAccount()
 * @method static \AlexBabintsev\Magicline\Resources\CustomersCommunication customersCommunication()
 * @method static \AlexBabintsev\Magicline\Resources\CustomersSelfService customersSelfService()
 * @method static \AlexBabintsev\Magicline\Resources\Devices devices()
 * @method static \AlexBabintsev\Magicline\Resources\Employees employees()
 * @method static \AlexBabintsev\Magicline\Resources\Finance finance()
 * @method static \AlexBabintsev\Magicline\Resources\Memberships memberships()
 * @method static \AlexBabintsev\Magicline\Resources\MembershipsSelfService membershipsSelfService()
 * @method static \AlexBabintsev\Magicline\Resources\Payments payments()
 * @method static \AlexBabintsev\Magicline\Resources\Studios studios()
 * @method static \AlexBabintsev\Magicline\Resources\TrialOffers trialOffers()
 *
 * @see \AlexBabintsev\Magicline\Magicline
 */
class Magicline extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AlexBabintsev\Magicline\Magicline::class;
    }
}
