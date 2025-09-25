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
];
