<?php

return [
    'support' => [
        'phone' => env('SUPPORT_PHONE'),
        'email' => env('SUPPORT_EMAIL'),
        'messengers' => [
            'viber' => env('SUPPORT_VIBER'),
            'facebook' => env('SUPPORT_PHONE'),
            'whatsapp' => env('SUPPORT_WHATSAPP'),
            'telegram' => env('SUPPORT_TELEGRAM')
        ]
    ],
    'local_geoip' => env('LOCAL_GEOIP')
];
