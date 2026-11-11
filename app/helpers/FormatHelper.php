<?php

declare(strict_types=1);

/**
 * Reusable formatting helper: currency, dates, VAT calculations and status badges.
 */
class FormatHelper
{
    /**
     * Format integer amount as VND currency.
     */
    public static function formatVnd(int|float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Format date time to Asia/Ho_Chi_Minh display.
     */
    public static function formatDateTime(string $datetime, string $format = 'd/m/Y H:i'): string
    {
        try {
            $timestamp = strtotime($datetime);
            return $timestamp ? date($format, $timestamp) : $datetime;
        } catch (\Throwable) {
            return $datetime;
        }
    }

    /**
     * Calculate VAT breakdown from a gross total (inclusive of VAT).
     * Default VAT rate in Vietnam tourism/hospitality is 8% (decreed VAT reduction).
     *
     * @param int|float $grossTotal Total price inclusive of VAT
     * @param float $vatRate VAT percentage rate (default 0.08 = 8%)
     * @return array ['net' => float, 'vat' => float, 'gross' => float, 'rate_percent' => int]
     */
    public static function calculateVat(int|float $grossTotal, float $vatRate = 0.08): array
    {
        $gross = (float)$grossTotal;
        $net = round($gross / (1 + $vatRate));
        $vat = $gross - $net;

        return [
            'net' => $net,
            'vat' => $vat,
            'gross' => $gross,
            'rate_percent' => (int)round($vatRate * 100),
        ];
    }

    /**
     * Get list of Vietnamese tourism provinces and major cities with standard coordinates.
     *
     * @return array<string, array{name: string, lat: float, lng: float}>
     */
    public static function getProvincesList(): array
    {
        return [
            'Lâm Đồng' => ['name' => 'Lâm Đồng (Đà Lạt, Lạc Dương)', 'lat' => 11.9404, 'lng' => 108.4583],
            'Lào Cai' => ['name' => 'Lào Cai (Sa Pa, Bắc Hà, Y Tý)', 'lat' => 22.3364, 'lng' => 103.8438],
            'Quảng Nam' => ['name' => 'Quảng Nam (Hội An, Mỹ Sơn)', 'lat' => 15.8801, 'lng' => 108.3380],
            'Đà Nẵng' => ['name' => 'Đà Nẵng (Bà Nà, Sơn Trà)', 'lat' => 16.0544, 'lng' => 108.2022],
            'Kiên Giang' => ['name' => 'Kiên Giang (Phú Quốc, Nam Du)', 'lat' => 10.2289, 'lng' => 103.9572],
            'Khánh Hòa' => ['name' => 'Khánh Hòa (Nha Trang, Cam Ranh)', 'lat' => 12.2388, 'lng' => 109.1967],
            'Thừa Thiên Huế' => ['name' => 'Thừa Thiên Huế (Cố Đô, Lăng Cô)', 'lat' => 16.4637, 'lng' => 107.5909],
            'Ninh Bình' => ['name' => 'Ninh Bình (Tràng An, Tam Cốc)', 'lat' => 20.2506, 'lng' => 105.9745],
            'Hà Giang' => ['name' => 'Hà Giang (Đồng Văn, Mèo Vạc)', 'lat' => 22.8233, 'lng' => 104.9839],
            'Yên Bái' => ['name' => 'Yên Bái (Mù Cang Chải, Tú Lệ)', 'lat' => 21.8488, 'lng' => 104.0863],
            'Cao Bằng' => ['name' => 'Cao Bằng (Thác Bản Giốc, Pác Bó)', 'lat' => 22.6666, 'lng' => 106.2639],
            'Quảng Ninh' => ['name' => 'Quảng Ninh (Hạ Long, Cô Tô)', 'lat' => 20.9505, 'lng' => 107.0733],
            'Hà Nội' => ['name' => 'Hà Nội (Hoàn Kiếm, Ba Vì, Phố Cổ)', 'lat' => 21.0285, 'lng' => 105.8542],
            'TP. Hồ Chí Minh' => ['name' => 'TP. Hồ Chí Minh (Trung tâm, Cần Giờ)', 'lat' => 10.8231, 'lng' => 106.6297],
            'Bà Rịa - Vũng Tàu' => ['name' => 'Bà Rịa - Vũng Tàu (Côn Đảo, Hồ Tràm)', 'lat' => 10.3460, 'lng' => 107.0843],
            'Bình Thuận' => ['name' => 'Bình Thuận (Mũi Né, Phan Thiết)', 'lat' => 10.9804, 'lng' => 108.2615],
            'Đắk Lắk' => ['name' => 'Đắk Lắk (Buôn Ma Thuột, Hồ Lắk)', 'lat' => 12.6675, 'lng' => 108.0383],
            'Gia Lai' => ['name' => 'Gia Lai (Pleiku, Chư Đăng Ya)', 'lat' => 13.9833, 'lng' => 108.0000],
            'Kon Tum' => ['name' => 'Kon Tum (Măng Đen, Nhà Thờ Gỗ)', 'lat' => 14.6015, 'lng' => 108.2980],
            'Cần Thơ' => ['name' => 'Cần Thơ (Chợ Nổi Cái Răng, Ninh Kiều)', 'lat' => 10.0452, 'lng' => 105.7469],
            'An Giang' => ['name' => 'An Giang (Châu Đốc, Rừng Tràm Trà Sư)', 'lat' => 10.5216, 'lng' => 105.1259],
            'Phú Yên' => ['name' => 'Phú Yên (Gành Đá Đĩa, Mũi Điện)', 'lat' => 13.0882, 'lng' => 109.3075],
            'Bình Định' => ['name' => 'Bình Định (Quy Nhơn, Kỳ Co)', 'lat' => 13.7820, 'lng' => 109.2197],
            'Quảng Bình' => ['name' => 'Quảng Bình (Phong Nha, Sơn Đoòng)', 'lat' => 17.4822, 'lng' => 106.5982],
            'Sơn La' => ['name' => 'Sơn La (Mộc Châu, Tà Xùa)', 'lat' => 20.8436, 'lng' => 104.6473],
            'Hòa Bình' => ['name' => 'Hòa Bình (Mai Châu, Thung Nai)', 'lat' => 20.6667, 'lng' => 105.0833],
            'Tây Ninh' => ['name' => 'Tây Ninh (Núi Bà Đen)', 'lat' => 11.3667, 'lng' => 106.1333],
            'Tiền Giang' => ['name' => 'Tiền Giang (Mỹ Tho, Thới Sơn)', 'lat' => 10.3544, 'lng' => 106.3653],
            'Bến Tre' => ['name' => 'Bến Tre (Xứ Dừa, Giồng Trôm)', 'lat' => 10.2433, 'lng' => 106.3756],
        ];
    }

    /**
     * Automatic geocoding: Inquire coordinates (latitude, longitude) from address/location string.
     * Matches against known tourism landmarks and province names.
     *
     * @param string $text Address or location text
     * @return array{latitude: float, longitude: float}
     */
    public static function lookupCoordinates(string $text): array
    {
        $textLower = mb_strtolower($text, 'UTF-8');

        // Specific hotspot landmark mappings
        $hotspots = [
            'đà lạt' => [11.9404, 108.4583],
            'cầu đất' => [11.8596, 108.5776],
            'langbiang' => [12.0444, 108.4414],
            'tuyền lâm' => [11.8906, 108.4287],
            'sa pa' => [22.3364, 103.8438],
            'sapa' => [22.3364, 103.8438],
            'fansipan' => [22.3033, 103.7753],
            'hội an' => [15.8801, 108.3380],
            'mỹ sơn' => [15.7981, 108.1245],
            'bà nà' => [15.9988, 107.9961],
            'phú quốc' => [10.2289, 103.9572],
            'nha trang' => [12.2388, 109.1967],
            'huế' => [16.4637, 107.5909],
            'tràng an' => [20.2506, 105.9145],
            'tam cốc' => [20.2185, 105.9329],
            'đồng văn' => [23.2778, 105.3611],
            'mã pí lèng' => [23.2389, 105.4194],
            'hà giang' => [22.8233, 104.9839],
            'mù cang chải' => [21.8488, 104.0863],
            'bản giốc' => [22.8533, 106.7236],
            'hạ long' => [20.9505, 107.0733],
            'vũng tàu' => [10.3460, 107.0843],
            'mũi né' => [10.9333, 108.2833],
            'phan thiết' => [10.9804, 108.2615],
            'măng đen' => [14.6015, 108.2980],
            'quy nhơn' => [13.7820, 109.2197],
            'kỳ co' => [13.9167, 109.2833],
            'phong nha' => [17.5833, 106.2833],
            'sơn đoòng' => [17.4564, 106.2872],
            'mộc châu' => [20.8436, 104.6473],
            'tà xùa' => [21.2858, 104.4697],
        ];

        foreach ($hotspots as $keyword => $coord) {
            if (str_contains($textLower, $keyword)) {
                return ['latitude' => $coord[0], 'longitude' => $coord[1]];
            }
        }

        // Check province names
        $provinces = self::getProvincesList();
        foreach ($provinces as $provName => $info) {
            if (str_contains($textLower, mb_strtolower($provName, 'UTF-8'))) {
                return ['latitude' => $info['lat'], 'longitude' => $info['lng']];
            }
        }

        // Default Vietnam center coordinates (Đà Nẵng / Đà Lạt)
        return ['latitude' => 11.9404, 'longitude' => 108.4583];
    }
}

if (!class_exists('App\Helpers\FormatHelper', false)) {
    class_alias(FormatHelper::class, 'App\Helpers\FormatHelper');
}
