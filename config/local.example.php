<?php

declare(strict_types=1);

// Copy to local.php (ignored by Git) and override only the values you need.
// Prefer environment variables. Do not place real secrets in this example.
return [
    'database' => ['user' => '', 'password' => ''],
    'payment' => ['momo' => [
        'partner_code' => '', 'access_key' => '', 'secret_key' => '',
    ]],
    'integrations' => ['weather' => ['api_key' => '']],
];
