<?php

use AlexBabintsev\Magicline\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

// Clean up Mockery after each test
afterEach(function () {
    if (class_exists('Mockery')) {
        Mockery::close();
    }
});

// Handle CI environment better
beforeEach(function () {
    // Ensure clean state for each test in CI
    if (getenv('CI') === 'true') {
        gc_collect_cycles();
    }
});
