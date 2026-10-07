<?php

declare(strict_types=1);

return [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'name' => getenv('DB_NAME') ?: 'online_traveller',
    'user' => getenv('DB_USER') ?: '',
    'password' => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
