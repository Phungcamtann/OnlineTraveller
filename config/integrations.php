<?php

declare(strict_types=1);

return [
    'weather' => ['api_key' => getenv('OPENWEATHER_API_KEY') ?: ''],
    'rss' => ['url' => 'https://vnexpress.net/rss/du-lich.rss'],
    'timeout_seconds' => 10,
];
