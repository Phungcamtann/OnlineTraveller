<?php

declare(strict_types=1);

namespace App\Integrations;

/**
 * Travel News RSS XML Feed Parser Integration.
 *
 * Uses SimpleXML with strict XML safety flags, automatic local caching,
 * image/summary regex extraction, and intelligent offline fallback.
 */
class RssClient
{
    private string $feedUrl;
    private int $timeout;
    private string $cacheDir;
    private int $cacheTtl;

    public function __construct(
        ?string $feedUrl = null,
        int $timeout = 4,
        ?string $cacheDir = null,
        int $cacheTtl = 3600
    ) {
        $this->feedUrl = $feedUrl ?? $this->resolveConfigFeedUrl();
        $this->timeout = $timeout;
        $this->cacheTtl = $cacheTtl;

        if ($cacheDir !== null) {
            $this->cacheDir = rtrim($cacheDir, '/\\');
        } else {
            $this->cacheDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'rss';
        }

        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Get parsed travel news items with limit and disk caching.
     *
     * @param int $limit Maximum number of articles to return
     * @return array List of standardized article items
     */
    public function getFeed(int $limit = 6): array
    {
        $cacheKey = 'feed_' . md5($this->feedUrl . '_' . $limit);
        $cached = $this->readCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $xmlString = $this->fetchXml($this->feedUrl);
        if ($xmlString !== null) {
            $items = $this->parseXml($xmlString, $limit);
            if (!empty($items)) {
                $this->writeCache($cacheKey, $items);
                return $items;
            }
        }

        // Fallback to high quality offline mock articles
        $fallback = $this->getMockFallback($limit);
        $this->writeCache($cacheKey, $fallback);
        return $fallback;
    }

    /**
     * Parse raw XML string using SimpleXML into clean article array.
     */
    public function parseXml(string $xmlString, int $limit = 6): array
    {
        // Safe XML parsing options: no entity resolution, clean CDATA
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        if ($xml === false || !isset($xml->channel->item)) {
            return [];
        }

        $items = [];
        $count = 0;

        foreach ($xml->channel->item as $item) {
            if ($count >= $limit) {
                break;
            }

            $title = trim((string)$item->title);
            $link = trim((string)$item->link);
            $rawDesc = (string)$item->description;
            $rawPubDate = (string)$item->pubDate;

            // Extract thumbnail image from description HTML or enclosure
            $thumbnail = $this->extractThumbnail($item, $rawDesc);

            // Clean plain text summary
            $summary = $this->cleanSummary($rawDesc);

            // Format date
            $pubTime = !empty($rawPubDate) ? strtotime($rawPubDate) : false;
            $formattedDate = $pubTime ? date('H:i - d/m/Y', $pubTime) : date('H:i - d/m/Y');

            $items[] = [
                'title' => $title,
                'link' => $link,
                'summary' => $summary,
                'thumbnail' => $thumbnail,
                'pub_date' => $formattedDate,
                'source' => 'VnExpress Du Lịch',
                'is_mock' => false,
            ];

            $count++;
        }

        return $items;
    }

    /**
     * Extract image URL from XML item enclosure or description HTML img tag.
     */
    private function extractThumbnail(\SimpleXMLElement $item, string $description): string
    {
        // 1. Check <enclosure url="..." type="image/...">
        if (isset($item->enclosure) && isset($item->enclosure['url'])) {
            $encUrl = (string)$item->enclosure['url'];
            if (!empty($encUrl)) {
                return $encUrl;
            }
        }

        // 2. Regex search for <img src="..." in description HTML
        if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $description, $matches)) {
            return $matches[1];
        }

        // 3. Fallback placeholder
        return 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80';
    }

    /**
     * Clean raw description into readable plain text summary.
     */
    private function cleanSummary(string $description): string
    {
        // Strip CDATA, HTML tags and extra white spaces
        $plain = strip_tags($description);
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace('/\s+/', ' ', $plain);
        $clean = trim((string)$clean);

        // Truncate cleanly around 160 characters
        if (mb_strlen($clean) > 170) {
            $sub = mb_substr($clean, 0, 160);
            $lastSpace = mb_strrpos($sub, ' ');
            if ($lastSpace !== false) {
                $sub = mb_substr($sub, 0, $lastSpace);
            }
            return $sub . '...';
        }

        return $clean;
    }

    /**
     * Fetch XML string from remote feed URL via cURL or stream with timeout.
     */
    private function fetchXml(string $url): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => $this->timeout,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/rss+xml, application/xml, text/xml',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) OnlineTraveller-RSS/1.0',
                ],
            ]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && is_string($result) && !empty($result)) {
                return $result;
            }
            return null;
        }

        // Stream context fallback
        $context = stream_context_create([
            'http' => [
                'timeout' => $this->timeout,
                'header' => "Accept: application/rss+xml, application/xml, text/xml\r\nUser-Agent: OnlineTraveller-RSS/1.0\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $result = @file_get_contents($url, false, $context);
        return is_string($result) && !empty($result) ? $result : null;
    }

    /**
     * Read cached parsed news items.
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
     * Write parsed news items to JSON file cache.
     */
    private function writeCache(string $key, array $data): void
    {
        $file = $this->cacheDir . DIRECTORY_SEPARATOR . $key . '.json';
        @file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * Fallback curated travel articles when network is offline.
     */
    public function getMockFallback(int $limit = 6): array
    {
        $all = [
            [
                'title' => 'Mùa Lúa Chín Vàng Óng Trên Những Thửa Ruộng Bậc Thang Mù Cang Chải',
                'link' => 'https://vnexpress.net/du-lich',
                'summary' => 'Tháng 9 và tháng 10 hàng năm là thời điểm Mù Cang Chải khoác lên mình tấm áo vàng rực rỡ của những triền ruộng bậc thang lúa chín thơm ngát, thu hút hàng vạn lữ khách.',
                'thumbnail' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=800&auto=format&fit=crop&q=80',
                'pub_date' => date('H:i - d/m/Y', strtotime('-2 hours')),
                'source' => 'VnExpress Du Lịch (Bản Lưu Cache)',
                'is_mock' => true,
            ],
            [
                'title' => 'Kinh Nghiệm Săn Mây Và Đón Bình Minh Tuyệt Sắc Trên Đỉnh LangBiang Đà Lạt',
                'link' => 'https://vnexpress.net/du-lich',
                'summary' => 'Hành trình cùng hướng dẫn viên bản địa thức dậy từ 4h sáng, vượt những con dốc thông reo để chiêm ngưỡng biển mây bồng bềnh phủ kín thung lũng Đà Lạt trong ánh nắng sớm.',
                'thumbnail' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=800&auto=format&fit=crop&q=80',
                'pub_date' => date('H:i - d/m/Y', strtotime('-4 hours')),
                'source' => 'VnExpress Du Lịch (Bản Lưu Cache)',
                'is_mock' => true,
            ],
            [
                'title' => 'Trải Nghiệm Văn Hóa Sông Nước Miền Tây: Một Ngày Đi Chợ Nổi Cái Răng Cùng Người Dân',
                'link' => 'https://vnexpress.net/du-lich',
                'summary' => 'Tiếng xuồng máy rộn rã, những cây bẹo treo lủng lẳng dưa hấu, bưởi năm roi và tô hủ tiếu nóng hổi ngay trên mạn thuyền đem lại trải nghiệm văn hóa đậm chất Nam Bộ.',
                'thumbnail' => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?w=800&auto=format&fit=crop&q=80',
                'pub_date' => date('H:i - d/m/Y', strtotime('-7 hours')),
                'source' => 'VnExpress Du Lịch (Bản Lưu Cache)',
                'is_mock' => true,
            ],
            [
                'title' => 'Bí Quyết Trekking Rừng Nguyên Sinh Bidoup - Núi Bà An Toàn Cho Người Mới Bắt Đầu',
                'link' => 'https://vnexpress.net/du-lich',
                'summary' => 'Khám phá thảm thực vật phong phú của mái nhà Tây Nguyên với sự đồng hành của người dẫn đường dân tộc K\'Ho am hiểu từng lối mòn và nguồn nước mát.',
                'thumbnail' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800&auto=format&fit=crop&q=80',
                'pub_date' => date('H:i - d/m/Y', strtotime('-12 hours')),
                'source' => 'VnExpress Du Lịch (Bản Lưu Cache)',
                'is_mock' => true,
            ],
            [
                'title' => 'Lạc Bước Giữa Những Con Ngõ Rêu Phong Và Đèn Lồng Lung Linh Tại Phố Cổ Hội An',
                'link' => 'https://vnexpress.net/du-lich',
                'summary' => 'Khi màn đêm buông xuống, dòng sông Hoài lấp lánh hoa đăng, những mái ngói âm dương phủ đầy rêu phong kể câu chuyện thương cảng phồn hoa thế kỷ 17.',
                'thumbnail' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=800&auto=format&fit=crop&q=80',
                'pub_date' => date('H:i - d/m/Y', strtotime('-1 day')),
                'source' => 'VnExpress Du Lịch (Bản Lưu Cache)',
                'is_mock' => true,
            ],
            [
                'title' => 'Ngắm Hoàng Hôn Bãi Dài Phú Quốc: Thiên Đường Biển Đảo Đẹp Nhất Mùa Nắng Đẹp',
                'link' => 'https://vnexpress.net/du-lich',
                'summary' => 'Bờ cát trắng mịn trải dài cùng làn nước ngọc bích trong vắt, thưởng thức hải sản tươi rói do chính ngư dân địa phương đánh bắt lúc xế chiều.',
                'thumbnail' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80',
                'pub_date' => date('H:i - d/m/Y', strtotime('-1 day')),
                'source' => 'VnExpress Du Lịch (Bản Lưu Cache)',
                'is_mock' => true,
            ],
        ];

        return array_slice($all, 0, $limit);
    }

    /**
     * Resolve RSS Feed URL from config.
     */
    private function resolveConfigFeedUrl(): string
    {
        $projectRoot = dirname(__DIR__, 2);

        if (is_file($projectRoot . '/config/local.php')) {
            $local = include $projectRoot . '/config/local.php';
            if (!empty($local['integrations']['rss']['url'])) {
                return (string)$local['integrations']['rss']['url'];
            }
        }

        if (is_file($projectRoot . '/config/integrations.php')) {
            $integ = include $projectRoot . '/config/integrations.php';
            if (!empty($integ['rss']['url'])) {
                return (string)$integ['rss']['url'];
            }
        }

        return 'https://vnexpress.net/rss/du-lich.rss';
    }
}

if (!class_exists('RssClient', false)) {
    class_alias(RssClient::class, 'RssClient');
}
