<?php

declare(strict_types=1);

namespace App\Integrations;

/**
 * OpenWeatherMap JSON Webservice Integration Client.
 *
 * Provides real-time weather forecasting for tour destinations with
 * automatic local caching, timeout protection, and intelligent fallback
 * for offline development and testing.
 */
class OpenWeatherClient
{
    private string $apiKey;
    private int $timeout;
    private string $cacheDir;
    private int $cacheTtl;

    public function __construct(
        ?string $apiKey = null,
        int $timeout = 4,
        ?string $cacheDir = null,
        int $cacheTtl = 1800
    ) {
        // Resolve API key from config/environment
        if ($apiKey !== null) {
            $this->apiKey = $apiKey;
        } else {
            $this->apiKey = $this->resolveConfigApiKey();
        }

        $this->timeout = $timeout;
        $this->cacheTtl = $cacheTtl;

        // Setup cache directory
        if ($cacheDir !== null) {
            $this->cacheDir = rtrim($cacheDir, '/\\');
        } else {
            $this->cacheDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'weather';
        }

        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Get weather forecast for a tour/service model record.
     *
     * @param array $service Tour details with location, latitude, longitude
     * @return array Standardized weather information
     */
    public function getWeatherForService(array $service): array
    {
        $location = (string)($service['location'] ?? 'Việt Nam');
        $lat = isset($service['latitude']) && $service['latitude'] !== null ? (float)$service['latitude'] : null;
        $lon = isset($service['longitude']) && $service['longitude'] !== null ? (float)$service['longitude'] : null;

        if ($lat !== null && $lon !== null && ($lat != 0.0 || $lon != 0.0)) {
            return $this->getWeatherByCoords($lat, $lon, $location);
        }

        return $this->getWeatherByCity($location);
    }

    /**
     * Query OpenWeatherMap by geographic coordinates (lat, lon).
     */
    public function getWeatherByCoords(float $lat, float $lon, string $locationName = ''): array
    {
        $cacheKey = 'coords_' . md5(round($lat, 4) . '_' . round($lon, 4));
        $cached = $this->readCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        if (!empty($this->apiKey)) {
            $url = sprintf(
                'https://api.openweathermap.org/data/2.5/weather?lat=%.4f&lon=%.4f&appid=%s&units=metric&lang=vi',
                $lat,
                $lon,
                urlencode($this->apiKey)
            );

            $response = $this->fetchJson($url);
            if ($response !== null && isset($response['main'])) {
                $weather = $this->transformApiResponse($response, $locationName);
                $this->writeCache($cacheKey, $weather);
                return $weather;
            }
        }

        // Fallback to intelligent regional weather simulation
        $fallback = $this->getMockFallback($locationName, $lat, $lon);
        $this->writeCache($cacheKey, $fallback);
        return $fallback;
    }

    /**
     * Query OpenWeatherMap by city/destination name.
     */
    public function getWeatherByCity(string $cityName): array
    {
        $cleanCity = trim($cityName);
        if ($cleanCity === '') {
            $cleanCity = 'Đà Lạt';
        }

        $cacheKey = 'city_' . md5(mb_strtolower($cleanCity));
        $cached = $this->readCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        if (!empty($this->apiKey)) {
            // Normalize city name for OpenWeather API query
            $searchQuery = $this->extractPrimaryCityName($cleanCity);
            $url = sprintf(
                'https://api.openweathermap.org/data/2.5/weather?q=%s,VN&appid=%s&units=metric&lang=vi',
                urlencode($searchQuery),
                urlencode($this->apiKey)
            );

            $response = $this->fetchJson($url);
            if ($response !== null && isset($response['main'])) {
                $weather = $this->transformApiResponse($response, $cleanCity);
                $this->writeCache($cacheKey, $weather);
                return $weather;
            }
        }

        // Fallback
        $fallback = $this->getMockFallback($cleanCity);
        $this->writeCache($cacheKey, $fallback);
        return $fallback;
    }

    /**
     * Transform raw OpenWeatherMap API JSON response into clean UI schema.
     */
    private function transformApiResponse(array $data, string $preferredLocationName): array
    {
        $main = $data['main'] ?? [];
        $weatherInfo = $data['weather'][0] ?? [];
        $wind = $data['wind'] ?? [];

        $temp = (int)round((float)($main['temp'] ?? 25));
        $feelsLike = (int)round((float)($main['feels_like'] ?? $temp));
        $tempMin = (int)round((float)($main['temp_min'] ?? ($temp - 2)));
        $tempMax = (int)round((float)($main['temp_max'] ?? ($temp + 2)));
        $humidity = (int)($main['humidity'] ?? 75);
        $windSpeed = (float)round((float)($wind['speed'] ?? 2.5), 1);
        $description = mb_convert_case((string)($weatherInfo['description'] ?? 'Trời trong xanh'), MB_CASE_TITLE, 'UTF-8');
        $icon = (string)($weatherInfo['icon'] ?? '02d');

        $locationName = !empty($preferredLocationName) ? $preferredLocationName : (string)($data['name'] ?? 'Điểm đến');
        $advice = $this->generateTravelAdvice($locationName, $temp, $description);

        return [
            'location' => $locationName,
            'temp' => $temp,
            'feels_like' => $feelsLike,
            'temp_min' => $tempMin,
            'temp_max' => $tempMax,
            'humidity' => $humidity,
            'wind_speed' => $windSpeed,
            'description' => $description,
            'icon' => $icon,
            'icon_url' => "https://openweathermap.org/img/wn/{$icon}@2x.png",
            'advice' => $advice,
            'is_mock' => false,
            'source' => 'OpenWeatherMap API',
            'updated_at' => date('H:i - d/m/Y'),
        ];
    }

    /**
     * Fallback generator providing realistic regional Vietnamese weather conditions.
     */
    public function getMockFallback(string $location, ?float $lat = null, ?float $lon = null): array
    {
        $lower = mb_strtolower($location);

        if (str_contains($lower, 'đà lạt') || str_contains($lower, 'da lat') || str_contains($lower, 'lâm đồng') || ($lat && abs($lat - 11.94) < 0.3)) {
            $temp = 18;
            $feelsLike = 17;
            $tempMin = 14;
            $tempMax = 22;
            $humidity = 82;
            $windSpeed = 2.4;
            $description = 'Se lạnh mát mẻ, sương mây lãng đãng';
            $icon = '02d';
            $advice = 'Đặc trưng cao nguyên se lạnh, bạn nên chuẩn bị thêm áo khoác ấm mỏng và khăn choàng nhẹ khi đi dạo buổi sáng/tối.';
        } elseif (str_contains($lower, 'sapa') || str_contains($lower, 'sa pa') || str_contains($lower, 'lào cai') || str_contains($lower, 'fansipan')) {
            $temp = 15;
            $feelsLike = 14;
            $tempMin = 11;
            $tempMax = 18;
            $humidity = 88;
            $windSpeed = 1.9;
            $description = 'Mây mù bao phủ, không khí mát lành';
            $icon = '50d';
            $advice = 'Vùng núi cao có sương mù ẩm, hãy mang theo áo ấm dày, áo chống gió và giày thể thao bám tốt.';
        } elseif (str_contains($lower, 'hà giang') || str_contains($lower, 'đồng văn') || str_contains($lower, 'mèo vạc')) {
            $temp = 21;
            $feelsLike = 21;
            $tempMin = 17;
            $tempMax = 25;
            $humidity = 76;
            $windSpeed = 3.1;
            $description = 'Nắng ráo dịu mát, gió cao nguyên trong lành';
            $icon = '01d';
            $advice = 'Thời tiết rất đẹp để đi đèo ngắm cảnh hùng vĩ; ban ngày nắng ấm, chiều tối gió vùng cao có thể lạnh.';
        } elseif (str_contains($lower, 'phú quốc') || str_contains($lower, 'kiên giang') || str_contains($lower, 'biển')) {
            $temp = 29;
            $feelsLike = 31;
            $tempMin = 26;
            $tempMax = 32;
            $humidity = 78;
            $windSpeed = 4.2;
            $description = 'Nắng vàng rực rỡ, sóng biển êm đềm';
            $icon = '01d';
            $advice = 'Thời tiết tuyệt vời cho các hoạt động lặn ngắm san hô và tắm biển; chuẩn bị kính râm, kem chống nắng và mũ rộng vành.';
        } elseif (str_contains($lower, 'đà nẵng') || str_contains($lower, 'hội an') || str_contains($lower, 'quảng nam')) {
            $temp = 27;
            $feelsLike = 28;
            $tempMin = 24;
            $tempMax = 30;
            $humidity = 75;
            $windSpeed = 3.5;
            $description = 'Trời nắng ráo xen mây nhẹ';
            $icon = '02d';
            $advice = 'Khí hậu ven biển thoáng đãng, thích hợp khám phá phố cổ về đêm hoặc đạp xe ngắm làng quê sông nước.';
        } elseif (str_contains($lower, 'hà nội') || str_contains($lower, 'ninh bình') || str_contains($lower, 'hạ long')) {
            $temp = 24;
            $feelsLike = 24;
            $tempMin = 20;
            $tempMax = 27;
            $humidity = 71;
            $windSpeed = 2.8;
            $description = 'Gió mát dịu dàng, thời tiết trong lành';
            $icon = '02d';
            $advice = 'Tiết trời ôn hòa dễ chịu, hoàn hảo cho những chuyến đi bộ thưởng thức ẩm thực và di tích lịch sử.';
        } else {
            $temp = 26;
            $feelsLike = 27;
            $tempMin = 22;
            $tempMax = 30;
            $humidity = 74;
            $windSpeed = 2.6;
            $description = 'Thời tiết ổn định, nắng ấm chan hòa';
            $icon = '02d';
            $advice = 'Điều kiện thời tiết rất thuận lợi để trải nghiệm cùng Dân bản địa.';
        }

        return [
            'location' => !empty($location) ? $location : 'Điểm đến trải nghiệm',
            'temp' => $temp,
            'feels_like' => $feelsLike,
            'temp_min' => $tempMin,
            'temp_max' => $tempMax,
            'humidity' => $humidity,
            'wind_speed' => $windSpeed,
            'description' => $description,
            'icon' => $icon,
            'icon_url' => "https://openweathermap.org/img/wn/{$icon}@2x.png",
            'advice' => $advice,
            'is_mock' => true,
            'source' => 'OpenWeatherMap (Fallback Service)',
            'updated_at' => date('H:i - d/m/Y'),
        ];
    }

    /**
     * Generate helpful local host advice based on weather & location.
     */
    private function generateTravelAdvice(string $location, int $temp, string $description): string
    {
        if ($temp < 20) {
            return 'Khí hậu se lạnh mát mẻ, bạn nên mang thêm áo ấm mỏng và khăn choàng nhẹ khi tham gia tour cùng Host.';
        }
        if ($temp > 30) {
            return 'Thời tiết nhiều nắng, hãy mang theo kem chống nắng, mũ nón và nước uống bổ sung khi di chuyển ngoài trời.';
        }
        if (str_contains(mb_strtolower($description), 'mưa')) {
            return 'Có khả năng xuất hiện mưa nhỏ, bạn nên mang theo ô che hoặc áo mưa mỏng để chuyến đi thêm trọn vẹn.';
        }
        return 'Thời tiết đang rất thuận lợi cho các hoạt động khám phá và giao lưu trải nghiệm cùng người dân bản xứ!';
    }

    /**
     * Extract primary city name from descriptive location strings (e.g. "Đà Lạt, Lâm Đồng" -> "Da Lat").
     */
    private function extractPrimaryCityName(string $location): string
    {
        $parts = explode(',', $location);
        $first = trim($parts[0]);

        $map = [
            'Đà Lạt' => 'Da Lat',
            'Sa Pa' => 'Sa Pa',
            'Sapa' => 'Sa Pa',
            'Hà Giang' => 'Ha Giang',
            'Đà Nẵng' => 'Da Nang',
            'Hội An' => 'Hoi An',
            'Hà Nội' => 'Hanoi',
            'Hồ Chí Minh' => 'Ho Chi Minh',
            'Sài Gòn' => 'Ho Chi Minh',
            'Phú Quốc' => 'Phu Quoc',
            'Nha Trang' => 'Nha Trang',
            'Huế' => 'Hue',
            'Quy Nhơn' => 'Quy Nhon',
            'Vũng Tàu' => 'Vung Tau',
            'Cần Thơ' => 'Can Tho',
        ];

        foreach ($map as $vi => $en) {
            if (mb_stripos($first, $vi) !== false) {
                return $en;
            }
        }

        return $first;
    }

    /**
     * Fetch JSON over HTTP via cURL or stream with strict timeout.
     */
    private function fetchJson(string $url): ?array
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => $this->timeout,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'User-Agent: OnlineTraveller-App/1.0',
                ],
            ]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && is_string($result)) {
                $decoded = json_decode($result, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
            return null;
        }

        // Stream fallback
        $context = stream_context_create([
            'http' => [
                'timeout' => $this->timeout,
                'header' => "Accept: application/json\r\nUser-Agent: OnlineTraveller-App/1.0\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $result = @file_get_contents($url, false, $context);
        if (is_string($result)) {
            $decoded = json_decode($result, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Read cached weather data if still valid.
     */
    private function readCache(string $key): ?array
    {
        $file = $this->cacheDir . DIRECTORY_SEPARATOR . $key . '.json';
        if (!is_file($file)) {
            return null;
        }

        $mtime = filemtime($file);
        if ($mtime === false || (time() - $mtime) > $this->cacheTtl) {
            @unlink($file);
            return null;
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            return null;
        }

        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Write weather data to file cache.
     */
    private function writeCache(string $key, array $data): void
    {
        $file = $this->cacheDir . DIRECTORY_SEPARATOR . $key . '.json';
        @file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * Resolve OpenWeather API key from config files or getenv.
     */
    private function resolveConfigApiKey(): string
    {
        $envKey = getenv('OPENWEATHER_API_KEY');
        if (!empty($envKey)) {
            return (string)$envKey;
        }

        $projectRoot = dirname(__DIR__, 2);
        if (is_file($projectRoot . '/config/local.php')) {
            $local = include $projectRoot . '/config/local.php';
            if (!empty($local['integrations']['weather']['api_key'])) {
                return (string)$local['integrations']['weather']['api_key'];
            }
        }

        if (is_file($projectRoot . '/config/integrations.php')) {
            $integ = include $projectRoot . '/config/integrations.php';
            if (!empty($integ['weather']['api_key'])) {
                return (string)$integ['weather']['api_key'];
            }
        }

        return '';
    }
}

if (!class_exists('OpenWeatherClient', false)) {
    class_alias(OpenWeatherClient::class, 'OpenWeatherClient');
}
