<?php

namespace alexbabintsev\Magicline;

use alexbabintsev\Magicline\Http\MagiclineClient;
use alexbabintsev\Magicline\Resources\Appointments;
use alexbabintsev\Magicline\Resources\CheckinVouchers;
use alexbabintsev\Magicline\Resources\Classes;
use alexbabintsev\Magicline\Resources\CrossStudio;
use alexbabintsev\Magicline\Resources\Customers;
use alexbabintsev\Magicline\Resources\CustomersAccount;
use alexbabintsev\Magicline\Resources\CustomersCommunication;
use alexbabintsev\Magicline\Resources\CustomersSelfService;
use alexbabintsev\Magicline\Resources\Devices;
use alexbabintsev\Magicline\Resources\Employees;
use alexbabintsev\Magicline\Resources\Finance;
use alexbabintsev\Magicline\Resources\Memberships;
use alexbabintsev\Magicline\Resources\MembershipsSelfService;
use alexbabintsev\Magicline\Resources\Payments;
use alexbabintsev\Magicline\Resources\Studios;
use alexbabintsev\Magicline\Resources\TrialOffers;

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
