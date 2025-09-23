# Laravel package for seamless integration with the Magicline API — a fitness club management system. Easily connect schedules, bookings, members, and subscriptions to your Laravel app.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alexbabintsev/laravel-magicline.svg?style=flat-square)](https://packagist.org/packages/alexbabintsev/laravel-magicline)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/alexbabintsev/laravel-magicline/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/alexbabintsev/laravel-magicline/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/alexbabintsev/laravel-magicline/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/alexbabintsev/laravel-magicline/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/alexbabintsev/laravel-magicline.svg?style=flat-square)](https://packagist.org/packages/alexbabintsev/laravel-magicline)

Laravel Magicline provides a comprehensive, type-safe integration with the Magicline API for fitness club management systems. The package offers intuitive resource-based access to all Magicline endpoints including customer management, class scheduling, appointment booking, membership handling, and payment processing.

Built with modern Laravel best practices, this package includes robust error handling, automatic retries, comprehensive logging, and detailed DTOs for type safety.

## Requirements

- PHP 8.1+
- Laravel 10.x, 11.x or 12.x

## Installation

You can install the package via composer:

```bash
composer require alexbabintsev/laravel-magicline
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="magicline-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="magicline-config"
```

This is the contents of the published config file:

```php
return [
    'api_key' => env('MAGICLINE_API_KEY'),
    'base_url' => env('MAGICLINE_BASE_URL', 'https://open-api-demo.open-api.magicline.com'),
    'timeout' => env('MAGICLINE_TIMEOUT', 30),
    'retry' => [
        'times' => env('MAGICLINE_RETRY_TIMES', 3),
        'sleep' => env('MAGICLINE_RETRY_SLEEP', 100),
    ],
    'pagination' => [
        'default_slice_size' => 50,
        'max_slice_size' => 100,
        'min_slice_size' => 10,
    ],
    'logging' => [
        'enabled' => env('MAGICLINE_LOGGING_ENABLED', false),
        'level' => env('MAGICLINE_LOGGING_LEVEL', 'debug'),
    ],
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="magicline-views"
```

## Configuration

Set your Magicline API credentials in your `.env` file:

```env
MAGICLINE_API_KEY=your-api-key-here
MAGICLINE_BASE_URL=https://your-tenant.magicline.com
```

## Usage

### Basic Usage

```php
use alexbabintsev\Magicline\Facades\Magicline;

// Get customers with pagination
$customers = Magicline::customers()->list(offset: 0, sliceSize: 50);

// Find a specific customer
$customer = Magicline::customers()->find(123);

// Get customer account balances
$balances = Magicline::customersAccount()->getBalances(123);
```

### Working with Classes

```php
// List available classes
$classes = Magicline::classes()->list();

// Book a class
$booking = Magicline::classes()->book(456, [
    'customerId' => 123,
    'notes' => 'First time booking'
]);
```

### Appointments

```php
// Get bookable appointments
$appointments = Magicline::appointments()->getBookable();

// Book an appointment
$booking = Magicline::appointments()->book([
    'customerId' => 123,
    'appointmentId' => 789,
    'notes' => 'Personal training session'
]);
```

### Memberships

```php
// Get available membership offers
$offers = Magicline::memberships()->getOffers();

// Get customer contract data
$contracts = Magicline::membershipsSelfService()->getContractData(123);

// Cancel membership contract
$cancellation = Magicline::membershipsSelfService()->cancelOrdinaryContract(123, [
    'reason' => 'Moving to another city',
    'cancellationDate' => '2024-12-31'
]);
```

### Payments

```php
// Create user payment session
$paymentSession = Magicline::payments()->createUserSession([
    'customerId' => 123,
    'amount' => 5999, // in cents
    'currency' => 'EUR',
    'description' => 'Monthly membership fee',
    'returnUrl' => 'https://yourapp.com/payment/success',
    'cancelUrl' => 'https://yourapp.com/payment/cancel'
]);
```

### Devices & Studio Management

```php
// List all gym devices
$devices = Magicline::devices()->list();

// Activate a specific device
$activation = Magicline::devices()->activate('device-123');

// Get studio utilization (current occupancy)
$utilization = Magicline::studios()->getUtilization();
echo "Current occupancy: {$utilization['currentCount']}/{$utilization['maxCapacity']}";
```

### Employees

```php
// List all employees with pagination
$employees = Magicline::employees()->list(offset: 0, sliceSize: 25);

// Access employee information
foreach ($employees['data'] as $employee) {
    echo "Employee: {$employee['firstName']} {$employee['lastName']}";
    echo "Position: {$employee['position']}";
}
```

### Customer Self-Service

```php
// Get customer contact data
$contactData = Magicline::customersSelfService()->getContactData(123);

// Request contact data amendment
$amendment = Magicline::customersSelfService()->createContactDataAmendment(123, [
    'email' => 'newemail@example.com',
    'phone' => '+49123456789',
    'address' => [
        'street' => 'Neue Straße 456',
        'city' => 'Berlin',
        'postalCode' => '10115'
    ]
]);
```

### Customer Communication

```php
// Create new communication thread
$thread = Magicline::customersCommunication()->createThread(123, [
    'subject' => 'Question about membership',
    'message' => 'I have a question about upgrading my membership.',
    'priority' => 'normal'
]);

// Add message to existing thread
$response = Magicline::customersCommunication()->addToThread(123, 'thread-456', [
    'message' => 'Thank you for your quick response!',
    'attachments' => []
]);
```

### Customer Account Management

```php
// Get customer account balances
$balances = Magicline::customersAccount()->getBalances(123);

echo "Current balance: {$balances['currentBalance']} {$balances['currency']}";
echo "Outstanding amount: {$balances['outstandingAmount']} {$balances['currency']}";
```

### Trial Offers

```php
// Get bookable trial classes
$trialClasses = Magicline::trialOffers()->getBookableClasses(offset: 0, sliceSize: 20);

// Get bookable trial appointments
$trialAppointments = Magicline::trialOffers()->getBookableAppointments(offset: 0, sliceSize: 10);

// Book trial class
foreach ($trialClasses['data'] as $class) {
    if ($class['availableSpots'] > 0) {
        $booking = Magicline::classes()->book($class['id'], [
            'customerId' => 456, // Trial customer
            'notes' => 'First trial class'
        ]);
        break;
    }
}
```

### Check-in Vouchers

```php
// Redeem check-in voucher
$redemption = Magicline::checkinVouchers()->redeem([
    'voucherCode' => 'VOUCHER123',
    'customerId' => 123,
    'locationId' => 'gym-main'
]);

if ($redemption['success']) {
    echo "Voucher redeemed successfully!";
    echo "Remaining uses: {$redemption['remainingUses']}";
}
```

### Cross Studio Operations

```php
// Find customers across multiple studios
$crossStudioCustomers = Magicline::crossStudio()->getCustomersBy([
    'email' => 'customer@example.com',
    'includeInactive' => false
]);

// Access customer data from different studios
foreach ($crossStudioCustomers['customers'] as $customer) {
    echo "Found customer at studio: {$customer['studioName']}";
    echo "Customer ID: {$customer['customerId']}";
    echo "Status: {$customer['status']}";
}
```

### Finance & Debt Collection

```php
// Get debt collection configuration
$debtConfig = Magicline::finance()->getDebtCollectionConfiguration();

echo "Grace period: {$debtConfig['gracePeriodDays']} days";
echo "Late fee: {$debtConfig['lateFeeAmount']} {$debtConfig['currency']}";
```

### Advanced Usage with Pagination

```php
// Handle large datasets with pagination
$offset = 0;
$sliceSize = 100;
$allCustomers = [];

do {
    $response = Magicline::customers()->list($offset, $sliceSize);
    $customers = $response['data'];

    $allCustomers = array_merge($allCustomers, $customers);
    $offset += $sliceSize;

    // Continue until we get less than requested slice size
} while (count($customers) === $sliceSize);

echo "Total customers loaded: " . count($allCustomers);
```

### Error Handling

```php
use alexbabintsev\Magicline\Exceptions\MagiclineApiException;
use alexbabintsev\Magicline\Exceptions\MagiclineAuthenticationException;

try {
    $customers = Magicline::customers()->list();
} catch (MagiclineAuthenticationException $e) {
    // Handle authentication errors
    logger()->error('Magicline authentication failed: ' . $e->getMessage());
} catch (MagiclineApiException $e) {
    // Handle other API errors
    logger()->error('Magicline API error: ' . $e->getMessage(), [
        'status_code' => $e->getHttpStatusCode(),
        'error_code' => $e->getErrorCode(),
        'details' => $e->getErrorDetails()
    ]);
}
```

### Using DTOs

```php
use alexbabintsev\Magicline\DataTransferObjects\Customer;

$customersData = Magicline::customers()->list();
$customers = Customer::collection($customersData['data']);

foreach ($customers as $customer) {
    echo "Customer: {$customer->firstName} {$customer->lastName}";
    echo "Email: {$customer->email}";
    if ($customer->address) {
        echo "City: {$customer->address->city}";
    }
}
```

### Testing the Connection

```bash
php artisan magicline:test
php artisan magicline:test --endpoint=customers
php artisan magicline:test --endpoint=studios
```

## Available Resources

- **Appointments**: Book and manage personal training appointments
- **Classes**: List, book and cancel group fitness classes
- **Customers**: Retrieve customer information and manage profiles
- **Customer Account**: Access customer billing and balance information
- **Customer Communication**: Send messages and manage communication threads
- **Customer Self-Service**: Handle customer self-service requests
- **Devices**: Manage gym equipment and device activation
- **Employees**: Access staff information
- **Finance**: Handle debt collection and financial operations
- **Memberships**: Manage membership offers and contracts
- **Membership Self-Service**: Customer membership management
- **Payments**: Process payments and manage payment sessions
- **Studios**: Get studio information and utilization data
- **Trial Offers**: Manage trial memberships and bookings
- **Checkin Vouchers**: Redeem gym entry vouchers
- **Cross Studio**: Cross-location customer operations

## Testing

```bash
composer test
composer test-coverage
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alex Babintsev](https://github.com/alexbabintsev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
