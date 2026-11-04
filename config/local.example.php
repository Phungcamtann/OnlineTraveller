<?php

declare(strict_types=1);

// Copy to local.php (ignored by Git) and override only the values you need.
// Prefer environment variables. Do not place real secrets in this example.
return [
    'database' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'online_traveller',
        'user' => 'root',
        'password' => '',
    ],
    'integrations' => [
        'google_maps' => ['api_key' => ''],
        'weather' => ['api_key' => ''],
        'rss' => ['url' => 'https://vnexpress.net/rss/du-lich.rss'],
    ],
    'payment' => [
        'momo' => [
            'environment' => 'sandbox',
            'endpoint' => 'https://test-payment.momo.vn/v2/gateway/api/create',
            'partner_code' => '',
            'access_key' => '',
            'secret_key' => '',
            'redirect_url' => 'http://localhost:8000/payments/callback',
            'ipn_url' => 'http://localhost:8000/payments/ipn',
        ],
    ],
];
