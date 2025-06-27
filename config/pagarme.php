<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pagarme API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Pagarme payment
    | gateway integration. All options can be overridden via environment
    | variables.
    |
    */

    'api_key' => env('PAGARME_API_KEY', 'ak_test_*'),

    'base_url' => env('PAGARME_BASE_URL', 'https://api.pagar.me/core'),

    'api_version' => env('PAGARME_API_VERSION', 'v5'),

    /*
    |--------------------------------------------------------------------------
    | Request Configuration
    |--------------------------------------------------------------------------
    */

    'timeout' => env('PAGARME_TIMEOUT', 30),

    'connect_timeout' => env('PAGARME_CONNECT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */

    'log_requests' => env('PAGARME_LOG_REQUESTS', false),

    'log_channel' => env('PAGARME_LOG_CHANNEL', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Environment Configuration
    |--------------------------------------------------------------------------
    */

    'sandbox' => env('PAGARME_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhook_secret' => env('PAGARME_WEBHOOK_SECRET'),

    'webhook_tolerance' => env('PAGARME_WEBHOOK_TOLERANCE', 300), // 5 minutes

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache_prefix' => env('PAGARME_CACHE_PREFIX', 'pagarme'),

    'cache_ttl' => env('PAGARME_CACHE_TTL', 3600), // 1 hour
];
