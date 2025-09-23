<?php

use AlexBabintsev\Magicline\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

// Clean up Mockery after each test
afterEach(function () {
    Mockery::close();
});
