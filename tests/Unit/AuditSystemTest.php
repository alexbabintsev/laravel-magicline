<?php

use AlexBabintsev\Magicline\Models\MagiclineLog;
use AlexBabintsev\Magicline\Traits\LogsApiOperations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

// Simple test class to test logging functionality
class SimpleTestClass
{
    use LogsApiOperations;

    public function testSuccessfulOperation(string $resourceType, string $action)
    {
        return $this->executeWithLogging(
            $resourceType,
            $action,
            fn() => ['success' => true, 'id' => 123]
        );
    }

    public function testFailedOperation(string $resourceType, string $action)
    {
        return $this->executeWithLogging(
            $resourceType,
            $action,
            fn() => throw new \Exception('Test error')
        );
    }

    protected function isDatabaseLoggingEnabled(): bool
    {
        return Config::get('magicline.logging.database.enabled', false);
    }
}

beforeEach(function () {
    $this->testClass = new SimpleTestClass();
});

it('can log successful operations when database logging is enabled', function () {
    Config::set('magicline.logging.database.enabled', true);

    $result = $this->testClass->testSuccessfulOperation('customers', 'create');

    expect($result)->toBe(['success' => true, 'id' => 123]);

    $log = MagiclineLog::first();
    expect($log)->not->toBeNull();
    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('create');
    expect($log->status)->toBe(MagiclineLog::STATUS_SUCCESS);
    expect($log->response_data)->toBe(['success' => true, 'id' => 123]);
});

it('can log failed operations when database logging is enabled', function () {
    Config::set('magicline.logging.database.enabled', true);

    expect(fn() => $this->testClass->testFailedOperation('customers', 'delete'))
        ->toThrow(\Exception::class, 'Test error');

    $log = MagiclineLog::first();
    expect($log)->not->toBeNull();
    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('delete');
    expect($log->status)->toBe(MagiclineLog::STATUS_FAILED);
    expect($log->error_message)->toBe('Test error');
});

it('does not log operations when database logging is disabled', function () {
    Config::set('magicline.logging.database.enabled', false);

    $result = $this->testClass->testSuccessfulOperation('customers', 'create');

    expect($result)->toBe(['success' => true, 'id' => 123]);
    expect(MagiclineLog::count())->toBe(0);
});

it('can track API operations for audit trail', function () {
    Config::set('magicline.logging.database.enabled', true);

    // Simulate multiple API operations
    $this->testClass->testSuccessfulOperation('customers', 'create');
    $this->testClass->testSuccessfulOperation('customers', 'update');
    $this->testClass->testSuccessfulOperation('appointments', 'create');

    try {
        $this->testClass->testFailedOperation('customers', 'delete');
    } catch (\Exception $e) {
        // Expected
    }

    // Check audit trail
    $logs = MagiclineLog::orderBy('id')->get();
    expect($logs)->toHaveCount(4);

    // Check customer operations
    $customerLogs = MagiclineLog::forResource('customers')->get();
    expect($customerLogs)->toHaveCount(3);

    // Check success rate
    $stats = MagiclineLog::getStats('customers');
    expect($stats['total'])->toBe(3);
    expect($stats['successful'])->toBe(2);
    expect($stats['failed'])->toBe(1);
    expect($stats['success_rate'])->toBe(66.67);
});

it('can generate audit reports by time period', function () {
    Config::set('magicline.logging.database.enabled', true);

    // Create old operations
    $oldLog = MagiclineLog::logSuccess('customers', 'create', '1');
    $oldLog->update(['synced_at' => now()->subDays(3)]);

    $oldFailedLog = MagiclineLog::logError('customers', 'update', 'Old error', '1');
    $oldFailedLog->update(['synced_at' => now()->subDays(3)]);

    // Create recent operations
    MagiclineLog::logSuccess('customers', 'create', '2');
    MagiclineLog::logSuccess('customers', 'update', '2');

    // Test recent logs (last 24 hours)
    $recentLogs = MagiclineLog::recent(24)->get();
    expect($recentLogs)->toHaveCount(2);

    // Test date range filtering
    $oldLogs = MagiclineLog::inDateRange(
        now()->subDays(5),
        now()->subDays(2)
    )->get();
    expect($oldLogs)->toHaveCount(2);

    // Test overall stats for different time periods
    $recentStats = MagiclineLog::getStats('customers', 1); // last 1 day
    expect($recentStats['total'])->toBe(2);
    expect($recentStats['success_rate'])->toBe(100.0);

    $allTimeStats = MagiclineLog::getStats('customers', 7); // last 7 days
    expect($allTimeStats['total'])->toBe(4);
    expect($allTimeStats['success_rate'])->toBe(75.0); // 3 success out of 4
});

it('provides comprehensive audit logging capabilities', function () {
    Config::set('magicline.logging.database.enabled', true);

    // Test various resource types and actions
    $operations = [
        ['customers', 'create', true],
        ['customers', 'update', true],
        ['customers', 'delete', false], // will fail
        ['appointments', 'create', true],
        ['appointments', 'cancel', true],
        ['classes', 'book', true],
        ['classes', 'cancel', false], // will fail
    ];

    foreach ($operations as [$resource, $action, $shouldSucceed]) {
        try {
            if ($shouldSucceed) {
                $this->testClass->testSuccessfulOperation($resource, $action);
            } else {
                $this->testClass->testFailedOperation($resource, $action);
            }
        } catch (\Exception $e) {
            // Expected for failed operations
        }
    }

    // Verify comprehensive logging
    expect(MagiclineLog::count())->toBe(7);

    // Test filtering by different criteria
    expect(MagiclineLog::forResource('customers')->count())->toBe(3);
    expect(MagiclineLog::forResource('appointments')->count())->toBe(2);
    expect(MagiclineLog::forResource('classes')->count())->toBe(2);

    expect(MagiclineLog::forAction('create')->count())->toBe(2);
    expect(MagiclineLog::forAction('update')->count())->toBe(1);
    expect(MagiclineLog::forAction('delete')->count())->toBe(1);
    expect(MagiclineLog::forAction('cancel')->count())->toBe(2);
    expect(MagiclineLog::forAction('book')->count())->toBe(1);

    expect(MagiclineLog::successful()->count())->toBe(5);
    expect(MagiclineLog::failed()->count())->toBe(2);

    // Test overall system health
    $overallStats = MagiclineLog::getStats();
    expect($overallStats['total'])->toBe(7);
    expect($overallStats['successful'])->toBe(5);
    expect($overallStats['failed'])->toBe(2);
    expect($overallStats['success_rate'])->toBe(71.43); // 5/7 * 100
});
