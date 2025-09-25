<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Magicline Main API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the main Magicline API integration. Set your API key
    | and base URL for the Magicline service.
    |
    */

    'api_key' => env('MAGICLINE_API_KEY'),

    'base_url' => env('MAGICLINE_BASE_URL', 'https://open-api-demo.open-api.magicline.com'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the HTTP client settings for API requests.
    |
    */

    'timeout' => env('MAGICLINE_TIMEOUT', 30),

    'retry' => [
        'times' => env('MAGICLINE_RETRY_TIMES', 3),
        'sleep' => env('MAGICLINE_RETRY_SLEEP', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | Default settings for paginated API responses.
    |
    */

    'pagination' => [
        'default_slice_size' => 50,
        'max_slice_size' => 100,
        'min_slice_size' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of API requests and responses for debugging.
    |
    */

    'logging' => [
        'enabled' => env('MAGICLINE_LOGGING_ENABLED', false),
        'level' => env('MAGICLINE_LOGGING_LEVEL', 'debug'),
        'database' => [
            'enabled' => env('MAGICLINE_DATABASE_LOGGING_ENABLED', false),
            // Name of the database table for storing API operation logs
            // Used by both the migration and the MagiclineLog model
            'table' => env('MAGICLINE_DATABASE_LOGGING_TABLE', 'magicline_logs'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Connect API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Magicline Connect API - public API for websites
    | and customer-facing integrations. No API key required.
    |
    */

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
            'database' => [
                'enabled' => env('MAGICLINE_CONNECT_DATABASE_LOGGING_ENABLED', false),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for incoming webhook requests from Magicline.
    | Webhooks require X-API-KEY authentication.
    |
    */

    'webhooks' => [
        'api_key' => env('MAGICLINE_WEBHOOK_API_KEY'),
        'endpoint' => env('MAGICLINE_WEBHOOK_ENDPOINT', '/magicline/webhook'),
        'logging' => [
            'enabled' => env('MAGICLINE_WEBHOOK_LOGGING_ENABLED', true),
            'level' => env('MAGICLINE_WEBHOOK_LOGGING_LEVEL', 'info'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Device API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Magicline Device API - for device integrations
    | like card readers, vending machines, and time devices. Uses Bearer token
    | authentication with a separate base URL.
    |
    */

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
            'database' => [
                'enabled' => env('MAGICLINE_DEVICE_DATABASE_LOGGING_ENABLED', false),
            ],
        ],
    ],
];
