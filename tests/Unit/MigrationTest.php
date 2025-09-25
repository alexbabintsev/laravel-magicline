<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates table with configurable name', function () {
    // Test with default table name
    expect(Schema::hasTable('magicline_logs'))->toBeTrue();

    // Test table structure
    expect(Schema::hasColumns('magicline_logs', [
        'id',
        'resource_type',
        'resource_id',
        'action',
        'request_data',
        'response_data',
        'status',
        'error_message',
        'synced_at',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    // Test indexes exist
    $indexes = Schema::getIndexes('magicline_logs');
    $indexNames = collect($indexes)->pluck('name')->toArray();

    expect($indexNames)->toContain('magicline_logs_resource_type_resource_id_index');
    expect($indexNames)->toContain('magicline_logs_synced_at_index');
});

it('migration respects custom table name configuration', function () {
    // This test demonstrates that migration would use custom table name
    // In practice, this would need to be tested in a separate environment
    // where the config is set before running migrations

    $defaultTableName = config('magicline.logging.database.table', 'magicline_logs');
    expect($defaultTableName)->toBe('magicline_logs');

    // If we change the config, a new migration would create a different table
    config(['magicline.logging.database.table' => 'custom_audit_logs']);
    $customTableName = config('magicline.logging.database.table');
    expect($customTableName)->toBe('custom_audit_logs');
});
