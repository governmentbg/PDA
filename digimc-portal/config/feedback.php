<?php

return[
    'route' => [
        'path' => '/feedback/store',
        'name' => 'feedback.store',
        'throttle' => [
            'per_minute' => env('FEEDBACK_RATE_PER_MIN', 5),
            'per_hour'   => env('FEEDBACK_RATE_PER_HOUR', 50),
        ],
    ],

    // Categories shown in the form
    'categories' => [
        'Problem',
        'Suggestion',
        'Praise',
        'Question'
    ],

    // Copy used in UI/messages
    'messages' => [
        'success'        => 'Thank you, your message was sent successfully.',
        'generic_error'  => 'Something went wrong. Please try again.',
        'captcha_failed' => 'Captcha validation failed.',
    ],

    // Validation limits
    'validation' => [
        'subject_max'     => 150,
        'description_max' => 5000,
        'name_max'        => 120,
        'email_max'       => 255,
    ],

    // If not set in DB, fall back to .env values.
    'mail' => [
        'enable'            => true,
        'use_settings'      => true,
        'from_setting_key'  => env('from_contact_email', 'from@email.com'),
        'to_setting_key'    => env('to_contact_email', 'to@email.com'),

        'fallback_from'     => env('MAIL_FROM_ADDRESS', 'from@email.com'),
        'fallback_from_name'=> env('MAIL_FROM_NAME', 'Portal'),
        'fallback_to'       => env('FEEDBACK_FALLBACK_TO', 'to@email.com'),
    ],

    'email' => [
        'subject' => 'Portal feedback: :subject',
        'title' => 'New feedback received',
        'fields' => [
            'subject' => 'Subject',
            'category' => 'Category',
            'description' => 'Description',
            'email' => 'Contact email',
            'name' => 'Name',
        ],
    ],

    // reCAPTCHA (v2 checkbox)
    'recaptcha' => [
        'version'   => env('RECAPTCHA_VERSION', 'v2_checkbox'),
        'site'      => env('RECAPTCHA_SITE_KEY', 'insert site key here'),
        'secret'    => env('RECAPTCHA_SECRET_KEY', 'insert site secret key here'),
        'bypass_in_testing' => env('FEEDBACK_RECAPTCHA_BYPASS_IN_TESTING', true),
    ],

    // UI defaults for the bubble (used by the Blade component)
    'ui' => [
        'bubble_text' => 'Feedback',
        'position'    => 'bottom-right',
    ],
];
