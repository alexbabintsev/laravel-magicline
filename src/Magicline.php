<?php

namespace AlexBabintsev\Magicline;

use AlexBabintsev\Magicline\Http\MagiclineClient;
use AlexBabintsev\Magicline\Resources\Appointments;
use AlexBabintsev\Magicline\Resources\CheckinVouchers;
use AlexBabintsev\Magicline\Resources\Classes;
use AlexBabintsev\Magicline\Resources\CrossStudio;
use AlexBabintsev\Magicline\Resources\Customers;
use AlexBabintsev\Magicline\Resources\CustomersAccount;
use AlexBabintsev\Magicline\Resources\CustomersCommunication;
use AlexBabintsev\Magicline\Resources\CustomersSelfService;
use AlexBabintsev\Magicline\Resources\Devices;
use AlexBabintsev\Magicline\Resources\Employees;
use AlexBabintsev\Magicline\Resources\Finance;
use AlexBabintsev\Magicline\Resources\Memberships;
use AlexBabintsev\Magicline\Resources\MembershipsSelfService;
use AlexBabintsev\Magicline\Resources\Payments;
use AlexBabintsev\Magicline\Resources\Studios;
use AlexBabintsev\Magicline\Resources\TrialOffers;

class Magicline
{
    public function __construct(
        private MagiclineClient $client
    ) {}

    public function appointments(): Appointments
    {
        return new Appointments($this->client);
    }

    public function checkinVouchers(): CheckinVouchers
    {
        return new CheckinVouchers($this->client);
    }

    public function classes(): Classes
    {
        return new Classes($this->client);
    }

    public function crossStudio(): CrossStudio
    {
        return new CrossStudio($this->client);
    }

    public function customers(): Customers
    {
        return new Customers($this->client);
    }

    public function customersAccount(): CustomersAccount
    {
        return new CustomersAccount($this->client);
    }

    public function customersCommunication(): CustomersCommunication
    {
        return new CustomersCommunication($this->client);
    }

    public function customersSelfService(): CustomersSelfService
    {
        return new CustomersSelfService($this->client);
    }

    public function devices(): Devices
    {
        return new Devices($this->client);
    }

    public function employees(): Employees
    {
        return new Employees($this->client);
    }

    public function finance(): Finance
    {
        return new Finance($this->client);
    }

    public function memberships(): Memberships
    {
        return new Memberships($this->client);
    }

    public function membershipsSelfService(): MembershipsSelfService
    {
        return new MembershipsSelfService($this->client);
    }

    public function payments(): Payments
    {
        return new Payments($this->client);
    }

    public function studios(): Studios
    {
        return new Studios($this->client);
    }

    public function trialOffers(): TrialOffers
    {
        return new TrialOffers($this->client);
    }
}
