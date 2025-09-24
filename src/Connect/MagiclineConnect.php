<?php

namespace AlexBabintsev\Magicline\Connect;

use AlexBabintsev\Magicline\Connect\Http\MagiclineConnectClient;
use AlexBabintsev\Magicline\Connect\Resources\Studios;
use AlexBabintsev\Magicline\Connect\Resources\Campaigns;
use AlexBabintsev\Magicline\Connect\Resources\Referrals;
use AlexBabintsev\Magicline\Connect\Resources\Validation;
use AlexBabintsev\Magicline\Connect\Resources\AddressData;
use AlexBabintsev\Magicline\Connect\Resources\Leads;
use AlexBabintsev\Magicline\Connect\Resources\TrialSessions;
use AlexBabintsev\Magicline\Connect\Resources\RateBundles;
use AlexBabintsev\Magicline\Connect\Resources\Contracts;
use AlexBabintsev\Magicline\Connect\Resources\CreditCardTokenization;
use AlexBabintsev\Magicline\Connect\Resources\ImageUpload;
use AlexBabintsev\Magicline\Connect\Resources\ContractCancellation;

class MagiclineConnect
{
    public function __construct(
        private readonly MagiclineConnectClient $client
    ) {}

    /**
     * Studios management - get studio list and communication settings
     */
    public function studios(): Studios
    {
        return new Studios($this->client);
    }

    /**
     * Marketing campaigns for leads
     */
    public function campaigns(): Campaigns
    {
        return new Campaigns($this->client);
    }

    /**
     * Referral programs
     */
    public function referrals(): Referrals
    {
        return new Referrals($this->client);
    }

    /**
     * Validation configuration and utilities
     */
    public function validation(): Validation
    {
        return new Validation($this->client);
    }

    /**
     * Address and geolocation data
     */
    public function addressData(): AddressData
    {
        return new AddressData($this->client);
    }

    /**
     * Lead generation and management
     */
    public function leads(): Leads
    {
        return new Leads($this->client);
    }

    /**
     * Trial sessions booking with timezone support
     */
    public function trialSessions(): TrialSessions
    {
        return new TrialSessions($this->client);
    }

    /**
     * Rate bundles (pricing plans) with terms and modules
     */
    public function rateBundles(): RateBundles
    {
        return new RateBundles($this->client);
    }

    /**
     * Contract creation, preview, and management
     */
    public function contracts(): Contracts
    {
        return new Contracts($this->client);
    }

    /**
     * Credit card tokenization via Adyen
     */
    public function creditCardTokenization(): CreditCardTokenization
    {
        return new CreditCardTokenization($this->client);
    }

    /**
     * Image upload for member pictures
     */
    public function imageUpload(): ImageUpload
    {
        return new ImageUpload($this->client);
    }

    /**
     * Contract cancellation (online cancellation)
     */
    public function contractCancellation(): ContractCancellation
    {
        return new ContractCancellation($this->client);
    }

    /**
     * Get the underlying HTTP client
     */
    public function getClient(): MagiclineConnectClient
    {
        return $this->client;
    }
}