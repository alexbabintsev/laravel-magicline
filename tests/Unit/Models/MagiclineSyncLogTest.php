<?php

use AlexBabintsev\Magicline\Models\MagiclineLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a successful log entry', function () {
    $log = MagiclineLog::logSuccess(
        'customers',
        'create',
        '123',
        ['name' => 'John Doe'],
        ['id' => 123, 'created' => true]
    );

    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('create');
    expect($log->resource_id)->toBe('123');
    expect($log->status)->toBe(MagiclineLog::STATUS_SUCCESS);
    expect($log->request_data)->toBe(['name' => 'John Doe']);
    expect($log->response_data)->toBe(['id' => 123, 'created' => true]);
    expect($log->isSuccess())->toBeTrue();
});

it('can create an error log entry', function () {
    $log = MagiclineLog::logError(
        'customers',
        'create',
        'Validation failed',
        '123',
        ['name' => ''],
        ['errors' => ['name is required']]
    );

    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('create');
    expect($log->resource_id)->toBe('123');
    expect($log->status)->toBe(MagiclineLog::STATUS_FAILED);
    expect($log->error_message)->toBe('Validation failed');
    expect($log->isFailed())->toBeTrue();
});

it('can create a pending log entry', function () {
    $log = MagiclineLog::logPending(
        'customers',
        'update',
        '456',
        ['status' => 'processing']
    );

    expect($log->resource_type)->toBe('customers');
    expect($log->action)->toBe('update');
    expect($log->resource_id)->toBe('456');
    expect($log->status)->toBe(MagiclineLog::STATUS_PENDING);
    expect($log->isPending())->toBeTrue();
});

it('can update log with response data', function () {
    $log = MagiclineLog::logPending('customers', 'create');

    $log->updateWithResponse(['id' => 789, 'created' => true], true);

    expect($log->status)->toBe(MagiclineLog::STATUS_SUCCESS);
    expect($log->response_data)->toBe(['id' => 789, 'created' => true]);
});

it('can update log with error', function () {
    $log = MagiclineLog::logPending('customers', 'create');

    $log->updateWithError('Database error', ['error_code' => 500]);

    expect($log->status)->toBe(MagiclineLog::STATUS_FAILED);
    expect($log->error_message)->toBe('Database error');
    expect($log->response_data)->toBe(['error_code' => 500]);
});

it('can filter logs by resource type', function () {
    MagiclineLog::logSuccess('customers', 'create');
    MagiclineLog::logSuccess('appointments', 'create');
    MagiclineLog::logSuccess('customers', 'update');

    $customerLogs = MagiclineLog::forResource('customers')->get();

    expect($customerLogs)->toHaveCount(2);
    expect($customerLogs->pluck('resource_type')->unique()->first())->toBe('customers');
});

it('can filter logs by status', function () {
    MagiclineLog::logSuccess('customers', 'create');
    MagiclineLog::logError('customers', 'create', 'Error message');
    MagiclineLog::logSuccess('customers', 'update');

    $successfulLogs = MagiclineLog::successful()->get();
    $failedLogs = MagiclineLog::failed()->get();

    expect($successfulLogs)->toHaveCount(2);
    expect($failedLogs)->toHaveCount(1);
});

it('can filter logs by resource ID', function () {
    MagiclineLog::logSuccess('customers', 'create', '123');
    MagiclineLog::logSuccess('customers', 'create', '456');
    MagiclineLog::logSuccess('customers', 'update', '123');

    $logsForCustomer123 = MagiclineLog::forResourceId('123')->get();

    expect($logsForCustomer123)->toHaveCount(2);
    expect($logsForCustomer123->pluck('resource_id')->unique()->first())->toBe('123');
});

it('can get recent logs', function () {
    // Create old log
    $oldLog = MagiclineLog::logSuccess('customers', 'create');
    $oldLog->update(['synced_at' => now()->subDays(2)]);

    // Create recent log
    MagiclineLog::logSuccess('customers', 'update');

    $recentLogs = MagiclineLog::recent(24)->get();

    expect($recentLogs)->toHaveCount(1);
    expect($recentLogs->first()->action)->toBe('update');
});

it('can get statistics', function () {
    MagiclineLog::logSuccess('customers', 'create');
    MagiclineLog::logSuccess('customers', 'update');
    MagiclineLog::logError('customers', 'create', 'Error');
    MagiclineLog::logPending('customers', 'delete');

    $stats = MagiclineLog::getStats('customers');

    expect($stats['total'])->toBe(4);
    expect($stats['successful'])->toBe(2);
    expect($stats['failed'])->toBe(1);
    expect($stats['pending'])->toBe(1);
    expect($stats['success_rate'])->toBe(50.0);
});

it('can get overall statistics', function () {
    MagiclineLog::logSuccess('customers', 'create');
    MagiclineLog::logSuccess('appointments', 'update');
    MagiclineLog::logError('customers', 'create', 'Error');

    $stats = MagiclineLog::getStats();

    expect($stats['total'])->toBe(3);
    expect($stats['successful'])->toBe(2);
    expect($stats['failed'])->toBe(1);
    expect($stats['success_rate'])->toBe(66.67);
});

it('uses configurable table name', function () {
    $log = new MagiclineLog;

    // Should use default table name from config
    expect($log->getTable())->toBe('magicline_logs');

    // Test with different config value
    config(['magicline.logging.database.table' => 'custom_sync_logs']);

    $customLog = new MagiclineLog;
    expect($customLog->getTable())->toBe('custom_sync_logs');
});
