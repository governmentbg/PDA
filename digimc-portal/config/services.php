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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'recaptcha' => [
        'site_key'    => env('RECAPTCHA_SITE_KEY', 'insert site key here'),
        'secret_key'  => env('RECAPTCHA_SECRET_KEY', 'insert site secret key here'),
        'enabled' => env('RECAPTCHA_ENABLED', true),
    ],

    'iiif' => [
        'base_url' => env('IIIF_SERVER_URL', 'http://93.123.103.229:8182/iiif/2/'),
    ],

    'egov' => [
        'create_request_url' => env('EGOV_CREATE_REQUEST_URL', 'https://pay-test.egov.bg:44310/api/v1/eService/paymentJsonExtended'),
        'status_url' => env('EGOV_STATUS_URL', 'https://pay-test.egov.bg:44310/api/v1/eService/paymentsStatus'),
        'suspend_request_url' => env('EGOV_SUSPEND_REQUEST_URL', 'https://pay-test.egov.bg:44310/api/v1/eService/suspendRequest'),
        'payment_url' => env('EGOV_PAYMENT_URL', 'https://pay-test.egov.bg/Home/AccessByCode'),
        'client' => env('EGOV_CLIENT'),
        'secret' => env('EGOV_SECRET'),
        'applicant_uin_type' => env('EGOV_APPLICANT_UIN_TYPE', '1'),
        'applicant_uin'      => env('EGOV_APPLICANT_UIN'),
        'base_url' => env('EGOV_BASE_URL', 'https://pay-test.egov.bg:44310/'),
    ],

    'cdn_base_url' => env('CDN_BASE_URL'),

];
