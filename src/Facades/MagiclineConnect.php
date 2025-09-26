<?php

namespace AlexBabintsev\Magicline\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \AlexBabintsev\Magicline\Connect\Resources\Studios studios()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\Campaigns campaigns()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\Referrals referrals()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\Validation validation()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\AddressData addressData()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\Leads leads()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\TrialSessions trialSessions()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\RateBundles rateBundles()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\Contracts contracts()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\CreditCardTokenization creditCardTokenization()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\ImageUpload imageUpload()
 * @method static \AlexBabintsev\Magicline\Connect\Resources\ContractCancellation contractCancellation()
 * @method static \AlexBabintsev\Magicline\Connect\Http\MagiclineConnectClient getClient()
 *
 * @see \AlexBabintsev\Magicline\Connect\MagiclineConnect
 */
class MagiclineConnect extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AlexBabintsev\Magicline\Connect\MagiclineConnect::class;
    }
}
