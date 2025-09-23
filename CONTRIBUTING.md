# Contributing to Laravel Magicline

Thank you for considering contributing to Laravel Magicline! We welcome contributions from the community to help improve this package.

## Development Environment Setup

1. **Fork and clone the repository**:
   ```bash
   git clone https://github.com/alexbabintsev/laravel-magicline.git
   cd laravel-magicline
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Set up environment**:
   ```bash
   cp .env.example .env
   # Add your Magicline API credentials for testing
   ```

## Development Guidelines

### Code Style

This project follows the Laravel coding standards. We use **Laravel Pint** for code formatting:

```bash
composer format
```

### Static Analysis

We use **PHPStan** for static analysis with Larastan for Laravel-specific checks:

```bash
composer analyse
```

### Testing

All contributions must include appropriate tests. We use **Pest PHP** as our testing framework:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test
vendor/bin/pest tests/Unit/Resources/CustomersTest.php
```

**Test Requirements:**
- Unit tests for new resources/methods
- Integration tests for complex workflows
- Maintain or improve test coverage
- Use Mockery for mocking dependencies

### Architecture Guidelines

**Resource Classes:**
- Extend `BaseResource`
- Use type hints for parameters and return types
- Follow RESTful naming conventions
- Include proper docblocks

**DTOs (Data Transfer Objects):**
- Extend `BaseDto`
- Use readonly properties where possible
- Include `from()` and `collection()` static methods
- Provide `toArray()` method

**Exception Handling:**
- Use specific exception types for different error scenarios
- Include HTTP status codes and error details
- Extend appropriate base exception classes

## Contributing Process

### 1. Before You Start

- Check existing issues and PRs to avoid duplicates
- Create an issue to discuss major changes
- Ensure your idea aligns with the package goals

### 2. Making Changes

1. **Create a feature branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**:
   - Write clean, readable code
   - Follow existing patterns and conventions
   - Add/update tests for your changes
   - Update documentation if needed

3. **Test your changes**:
   ```bash
   composer test
   composer analyse
   composer format
   ```

### 3. Submitting Changes

1. **Commit your changes**:
   ```bash
   git add .
   git commit -m "Add: descriptive commit message"
   ```

2. **Push to your fork**:
   ```bash
   git push origin feature/your-feature-name
   ```

3. **Create a Pull Request**:
   - Use a clear, descriptive title
   - Explain what your changes do
   - Reference any related issues
   - Include screenshots for UI changes

## Types of Contributions

### Bug Fixes
- Fix issues in existing functionality
- Include tests that reproduce the bug
- Update documentation if the bug affected documented behavior

### New Features
- Add new Magicline API endpoints
- Enhance existing functionality
- Always include comprehensive tests
- Update README with usage examples

### Documentation
- Improve README examples
- Add inline code documentation
- Fix typos or unclear explanations
- Add missing docblocks

### Performance Improvements
- Optimize existing code
- Reduce memory usage
- Improve query efficiency
- Include benchmarks when relevant

## Pull Request Guidelines

### Requirements
- [ ] Tests pass (`composer test`)
- [ ] Code analysis passes (`composer analyse`)
- [ ] Code is formatted (`composer format`)
- [ ] Documentation updated if needed
- [ ] CHANGELOG.md updated for significant changes

### Review Process
1. Automated checks must pass
2. Code review by maintainers
3. Testing in different environments
4. Final approval and merge

## Coding Standards

### PHP Standards
- PHP 8.2+ features encouraged
- Use strict types: `declare(strict_types=1);`
- Type hint all parameters and return types
- Use readonly properties where applicable

### Laravel Standards
- Follow Laravel naming conventions
- Use Laravel's built-in features (validation, collections, etc.)
- Leverage service container and dependency injection
- Use facades appropriately

### Documentation Standards
- All public methods must have docblocks
- Include parameter and return type documentation
- Provide usage examples for complex methods
- Use clear, concise language

## Resource Development

When adding new Magicline API endpoints:

1. **Create the resource class**:
   ```php
   class YourResource extends BaseResource
   {
       public function someMethod(array $params = []): array
       {
           return $this->client->get('/v1/your-endpoint', $params);
       }
   }
   ```

2. **Add to the main Magicline class**:
   ```php
   public function yourResource(): YourResource
   {
       return new YourResource($this->client);
   }
   ```

3. **Update the facade docblock**:
   ```php
   /**
    * @method static YourResource yourResource()
    */
   ```

4. **Create comprehensive tests**:
   ```php
   test('your method works correctly', function () {
       // Test implementation
   });
   ```

## Issue Reporting

When reporting bugs:
- Use the issue template
- Include PHP and Laravel versions
- Provide minimal reproduction example
- Include error messages and stack traces
- Test with the latest version first

## Security

If you discover a security vulnerability:
- **DO NOT** create a public issue
- Email the maintainers directly
- Provide detailed information about the vulnerability
- Allow reasonable time for patching before disclosure

## Code of Conduct

- Be respectful and inclusive
- Welcome newcomers and help them learn
- Focus on constructive feedback
- Respect different viewpoints and experiences

## Questions?

- Check existing documentation and issues first
- Create a discussion for general questions
- Use issues for specific bugs or feature requests
- Be patient and respectful when asking for help

Thank you for contributing to Laravel Magicline! 🚀
