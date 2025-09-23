# Changelog

All notable changes to `laravel-magicline` will be documented in this file.

## [1.0.0] - 2025-09-24

### Added
- Initial release of Laravel Magicline package
- Complete integration with Magicline API for fitness club management
- Support for all major Magicline API endpoints:
  - **Customers**: List, find, and manage customer profiles
  - **Appointments**: Book and manage personal training appointments
  - **Classes**: List, book, and cancel group fitness classes
  - **Customer Account**: Access billing and balance information
  - **Customer Communication**: Send messages and manage communication threads
  - **Customer Self-Service**: Handle customer self-service requests
  - **Devices**: Manage gym equipment and device activation
  - **Employees**: Access staff information with pagination
  - **Finance**: Handle debt collection and financial operations
  - **Memberships**: Manage membership offers and contracts
  - **Membership Self-Service**: Customer membership management
  - **Payments**: Process payments and manage payment sessions
  - **Studios**: Get studio information and utilization data
  - **Trial Offers**: Manage trial memberships and bookings
  - **Checkin Vouchers**: Redeem gym entry vouchers
  - **Cross Studio**: Cross-location customer operations

### Features
- **Type-safe DTOs**: Comprehensive data transfer objects for all API responses
- **Robust HTTP Client**: Built-in retry logic with configurable parameters
- **Exception Handling**: Specific exception types for different API error scenarios
- **Comprehensive Logging**: Optional request/response logging with configurable levels
- **Laravel Integration**: Service provider, facade, and Artisan commands
- **Pagination Support**: Built-in pagination handling with validation
- **Configuration Management**: Flexible configuration with environment variables
- **Testing Command**: `magicline:test` Artisan command for API connectivity testing

### Technical Details
- **PHP Requirements**: PHP 8.2+ with support for modern PHP features
- **Laravel Compatibility**: Laravel 10.x, 11.x, and 12.x
- **Code Quality**: PHPStan Level 5 analysis with Larastan
- **Code Style**: Laravel Pint formatting following Laravel standards
- **Testing**: Comprehensive test suite using Pest PHP with 61.3% code coverage
- **Architecture**: Resource-based API access pattern with base classes
- **Security**: Secure API key handling and request authentication

### Configuration
- Configurable API timeouts and retry mechanisms
- Flexible pagination settings with min/max limits
- Optional request/response logging for debugging
- Environment-based configuration for different deployment stages

### Documentation
- Comprehensive README with usage examples for all endpoints
- Inline code documentation with PHPDoc standards
- Contributing guidelines for open-source collaboration
- Type hints and IDE support for better development experience

### Dependencies
- `spatie/laravel-package-tools`: Package development utilities
- `illuminate/contracts`: Laravel framework integration
- Modern development tools: PHPStan, Laravel Pint, Pest PHP
