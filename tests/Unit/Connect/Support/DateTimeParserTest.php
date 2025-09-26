<?php

use AlexBabintsev\Magicline\Connect\Support\DateTimeParser;
use Carbon\Carbon;

test('can parse old UTC format', function () {
    $dateTime = '2021-08-23T09:00:00.000Z';

    $parsed = DateTimeParser::parse($dateTime);

    expect($parsed)->toBeInstanceOf(Carbon::class);
    expect($parsed->getTimezone()->getName())->toBe('UTC');
    expect($parsed->format('Y-m-d H:i:s'))->toBe('2021-08-23 09:00:00');
});

test('can parse new timezone format', function () {
    $dateTime = '2021-08-23T11:00:00.000+02:00[Europe/Berlin]';

    $parsed = DateTimeParser::parse($dateTime);

    expect($parsed)->toBeInstanceOf(Carbon::class);
    expect($parsed->format('Y-m-d H:i:s'))->toBe('2021-08-23 11:00:00');
});

test('can format datetime with timezone', function () {
    $carbon = Carbon::create(2021, 8, 23, 11, 0, 0, 'Europe/Berlin');

    $formatted = DateTimeParser::format($carbon, 'Europe/Berlin');

    expect($formatted)->toContain('[Europe/Berlin]');
    expect($formatted)->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}[+-]\d{2}:\d{2}\[Europe\/Berlin\]$/');
});

test('can format for booking', function () {
    $carbon = Carbon::create(2021, 8, 23, 11, 0, 0, 'Europe/Berlin');

    $formatted = DateTimeParser::formatForBooking($carbon, 'Europe/Berlin');

    expect($formatted)->toContain('[Europe/Berlin]');
});

test('can detect old format', function () {
    $oldFormat = '2021-08-23T09:00:00.000Z';
    $newFormat = '2021-08-23T11:00:00.000+02:00[Europe/Berlin]';

    expect(DateTimeParser::isOldFormat($oldFormat))->toBeTrue();
    expect(DateTimeParser::isOldFormat($newFormat))->toBeFalse();
});

test('can detect new format', function () {
    $oldFormat = '2021-08-23T09:00:00.000Z';
    $newFormat = '2021-08-23T11:00:00.000+02:00[Europe/Berlin]';

    expect(DateTimeParser::isNewFormat($oldFormat))->toBeFalse();
    expect(DateTimeParser::isNewFormat($newFormat))->toBeTrue();
});

test('can extract timezone from new format', function () {
    $dateTime = '2021-08-23T11:00:00.000+02:00[Europe/Berlin]';

    $timezone = DateTimeParser::extractTimezone($dateTime);

    expect($timezone)->toBe('Europe/Berlin');
});

test('throws exception for empty datetime', function () {
    expect(fn () => DateTimeParser::parse(''))->toThrow(InvalidArgumentException::class);
});
