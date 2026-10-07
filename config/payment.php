<?php

declare(strict_types=1);

// Keep sandbox credentials on the server. Endpoint/signature integration is pending.
return [
    'momo' => [
        'environment' => 'sandbox',
        'endpoint' => getenv('MOMO_ENDPOINT') ?: '',
        'partner_code' => getenv('MOMO_PARTNER_CODE') ?: '',
        'access_key' => getenv('MOMO_ACCESS_KEY') ?: '',
        'secret_key' => getenv('MOMO_SECRET_KEY') ?: '',
        'redirect_url' => getenv('MOMO_REDIRECT_URL') ?: '',
        'ipn_url' => getenv('MOMO_IPN_URL') ?: '',
    ],
];
