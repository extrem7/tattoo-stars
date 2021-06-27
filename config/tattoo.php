<?php
return [
    'mail_system_receivers' => explode(',', env('MAIL_SYSTEM_RECEIVERS')),
    'languages' => [
        'ru' => [
            'name' => 'Русский',
            'on' => 'русском'
        ],
        'en' => [
            'name' => 'Английский',
            'on' => 'английском'
        ]
    ],
    'nickname_regex' => '/^([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/',
    'password_rules' => ['string', 'min:8', 'numbers', 'letters', 'confirmed']
];
