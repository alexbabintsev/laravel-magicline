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
