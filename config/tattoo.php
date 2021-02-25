<?php
return [
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
