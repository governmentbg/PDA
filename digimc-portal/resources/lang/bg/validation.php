<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Валидиращи съобщения
    |--------------------------------------------------------------------------
    */

    'accepted'             => ':attribute трябва да бъде прието.',
    'accepted_if'          => ':attribute трябва да бъде прието, когато :other е :value.',
    'active_url'           => ':attribute не е валиден URL адрес.',
    'after'                => ':attribute трябва да е дата след :date.',
    'after_or_equal'       => ':attribute трябва да е дата след или равна на :date.',
    'alpha'                => ':attribute може да съдържа само букви.',
    'alpha_dash'           => ':attribute може да съдържа само букви, цифри, тирета и долни черти.',
    'alpha_num'            => ':attribute може да съдържа само букви и цифри.',
    'array'                => ':attribute трябва да е масив.',
    'ascii'                => ':attribute трябва да съдържа само еднобайтови ASCII знаци и символи.',
    'before'               => ':attribute трябва да е дата преди :date.',
    'before_or_equal'      => ':attribute трябва да е дата преди или равна на :date.',
    'between'              => [
        'array'   => ':attribute трябва да има между :min и :max елемента.',
        'file'    => ':attribute трябва да е между :min и :max килобайта.',
        'numeric' => ':attribute трябва да е между :min и :max.',
        'string'  => ':attribute трябва да е между :min и :max символа.',
    ],
    'boolean'              => 'Полето :attribute трябва да е истина или лъжа.',
    'confirmed'            => 'Потвърждението на :attribute не съвпада.',
    'current_password'     => 'Текуща парола.',
    'date'                 => ':attribute не е валидна дата.',
    'date_equals'          => ':attribute трябва да е дата, равна на :date.',
    'date_format'          => ':attribute не съответства на формата :format.',
    'decimal'              => ':attribute трябва да има :decimal десетични знака.',
    'declined'             => ':attribute трябва да бъде отказано.',
    'declined_if'          => ':attribute трябва да бъде отказано, когато :other е :value.',
    'different'            => ':attribute и :other трябва да са различни.',
    'digits'               => ':attribute трябва да е с :digits цифри.',
    'digits_between'       => ':attribute трябва да е между :min и :max цифри.',
    'dimensions'           => ':attribute е с невалидни размери на изображението.',
    'distinct'             => 'Полето :attribute съдържа дублираща стойност.',
    'doesnt_end_with'      => ':attribute не може да завършва с някое от следните: :values.',
    'doesnt_start_with'    => ':attribute не може да започва с някое от следните: :values.',
    'email'                => ':attribute трябва да е валиден имейл адрес.',
    'ends_with'            => ':attribute трябва да завършва с едно от следните: :values.',
    'enum'                 => 'Избраният :attribute е невалиден.',
    'exists'               => 'Избраният :attribute е невалиден.',
    'file'                 => ':attribute трябва да е файл.',
    'filled'               => 'Полето :attribute трябва да има стойност.',
    'gt'                   => [
        'array'   => ':attribute трябва да има повече от :value елемента.',
        'file'    => ':attribute трябва да е по-голям от :value килобайта.',
        'numeric' => ':attribute трябва да е по-голям от :value.',
        'string'  => ':attribute трябва да е по-дълъг от :value символа.',
    ],
    'gte'                  => [
        'array'   => ':attribute трябва да има поне :value елемента.',
        'file'    => ':attribute трябва да е по-голям или равен на :value килобайта.',
        'numeric' => ':attribute трябва да е по-голям или равен на :value.',
        'string'  => ':attribute трябва да е по-дълъг или равен на :value символа.',
    ],
    'hex_color'            => ':attribute трябва да е валиден шестнадесетичен цвят.',
    'image'                => ':attribute трябва да е изображение.',
    'in'                   => 'Избраният :attribute е невалиден.',
    'in_array'             => 'Полето :attribute не съществува в :other.',
    'integer'              => ':attribute трябва да е цяло число.',
    'ip'                   => ':attribute трябва да е валиден IP адрес.',
    'ipv4'                 => ':attribute трябва да е валиден IPv4 адрес.',
    'ipv6'                 => ':attribute трябва да е валиден IPv6 адрес.',
    'json'                 => ':attribute трябва да е валиден JSON низ.',
    'lowercase'            => ':attribute трябва да е с малки букви.',
    'lt'                   => [
        'array'   => ':attribute трябва да има по-малко от :value елемента.',
        'file'    => ':attribute трябва да е по-малък от :value килобайта.',
        'numeric' => ':attribute трябва да е по-малък от :value.',
        'string'  => ':attribute трябва да е по-кратък от :value символа.',
    ],
    'lte'                  => [
        'array'   => ':attribute не трябва да има повече от :value елемента.',
        'file'    => ':attribute трябва да е по-малък или равен на :value килобайта.',
        'numeric' => ':attribute трябва да е по-малък или равен на :value.',
        'string'  => ':attribute трябва да е по-кратък или равен на :value символа.',
    ],
    'mac_address'          => ':attribute трябва да е валиден MAC адрес.',
    'max'                  => [
        'array'   => ':attribute не може да има повече от :max елемента.',
        'file'    => ':attribute не може да е по-голям от :max килобайта.',
        'numeric' => ':attribute не може да е по-голям от :max.',
        'string'  => ':attribute не може да е по-дълъг от :max символа.',
    ],
    'max_digits'           => ':attribute не може да има повече от :max цифри.',
    'mimes'                => ':attribute трябва да е файл от тип: :values.',
    'mimetypes'            => ':attribute трябва да е файл от тип: :values.',
    'min'                  => [
        'array'   => ':attribute трябва да има поне :min елемента.',
        'file'    => ':attribute трябва да е поне :min килобайта.',
        'numeric' => ':attribute трябва да е поне :min.',
        'string'  => ':attribute трябва да е поне :min символа.',
    ],
    'min_digits'           => ':attribute трябва да има поне :min цифри.',
    'missing'              => 'Полето :attribute трябва да липсва.',
    'missing_if'           => 'Полето :attribute трябва да липсва, когато :other е :value.',
    'missing_unless'       => 'Полето :attribute трябва да липсва, освен ако :other е в :values.',
    'missing_with'         => 'Полето :attribute трябва да липсва, когато :values е налично.',
    'missing_with_all'     => 'Полето :attribute трябва да липсва, когато :values са налични.',
    'multiple_of'          => ':attribute трябва да е кратно на :value.',
    'not_in'               => 'Избраният :attribute е невалиден.',
    'not_regex'            => 'Форматът на :attribute е невалиден.',
    'numeric'              => ':attribute трябва да е число.',
    'password'             => [
        'letters'       => ':attribute трябва да съдържа поне една буква.',
        'mixed'         => ':attribute трябва да съдържа поне една главна и една малка буква.',
        'numbers'       => ':attribute трябва да съдържа поне една цифра.',
        'symbols'       => ':attribute трябва да съдържа поне един специален символ.',
        'uncompromised' => 'Даденият :attribute се среща в публичен теч на данни. Моля, изберете друга стойност.',
        'weak'          => 'слаба',
        'normal'        => 'нормална',
        'strong'        => 'силна',
        'match'         => 'Паролите не съвпадат.',
        'required'      => 'Моля, въведете парола.',
        'min_eight'     => 'Паролата трябва да е поне 8 символа.',
    ],
    'present'              => 'Полето :attribute трябва да е налично.',
    'prohibited'           => 'Полето :attribute е забранено.',
    'prohibited_if'        => 'Полето :attribute е забранено, когато :other е :value.',
    'prohibited_unless'    => 'Полето :attribute е забранено, освен ако :other е в :values.',
    'prohibits'            => 'Полето :attribute забранява :other да бъде налично.',
    'regex'                => 'Форматът на :attribute е невалиден.',
    'required'             => 'Полето :attribute е задължително.',
    'required_array_keys'  => 'Полето :attribute трябва да съдържа записи за: :values.',
    'required_if'          => 'Полето :attribute е задължително, когато :other е :value.',
    'required_if_accepted' => 'Полето :attribute е задължително, когато :other е прието.',
    'required_unless'      => 'Полето :attribute е задължително, освен ако :other не е в :values.',
    'required_with'        => 'Полето :attribute е задължително, когато :values е налично.',
    'required_with_all'    => 'Полето :attribute е задължително, когато :values са налични.',
    'required_without'     => 'Полето :attribute е задължително, когато :values не е налично.',
    'required_without_all' => 'Полето :attribute е задължително, когато нито една от :values не е налична.',
    'same'                 => ':attribute и :other трябва да съвпадат.',
    'size'                 => [
        'array'   => ':attribute трябва да съдържа :size елемента.',
        'file'    => ':attribute трябва да е :size килобайта.',
        'numeric' => ':attribute трябва да е :size.',
        'string'  => ':attribute трябва да е :size символа.',
    ],
    'starts_with'          => ':attribute трябва да започва с едно от следните: :values.',
    'string'               => ':attribute трябва да е низ.',
    'timezone'             => ':attribute трябва да е валидна часова зона.',
    'unique'               => ':attribute вече съществува.',
    'uploaded'             => 'Качването на :attribute не бе успешно.',
    'uppercase'            => ':attribute трябва да е с главни букви.',
    'url'                  => ':attribute трябва да е валиден URL адрес.',
    'ulid'                 => ':attribute трябва да е валиден ULID.',
    'uuid'                 => ':attribute трябва да е валиден UUID.',

    /*
    |--------------------------------------------------------------------------
    | Потребителски съобщения за конкретни атрибути/правила
    |--------------------------------------------------------------------------
    |
    */
    'wrong_password' => 'Грешна парола.',
    'login_again' => 'Влезте отново',

    'custom' => [
        // 'email' => [
        //     'required' => 'Моля, въведете имейл адрес.',
        // ],
        'email' => [
            'unique' => 'Този имейл вече е зает.',
        ],
        'image' => [
            'max' => 'Снимката не може да е по-голяма от :max килобайта.',
            'uploaded' => 'Качването на снимка не бе успешно.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Приятелски имена на атрибутите
    |--------------------------------------------------------------------------
    | Тези стойности заместват :attribute в съобщенията.
    */

    'attributes' => [
        'first_name'            => 'Име',
        'last_name'             => 'Фамилия',
        'wants_notifications'   => 'Известия',
        'subscribed_news'       => 'Абонамент за новини',
        'subscribed_weekly'     => 'Седмичен бюлетин',

        'subject'               => 'Тема/Заглавие',
        'category'              => 'Категория',
        'description'           => 'Описание',
        'contact_email'         => 'Имейл за контакт',
        'name'                  => 'Име',
        'g-recaptcha-response'  => 'Captcha',

        'current_password'       => 'Текуща парола',
        'password'               => 'Парола',
        'password_confirmation'  => 'Потвърждение на паролата',
        'email'                  => 'Имейл',

        'image' => 'снимката',
    ],

];
