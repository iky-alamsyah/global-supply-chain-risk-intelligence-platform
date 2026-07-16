<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
'country_api' => [
    'base_url' => env('COUNTRY_API_BASE_URL'),
    'api_key'  => env('COUNTRY_API_KEY'),
],

'world_bank' => [
    'base_url' => env(
        'WORLD_BANK_BASE_URL',
        'https://api.worldbank.org/v2'
    ),
],

'open_meteo' => [
    'base_url' => env(
        'OPEN_METEO_BASE_URL',
        'https://api.open-meteo.com/v1'
    ),
],

'exchange_rate' => [
    'base_url' => env(
        'EXCHANGE_RATE_BASE_URL',
        'https://open.er-api.com/v6'
    ),
],
'newsdata' => [

    'base_url' => env(
        'NEWSDATA_BASE_URL',
        'https://newsdata.io/api/1'
    ),

    'api_key' => env('NEWSDATA_API_KEY'),

],
];
