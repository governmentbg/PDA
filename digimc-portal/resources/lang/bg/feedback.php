<?php

return [
    'bubble' => 'Обратна връзка',

    'modal' => [
        'title'   => 'Изпрати обратна връзка',
        'labels'  => [
            'subject' => 'Тема/Заглавие',
            'category'=> 'Категория',
            'description' => 'Описание',
            'email'   => 'Имейл за контакт',
            'name'    => 'Име',
        ],
        'buttons' => [
            'close' => 'Затвори',
            'send'  => 'Изпрати',
        ],
        'success'       => 'Благодарим! Съобщението беше изпратено успешно.',
        'generic_error' => 'Възникна грешка. Опитайте отново.',
        'fix'           => 'Моля, коригирайте маркираните полета.',
    ],

    'categories' => [
        'Problem'    => 'Проблем',
        'Suggestion' => 'Предложение',
        'Praise'     => 'Похвала',
        'Question'   => 'Въпрос',
    ],

    'captcha_failed' => 'Грешка при потвърждаване на reCaptcha.',

    'email' => [
        'subject' => ':subject',
        'title' => 'Получена е нова обратна връзка',
        'fields' => [
            'subject' => 'Тема/Заглавие',
            'category' => 'Категория',
            'description' => 'Описание',
            'email' => 'Имейл за контакт',
            'name' => 'Име',
        ],
    ],

    'validation' => [
        'required'    => ':attribute е задължително.',
        'email'       => ':attribute трябва да е валиден имейл.',
        'string'      => ':attribute трябва да е текст.',
        'max.string'  => ':attribute не може да е по-дълго от :max символа.',
        'in'          => 'Изберете валидна стойност за :attribute.',
    ],

    'too_many_attempts' => 'Твърде много опити. Моля, опитайте отново след малко.',

];
