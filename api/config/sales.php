<?php
return [
    'commission_rate'   => env('COMMISSION_RATE', 0.085),
    'currency'          => 'BRL,',
    'admin_email'       => env('ADMIN_EMAIL', 'admin@example.test'),
    'cache_ttl' => [
        'today' => env('SALES_CACHE_TTL_TODAY', 60),
        'past'  => env('SALES_CACHE_TTL_PAST', 60 * 60 * 24 * 30),
    ],
];
