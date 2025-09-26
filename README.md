# Laravel package for seamless integration with the Magicline API — a fitness club management system. Easily connect schedules, bookings, members, and subscriptions to your Laravel app.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alexbabintsev/laravel-magicline.svg?style=flat-square)](https://packagist.org/packages/alexbabintsev/laravel-magicline)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/alexbabintsev/laravel-magicline/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/alexbabintsev/laravel-magicline/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/alexbabintsev/laravel-magicline/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/alexbabintsev/laravel-magicline/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/alexbabintsev/laravel-magicline.svg?style=flat-square)](https://packagist.org/packages/alexbabintsev/laravel-magicline)

Laravel Magicline provides a comprehensive, type-safe integration with **all three** Magicline APIs for fitness club management systems. The package offers:

- **Main API**: Studio management, customer data, employees, memberships, and internal operations
- **Connect API**: Public-facing integrations for websites - contracts, trial sessions, leads, and customer-facing features
- **Device API**: Hardware device integrations - card readers, vending machines, and time tracking devices
- **Webhooks**: Real-time event processing with automatic Laravel event dispatching

Built with modern Laravel best practices, this package includes robust error handling, automatic retries, comprehensive logging, advanced timezone support, and detailed DTOs for type safety.

## Requirements

- PHP 8.2+
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
    // Main Magicline API (with API key)
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

    // Connect API (public API, no API key required)
    'connect' => [
        'base_url' => env('MAGICLINE_CONNECT_BASE_URL', 'https://connectdemo.api.magicline.com/connect/v1'),
        'tenant' => env('MAGICLINE_CONNECT_TENANT'),
        'timeout' => env('MAGICLINE_CONNECT_TIMEOUT', 30),
        'retry' => [
            'times' => env('MAGICLINE_CONNECT_RETRY_TIMES', 3),
            'sleep' => env('MAGICLINE_CONNECT_RETRY_SLEEP', 100),
        ],
        'logging' => [
            'enabled' => env('MAGICLINE_CONNECT_LOGGING_ENABLED', false),
            'level' => env('MAGICLINE_CONNECT_LOGGING_LEVEL', 'debug'),
        ],
    ],

    // Webhooks (incoming events from Magicline)
    'webhooks' => [
        'api_key' => env('MAGICLINE_WEBHOOK_API_KEY'),
        'endpoint' => env('MAGICLINE_WEBHOOK_ENDPOINT', '/magicline/webhook'),
        'logging' => [
            'enabled' => env('MAGICLINE_WEBHOOK_LOGGING_ENABLED', true),
            'level' => env('MAGICLINE_WEBHOOK_LOGGING_LEVEL', 'info'),
        ],
    ],

    // Device API (for hardware integrations)
    'device' => [
        'base_url' => env('MAGICLINE_DEVICE_BASE_URL', 'https://open-api-demo.devices.magicline.com'),
        'bearer_token' => env('MAGICLINE_DEVICE_BEARER_TOKEN'),
        'timeout' => env('MAGICLINE_DEVICE_TIMEOUT', 30),
        'retry' => [
            'times' => env('MAGICLINE_DEVICE_RETRY_TIMES', 3),
            'delay' => env('MAGICLINE_DEVICE_RETRY_DELAY', 1000),
        ],
        'logging' => [
            'enabled' => env('MAGICLINE_DEVICE_LOGGING_ENABLED', true),
            'level' => env('MAGICLINE_DEVICE_LOGGING_LEVEL', 'info'),
        ],
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
# Main API
MAGICLINE_API_KEY=your-api-key-here
MAGICLINE_BASE_URL=https://your-tenant.magicline.com

# Connect API (public API)
MAGICLINE_CONNECT_TENANT=your-tenant-name

# Device API (hardware integrations)
MAGICLINE_DEVICE_BEARER_TOKEN=your-device-bearer-token

# Webhooks
MAGICLINE_WEBHOOK_API_KEY=your-webhook-api-key
```

## Usage

The package provides three main APIs plus webhook handling:

- **Main API**: For studio management and internal operations (requires API key)
- **Connect API**: For public websites and customer-facing integrations (no API key required)
- **Device API**: For hardware device integrations like card readers and vending machines (requires Bearer token)
- **Webhooks**: For receiving real-time events from Magicline (requires API key authentication)

### Main API Usage

```php
use AlexBabintsev\Magicline\Facades\Magicline;

// Get customers with pagination
$customers = Magicline::customers()->list(offset: 0, sliceSize: 50);

// Find a specific customer
$customer = Magicline::customers()->find(123);

// Get customer account balances
$balances = Magicline::customersAccount()->getBalances(123);
```

### Connect API Usage

```php
use AlexBabintsev\Magicline\Facades\MagiclineConnect;

// Get list of studios
$studios = MagiclineConnect::studios()->list();

// Create a new lead
$lead = MagiclineConnect::leads()->create([
    'customer' => [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'address' => [
            'street' => 'Main Street',
            'houseNumber' => '123',
            'zip' => '12345',
            'city' => 'Berlin',
            'country' => 'DE'
        ]
    ],
    'studioId' => 123,
    'sourceCampaignId' => 'web-form-2024'
]);

// Get available trial session slots
$sessions = MagiclineConnect::trialSessions()->getAvailableSlots([
    'studioId' => 123,
    'startDate' => '2024-02-01',
    'endDate' => '2024-02-07'
]);

// Book a trial session
$booking = MagiclineConnect::trialSessions()->book([
    'studioId' => 123,
    'startDateTime' => '2024-02-03T10:00:00.000+01:00[Europe/Berlin]',
    'leadCustomer' => [
        'firstName' => 'Jane',
        'lastName' => 'Smith',
        'email' => 'jane@example.com',
        'phone' => '+49 123 456789'
    ]
]);
```

### Device API Usage

The Device API allows integration with hardware devices like card readers, vending machines, and time tracking systems.

```php
use AlexBabintsev\Magicline\Device\MagiclineDevice;
use AlexBabintsev\Magicline\Device\DTOs\Identification\CardNumberIdentification;
use AlexBabintsev\Magicline\Device\DTOs\Identification\QrCodeIdentification;
use AlexBabintsev\Magicline\Device\DTOs\Identification\WalletPassIdentification;

// Initialize Device API client
$deviceApi = app(MagiclineDevice::class);

// Card Reader Access Control
$cardId = CardNumberIdentification::decimal('1234567890');
$accessRequest = CardReaderIdentificationRequest::create($cardId);

// Check access (dry run)
$dryRunRequest = CardReaderIdentificationRequest::dryRun($cardId);
$response = $deviceApi->access()->cardReaderIdentification($dryRunRequest);

if ($response->isSuccess()) {
    // Grant access
    $actualRequest = CardReaderIdentificationRequest::create($cardId, true);
    $deviceApi->access()->cardReaderIdentification($actualRequest);
}

// Vending Machine Integration
$qrId = QrCodeIdentification::create('{"uuid":"123e4567-e89b-12d3-a456-426614174000"}');

// Check customer authorization
$vendingIdentRequest = VendingIdentificationRequest::createWithGeneratedId($qrId);
$identResponse = $deviceApi->vending()->identification($vendingIdentRequest);

if ($identResponse->isAuthorized() && $identResponse->hasSufficientCredit(2.50)) {
    // Process sale
    $saleRequest = VendingSaleRequest::create(
        $qrId,
        $identResponse->getTransactionId(),
        'shelf-A1',
        2.50
    );

    $saleResponse = $deviceApi->vending()->sale($saleRequest);

    if ($saleResponse->isSuccess()) {
        echo "Sale completed: {$saleResponse->getText()}";
    }
}

// Time Tracking System
$walletPassId = WalletPassIdentification::create('123e4567-e89b-12d3-a456-426614174000');
$timeRequest = TimeIdentificationRequest::create($walletPassId);

$timeResponse = $deviceApi->time()->identification($timeRequest);
if ($timeResponse->isSuccess()) {
    echo "Time tracking recorded: {$timeResponse->getText()}";
}
```

### Webhook Handling

The package provides automatic webhook handling with Laravel event dispatching:

```php
// Add to routes/web.php or routes/api.php
Route::post('/magicline/webhook', [\AlexBabintsev\Magicline\Webhooks\Http\Controllers\WebhookController::class, 'handle'])
    ->middleware(['api', \AlexBabintsev\Magicline\Webhooks\Middleware\VerifyWebhookSignature::class]);

// Listen to webhook events in your EventServiceProvider
protected $listen = [
    \AlexBabintsev\Magicline\Webhooks\Events\CustomerCreated::class => [
        YourCustomerCreatedListener::class,
    ],
    \AlexBabintsev\Magicline\Webhooks\Events\ContractUpdated::class => [
        YourContractUpdatedListener::class,
    ],
    \AlexBabintsev\Magicline\Webhooks\Events\AppointmentBookingCreated::class => [
        YourBookingListener::class,
    ],
];

// Handle webhook events in listeners
class YourCustomerCreatedListener
{
    public function handle(CustomerCreated $event): void
    {
        $webhookEvent = $event->webhookEvent;

        logger()->info('New customer created', [
            'customer_id' => $webhookEvent->entityId,
            'event_type' => $webhookEvent->eventType,
            'timestamp' => $webhookEvent->eventDateTime,
        ]);

        // Process the customer creation asynchronously
        ProcessNewCustomer::dispatch($webhookEvent->entityId);
    }
}
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

### Main API Resources

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

### Connect API Resources (Public API)

- **Studios**: List studios and communication settings for public websites
- **Campaigns**: Marketing campaigns for lead tracking
- **Referrals**: Referral program management
- **Leads**: Lead generation and identity token handling
- **Trial Sessions**: Public trial session booking with timezone support
- **Rate Bundles**: Pricing plans with terms and modules for contracts
- **Contracts**: Contract creation, preview, and cancellation
- **Credit Card Tokenization**: Adyen integration for secure payment processing
- **Image Upload**: Member picture upload via pre-signed URLs
- **Validation**: Form validation and document verification
- **Address Data**: Geolocation and address validation services

### Device API Resources (Hardware Integration)

- **Access Resource**: Card reader identification and access control
- **Vending Resource**: Vending machine customer identification and sales processing
- **Time Resource**: Time tracking device identification and logging

#### Supported Identification Types:
- **Card Numbers**: Support for DECIMAL, HEX_MSB, and HEX_LSB formats
- **QR Codes**: JSON and string data support with customer UUID extraction
- **Barcodes**: Standard barcode identification
- **PINs**: Numeric PIN-based identification
- **Wallet Pass**: Apple Wallet pass UUID validation and version detection

### Webhook Event Types

The package supports all official Magicline webhook event types with automatic Laravel event dispatching:

#### Customer Events
- `CUSTOMER_CREATED` - Customer has been created
- `CUSTOMER_UPDATED` - Customer's data has been changed
- `CUSTOMER_DELETED` - Customer has been deleted
- `CUSTOMER_CHECKIN` - Customer has physically checked in at facility
- `CUSTOMER_CHECKOUT` - Customer has physically checked out from facility
- `CUSTOMER_ACCESS_DISABLED` - Customer's access was disabled
- `CUSTOMER_COMMUNICATION_PREFERENCES_UPDATED` - Communication preferences updated
- `AGGREGATOR_MEMBER_CREATED` - Aggregator member was created

#### Contract Events
- `CONTRACT_CREATED` - Main contract was created
- `CONTRACT_UPDATED` - Contract was changed
- `CONTRACT_CANCELLED` - Contract was cancelled

#### Booking Events
- `APPOINTMENT_BOOKING_CREATED` - Appointment booking was created
- `APPOINTMENT_BOOKING_UPDATED` - Appointment booking time or resource was updated
- `APPOINTMENT_BOOKING_CANCELLED` - Appointment booking was cancelled
- `CLASS_BOOKING_CREATED` - Class booking was created
- `CLASS_BOOKING_UPDATED` - Class booking was updated
- `CLASS_BOOKING_CANCELLED` - Class booking was cancelled

#### Class Events
- `CLASS_SLOT_UPDATED` - Class slot time or resource was updated
- `CLASS_SLOT_CANCELLED` - Class slot was cancelled

#### Employee Events
- `EMPLOYEE_CREATED` - Employee has been created at facility
- `EMPLOYEE_UPDATED` - Employee's data has been changed
- `EMPLOYEE_DELETED` - Employee has been deleted

#### Device Events
- `DEVICE_CREATED` - Device was created

#### Studio Events
- `ADDITIONAL_INFORMATION_FIELDS_UPDATED` - Additional information field was updated
- `AUTOMATIC_CUSTOMER_CHECKOUT` - One or multiple customers were automatically checked out

#### Finance Events
- `FINANCE_DEBT_COLLECTION_RUN_CREATED` - Debt collection run was created
- `FINANCE_DEBT_COLLECTION_CONFIGURATION_UPDATED` - Debt collection configuration was updated

#### Export Events
- `TAX_ADVISOR_EXPORT_CREATED` - Tax advisor export was created

## Device API Features

### Authentication and Token Management

The Device API uses persistent token authentication with separate tokens for each device:

```php
// Tokens are generated automatically when devices are created in studio
// Listen for DEVICE_CREATED webhook to get device information
protected $listen = [
    \AlexBabintsev\Magicline\Webhooks\Events\DeviceActivated::class => [
        App\Listeners\HandleNewDevice::class,
    ],
];

// After receiving device webhook, activate the device to get token
$deviceActivation = Magicline::devices()->activate('device-id-from-webhook');
$bearerToken = $deviceActivation['token'];

// Configure Device API with received token
config(['magicline.device.bearer_token' => $bearerToken]);
```

### Hardware Integration Support

The Device API supports three main categories of hardware devices with different operational patterns:

```php
// Access Control Devices (Card Readers)
$cardId = CardNumberIdentification::hexMsb('1A2B3C4D');
$accessRequest = CardReaderIdentificationRequest::create($cardId);
$response = $deviceApi->access()->cardReaderIdentification($accessRequest);

// Vending Machines
$qrId = QrCodeIdentification::create('customer-qr-data');
$vendingRequest = VendingIdentificationRequest::create($qrId, 'txn-' . uniqid());
$vendingResponse = $deviceApi->vending()->identification($vendingRequest);

// Time Tracking Devices
$walletId = WalletPassIdentification::create('123e4567-e89b-12d3-a456-426614174000');
$timeRequest = TimeIdentificationRequest::create($walletId);
$timeResponse = $deviceApi->time()->identification($timeRequest);
```

### Access Control Devices

Access devices use a two-phase approach: **Checks** and **Actions**.

- **Checks**: Always executed to verify customer permissions (studio access, parking, rooms, equipment)
- **Actions**: Only executed when `shouldExecuteAction=true` (check-in, parking slot updates, contingent booking)

```php
// Phase 1: Perform checks only (dry-run)
$checkRequest = CardReaderIdentificationRequest::dryRun($cardId);
$checkResult = $deviceApi->access()->cardReaderIdentification($checkRequest);

if ($checkResult->isSuccess()) {
    // Phase 2: Execute actual actions
    $actionRequest = CardReaderIdentificationRequest::create($cardId, true);
    $deviceApi->access()->cardReaderIdentification($actionRequest);
}
```

### Vending Machine Concurrency Control

Vending devices require special concurrency control to prevent abuse through transaction locking:

```php
// Generate unique transaction ID (UUID format required)
$transactionId = VendingIdentificationRequest::generateTransactionId();

// Step 1: Customer identification (locks customer for this transaction)
$identRequest = VendingIdentificationRequest::create($qrId, $transactionId);
$identResponse = $deviceApi->vending()->identification($identRequest);

if ($identResponse->isAuthorized() && $identResponse->hasSufficientCredit(2.50)) {
    // Step 2: Validate sale (dry-run)
    $saleValidation = VendingSaleRequest::create($qrId, $transactionId, 'shelf-A1', 2.50, false);
    $validationResult = $deviceApi->vending()->sale($saleValidation);

    if ($validationResult->isSuccess()) {
        // Step 3: Execute actual sale (releases lock)
        $actualSale = VendingSaleRequest::create($qrId, $transactionId, 'shelf-A1', 2.50, true);
        $saleResult = $deviceApi->vending()->sale($actualSale);
    }
}
```

#### Vending Timeouts

Two configurable timeouts prevent abandoned transactions:

```php
// Configure timeouts in studio settings:

// 1. Idle Timeout (default: 15 seconds)
// - Starts after customer identification
// - Cancels if customer doesn't select product
// - Releases customer lock automatically

// 2. Product Draw Timeout
// - Starts after product selection (shouldExecuteAction=false)
// - Cancels if customer doesn't complete purchase
// - Allows customer to use other devices

// Important: Coordinate timeout values with studio operators
```

### Time Tracking Devices

Time devices follow identification → usage pattern:

```php
// Step 1: Customer identification
$timeIdent = TimeIdentificationRequest::create($walletPassId);
$identResult = $deviceApi->time()->identification($timeIdent);

if ($identResult->isSuccess()) {
    // Step 2: Usage validation (dry-run)
    $usageValidation = TimeUsageRequest::dryRun($walletPassId, 'solarium-booth-1');
    $validationResult = $deviceApi->time()->usage($usageValidation);

    if ($validationResult->isSuccess()) {
        // Step 3: Execute usage (book benefit/charge customer)
        $actualUsage = TimeUsageRequest::create($walletPassId, 'solarium-booth-1', true);
        $usageResult = $deviceApi->time()->usage($actualUsage);
    }
}
```

### Dry-Run Support

All device operations support dry-run mode for validation without execution:

```php
// Validate access without actually granting it
$dryRunRequest = CardReaderIdentificationRequest::dryRun($cardId);
$validation = $deviceApi->access()->cardReaderIdentification($dryRunRequest);

if ($validation->isSuccess()) {
    // Now perform the actual operation
    $actualRequest = CardReaderIdentificationRequest::create($cardId, true);
    $deviceApi->access()->cardReaderIdentification($actualRequest);
}

// Validate vending purchase without processing payment
$dryRunSale = VendingSaleRequest::dryRun($qrId, 'txn-123', 'shelf-A1', 2.50);
$saleValidation = $deviceApi->vending()->sale($dryRunSale);
```

### Money and Transaction Handling

The Device API includes robust money handling with currency support:

```php
use AlexBabintsev\Magicline\Device\DTOs\Money;

// Create money objects with automatic currency formatting
$price = Money::create(2.50, 'EUR');
echo $price->format(); // "2.50 EUR"

// Convert to/from cents for precise calculations
$cents = $price->toCents(); // 250
$fromCents = Money::fromCents(250, 'EUR'); // 2.50 EUR

// Built-in validation methods
if ($price->isPositive()) {
    // Process transaction
}
```

### Identification Method Validation

Each identification type includes built-in validation:

```php
// Card number format validation
$cardId = CardNumberIdentification::decimal('1234567890');
if ($cardId->isValidDecimal()) {
    // Process decimal card number
}

$hexCard = CardNumberIdentification::hexMsb('1A2B3C4D');
if ($hexCard->isValidHex()) {
    // Process hex card number
}

// UUID validation for Wallet Pass
$walletPass = WalletPassIdentification::create('123e4567-e89b-12d3-a456-426614174000');
if ($walletPass->isValidUuid()) {
    echo "UUID Version: " . $walletPass->getUuidVersion();
}

// QR Code data extraction
$qrCode = QrCodeIdentification::create('{"uuid":"customer-uuid","tenant":"gym1"}');
if ($qrCode->isJson()) {
    $customerUuid = $qrCode->getCustomerUuid();
    $tenant = $qrCode->getTenant();
}
```

### Error Handling and Reliability

The Device API includes robust error handling for various scenarios:

```php
use AlexBabintsev\Magicline\Exceptions\MagiclineApiException;

try {
    $response = $deviceApi->vending()->identification($request);
} catch (MagiclineApiException $e) {
    if ($e->getHttpStatusCode() === 503) {
        // Database transaction timeout - safe to retry
        logger()->warning('Device API timeout, retrying...', [
            'device_type' => 'vending',
            'transaction_id' => $request->getTransactionId()
        ]);

        // Retry after delay
        sleep(2);
        $response = $deviceApi->vending()->identification($request);
    } else {
        // Other API errors (customer not authorized, etc.)
        logger()->error('Device API error', [
            'status' => $e->getHttpStatusCode(),
            'error' => $e->getMessage(),
            'details' => $e->getErrorDetails()
        ]);
    }
}
```

### Localization Support

The Device API supports 15+ languages for error messages and responses:

```php
// Configure language via Accept-Language header
$deviceApi = app(MagiclineDevice::class);

// Supported languages: cs, de, en, es, fr, hu, it, nb, nl, pl, ro, ru, sl, sv, tr
// Country variants: de-CH, de-LI, en-CA, en-GB, en-US, fr-LU

// Language is automatically set based on:
// 1. Accept-Language header (if provided)
// 2. Studio's configured language (fallback)
// 3. English (default fallback)

// The HTTP client automatically handles localization
$response = $deviceApi->access()->cardReaderIdentification($request);
// Error messages will be in the appropriate language
```

### Production Considerations

Important considerations for production deployments:

```php
// 1. Token Management
// Store device tokens securely per device
$deviceTokens = [
    'card-reader-1' => 'bearer-token-1',
    'vending-machine-a' => 'bearer-token-2',
    'time-tracker-booth-1' => 'bearer-token-3',
];

// 2. Concurrency for Vending Machines
// Implement proper locking mechanism
Redis::lock("vending-customer-{$customerId}", 30)->block(5, function () use ($deviceApi, $request) {
    return $deviceApi->vending()->identification($request);
});

// 3. Timeout Configuration
// Coordinate with studio operators on appropriate timeout values
$timeouts = [
    'idle_timeout' => 15,        // seconds after identification
    'product_draw_timeout' => 30, // seconds between selection and purchase
];

// 4. Monitoring and Logging
// Log all device operations for audit trail
logger()->info('Device operation', [
    'device_id' => $deviceId,
    'operation' => 'identification',
    'customer_id' => $customerId,
    'transaction_id' => $transactionId,
    'success' => $response->isSuccess()
]);

// 5. Error Recovery
// Implement retry logic for transient failures
$retryAttempts = 3;
$retryDelay = 1000; // milliseconds

// This is handled automatically by the HTTP client
```

## Connect API Features

### Timezone Support

The Connect API includes advanced timezone handling for trial sessions:

```php
// Supports both old UTC format and new timezone-aware format
$slots = MagiclineConnect::trialSessions()->getAvailableSlots([
    'studioId' => 123,
    'startDate' => '2024-02-01',
    'endDate' => '2024-02-07'
]);

// Automatically handles timezone conversions for booking
MagiclineConnect::trialSessions()->book([
    'startDateTime' => '2024-02-03T10:00:00.000+01:00[Europe/Berlin]', // New format
    // or
    'startDateTime' => '2024-02-03T09:00:00.000Z', // Old UTC format (also supported)
]);
```

### Localization Support

Smart locale resolution with fallback chain:

```php
$customer = [
    'locale' => 'de_CH',      // Highest priority
    'language' => 'fr',       // Medium priority (overridden by locale)
    'countryCode' => 'CH',    // Fallback (not used when locale is present)
];

// Automatically resolves to: language='de', locale='de_CH'
```

### Contract Management

Full contract lifecycle with signatures and payments:

```php
// Preview contract with voucher
$preview = MagiclineConnect::contracts()->preview([
    'studioId' => 123,
    'rateBundleTermId' => 456,
    'customer' => ['dateOfBirth' => '1990-01-01'],
    'voucherCode' => 'DISCOUNT20'
]);

// Create contract with digital signatures
$contract = MagiclineConnect::contracts()->create([
    'studioId' => 123,
    'rateBundleTermId' => 456,
    'paymentChoice' => 'CREDIT_CARD',
    'creditCard' => ['tokenizationReference' => $tokenRef],
    'signatures' => [
        'contractSignature' => $svgBase64,
        'sepaSignature' => $sepaSignature,
        'textBlockSignatures' => [
            ['textBlockId' => 789, 'signature' => $textSignature]
        ]
    ]
]);
```

## Webhook Features

### Automatic Event Dispatching

The webhook system automatically dispatches Laravel events for all incoming Magicline webhooks:

```php
// Supported webhook events are automatically converted to Laravel events
// Example: CUSTOMER_CREATED webhook becomes CustomerCreated Laravel event

protected $listen = [
    \AlexBabintsev\Magicline\Webhooks\Events\CustomerCreated::class => [
        App\Listeners\SendWelcomeEmail::class,
        App\Listeners\CreateUserAccount::class,
    ],
    \AlexBabintsev\Magicline\Webhooks\Events\PaymentFailed::class => [
        App\Listeners\NotifyAccountingTeam::class,
        App\Listeners\SuspendMembership::class,
    ],
];
```

### Webhook Validation and Security

All webhooks are automatically validated for security:

```php
// Add the middleware to your webhook route
Route::post('/magicline/webhook', [\AlexBabintsev\Magicline\Webhooks\Http\Controllers\WebhookController::class, 'handle'])
    ->middleware([
        'api',
        \AlexBabintsev\Magicline\Webhooks\Middleware\VerifyWebhookSignature::class
    ]);
```

The middleware provides:
- **API Key Authentication**: Validates X-API-KEY header using timing-safe comparison
- **Content Type Validation**: Ensures JSON payloads
- **Request Method Validation**: Only allows POST requests
- **Automatic Logging**: Configurable request/response logging via MagiclineLog model

### Event Processing

```php
// Access webhook data in your listeners
class HandleCustomerUpdate
{
    public function handle(\AlexBabintsev\Magicline\Webhooks\Events\CustomerUpdated $event): void
    {
        $webhookEvent = $event->webhookEvent;

        // Access all webhook data
        $customerId = $webhookEvent->entityId;
        $eventType = $webhookEvent->eventType; // 'CUSTOMER_UPDATED'
        $timestamp = $webhookEvent->eventDateTime;
        $studioId = $webhookEvent->studioId;

        // Process asynchronously for performance
        ProcessCustomerUpdate::dispatch($customerId, $webhookEvent->toArray());
    }
}
```

### Event Categories and Priorities

Different event types have different processing priorities:

```php
// High priority events (immediate processing required)
- PAYMENT_FAILED
- SYSTEM_MAINTENANCE_SCHEDULED
- DEVICE_DEACTIVATED

// Medium priority events (process within minutes)
- CUSTOMER_CREATED, CUSTOMER_UPDATED
- CONTRACT_CREATED, CONTRACT_UPDATED
- APPOINTMENT_BOOKING_CREATED

// Low priority events (process within hours)
- CUSTOMER_DELETED
- TRIAL_SESSION_CANCELLED
- EMPLOYEE_UPDATED
```

### Webhook Status and Monitoring

```php
// Check webhook processing status
Route::get('/magicline/webhook/status', [\AlexBabintsev\Magicline\Webhooks\Http\Controllers\WebhookController::class, 'status']);

// Returns JSON with system status:
{
    "status": "ok",
    "timestamp": "2024-02-03T10:15:30.000Z",
    "version": "1.0.0"
}
```

### Event Priority System

The webhook system includes automatic event prioritization for processing efficiency:

```php
// High priority events (processed immediately)
- CUSTOMER_CHECKIN/CHECKOUT - Real-time facility access
- CONTRACT_CREATED/CANCELLED - Business critical operations
- CUSTOMER_ACCESS_DISABLED - Security-related events

// Medium priority events (processed within minutes)
- CUSTOMER_CREATED/UPDATED - Customer data changes
- BOOKING events - Appointment and class bookings
- EMPLOYEE events - Staff management

// Low priority events (processed within hours)
- ADDITIONAL_INFORMATION_FIELDS_UPDATED - Configuration changes
- TAX_ADVISOR_EXPORT_CREATED - Administrative exports
- FINANCE_DEBT_COLLECTION events - Financial processes
```

### Handling Processing Failures

```php
// The webhook handler includes automatic error handling
class YourWebhookListener
{
    public function handle($event): void
    {
        try {
            // Your processing logic
            $this->processWebhookEvent($event->webhookEvent);
        } catch (\Exception $e) {
            // Webhook system will log the error and continue
            // Your application won't crash from webhook processing errors
            logger()->error('Webhook processing failed', [
                'event_type' => $event->webhookEvent->eventType,
                'entity_id' => $event->webhookEvent->entityId,
                'error' => $e->getMessage()
            ]);

            // Optionally re-queue for retry
            ProcessWebhookEvent::dispatch($event->webhookEvent)->delay(now()->addMinutes(5));
        }
    }
}
```

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
