<?php

namespace AlexBabintsev\Magicline\Connect\Support;

use Carbon\Carbon;
use InvalidArgumentException;

class DateTimeParser
{
    /**
     * Parse datetime string from Connect API (supports both old and new formats)
     *
     * Old format (UTC): "2021-08-23T09:00:00.000Z"
     * New format (local): "2021-08-23T11:00:00.000+02:00[Europe/Berlin]"
     */
    public static function parse(string $dateTime): Carbon
    {
        if (empty($dateTime)) {
            throw new InvalidArgumentException('DateTime string cannot be empty');
        }

        // New format with timezone in brackets
        if (str_ends_with($dateTime, ']')) {
            // Remove timezone name from brackets: [Europe/Berlin]
            $withoutTimezone = preg_replace('/\[.*\]$/', '', $dateTime);

            if (! $withoutTimezone) {
                throw new InvalidArgumentException('Invalid datetime format: '.$dateTime);
            }

            try {
                // Parse ISO 8601 with timezone offset
                return Carbon::createFromFormat('Y-m-d\TH:i:s.vP', $withoutTimezone)
                    ?: Carbon::createFromFormat('Y-m-d\TH:i:sP', $withoutTimezone);
            } catch (\Exception $e) {
                throw new InvalidArgumentException('Failed to parse new datetime format: '.$dateTime, 0, $e);
            }
        }

        // Old format (UTC)
        if (str_ends_with($dateTime, 'Z')) {
            try {
                return Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $dateTime, 'UTC')
                    ?: Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $dateTime, 'UTC');
            } catch (\Exception $e) {
                throw new InvalidArgumentException('Failed to parse old datetime format: '.$dateTime, 0, $e);
            }
        }

        // Try to parse as standard ISO 8601
        try {
            return Carbon::parse($dateTime);
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Unrecognized datetime format: '.$dateTime, 0, $e);
        }
    }

    /**
     * Format datetime for API requests (uses new format with timezone)
     */
    public static function format(Carbon $dateTime, ?string $timezone = null): string
    {
        if ($timezone) {
            $dateTime = $dateTime->setTimezone($timezone);

            return $dateTime->format('Y-m-d\TH:i:s.vP').'['.$timezone.']';
        }

        // If no timezone specified, use the datetime's current timezone
        $tz = $dateTime->getTimezone()->getName();

        return $dateTime->format('Y-m-d\TH:i:s.vP').'['.$tz.']';
    }

    /**
     * Format datetime for booking (backwards compatible)
     */
    public static function formatForBooking(Carbon $dateTime, ?string $timezone = null): string
    {
        // Always use new format for bookings as recommended by Magicline
        return self::format($dateTime, $timezone);
    }

    /**
     * Convert datetime to specific timezone
     */
    public static function convertToTimezone(Carbon $dateTime, string $timezone): Carbon
    {
        return $dateTime->setTimezone($timezone);
    }

    /**
     * Parse multiple datetime strings
     */
    public static function parseMultiple(array $dateTimes): array
    {
        return array_map([self::class, 'parse'], $dateTimes);
    }

    /**
     * Check if datetime string is in new format
     */
    public static function isNewFormat(string $dateTime): bool
    {
        return str_ends_with($dateTime, ']');
    }

    /**
     * Check if datetime string is in old format
     */
    public static function isOldFormat(string $dateTime): bool
    {
        return str_ends_with($dateTime, 'Z');
    }

    /**
     * Extract timezone from new format datetime string
     */
    public static function extractTimezone(string $dateTime): ?string
    {
        if (! self::isNewFormat($dateTime)) {
            return null;
        }

        preg_match('/\[([^\]]+)\]$/', $dateTime, $matches);

        return $matches[1] ?? null;
    }
}
