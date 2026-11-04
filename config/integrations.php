<?php

declare(strict_types=1);

return [
    'google_maps' => [
        'api_key' => getenv('GOOGLE_MAPS_API_KEY') ?: '',
    ],
    'weather' => [
        'api_key' => getenv('OPENWEATHER_API_KEY') ?: '',
    ],
    'rss' => [
        'url' => 'https://vnexpress.net/rss/du-lich.rss',
    ],
    'timeout_seconds' => 10,
];
