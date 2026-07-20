<?php

return [
    'bubble' => 'Feedback',

    'modal' => [
        'title' => 'Send Feedback',
        'labels' => [
            'subject' => 'Subject/Title',
            'category' => 'Category',
            'description' => 'Description',
            'email' => 'Contact email',
            'name' => 'Name',
        ],
        'buttons' => [
            'close' => 'Close',
            'send' => 'Send',
        ],
        'success' => 'Thank you, your message was sent successfully.',
        'generic_error' => 'Something went wrong. Please try again.',
        'fix' => 'Please fix the highlighted fields.',
    ],

    'categories' => [
        'Problem' => 'Problem',
        'Suggestion' => 'Suggestion',
        'Praise' => 'Praise',
        'Question' => 'Question',
    ],

    'captcha_failed' => 'Captcha validation failed.',

    'email' => [
        'subject' => ':subject',
        'title' => 'New feedback received',
        'fields' => [
            'subject' => 'Subject',
            'category' => 'Category',
            'description' => 'Description',
            'email' => 'Contact email',
            'name' => 'Name',
        ],
    ],

    'validation' => [
        'required' => ':attribute is required.',
        'email' => ':attribute must be a valid email address.',
        'string' => ':attribute must be text.',
        'max.string' => ':attribute may not be greater than :max characters.',
        'in' => 'Select a valid :attribute.',
    ],

    'too_many_attempts' => 'Too many attempts. Please try again later.',

];
