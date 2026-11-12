<?php

declare(strict_types=1);

/**
 * ============================================================================
 * ONLINETRAVELLER - MASTER TEST SUITE RUNNER
 * Chạy toàn bộ các bộ kiểm thử tự động của hệ thống (Sprint 1, Sprint 2, UI)
 * Cách dùng: php tests/run_all_tests.php
 * ============================================================================
 */

$phpBinary = PHP_BINARY;
if (!is_file($phpBinary)) {
    $phpBinary = 'C:\\xampp\\php\\php.exe';
}

$testSuites = [
    // --- GIAO DIỆN & BỐ CỤC UI DARK-ALPINE ---
    [
        'category' => 'GIAO DIỆN & THEME',
        'code'     => 'UI-01',
        'file'     => 'test_step1_layout_theme.php',
        'name'     => 'Dark-Alpine Theme, Glassmorphism & Main Layout',
    ],
    [
        'category' => 'GIAO DIỆN & THEME',
        'code'     => 'UI-02',
        'file'     => 'test_step2_home_index.php',
        'name'     => 'Trang chủ: Hero, Search Bar, Weather Cards & RSS',
    ],
    [
        'category' => 'GIAO DIỆN & THEME',
        'code'     => 'UI-03',
        'file'     => 'test_step3_services_show.php',
        'name'     => 'Chi tiết Tour: Timeline Trạm, 3D Z-Scroll Modal & Đặt chỗ',
    ],

    // --- SPRINT 1: STORED PROCEDURES & BUSINESS CORE ---
    [
        'category' => 'SPRINT 1 CORE',
        'code'     => 'TV2-01',
        'file'     => 'test_tv2_01.php',
        'name'     => 'Quản trị Tour, Homestay & Lịch trình (6 Stored Procedures)',
    ],
    [
        'category' => 'SPRINT 1 CORE',
        'code'     => 'TV3-01',
        'file'     => 'test_tv3_01.php',
        'name'     => 'Báo giá, Đặt cọc 50%, Giữ chỗ & Thanh toán (6 Stored Procedures)',
    ],
    [
        'category' => 'SPRINT 1 CORE',
        'code'     => 'TV4-01',
        'file'     => 'test_tv4_01.php',
        'name'     => 'Cộng đồng: Q&A, Đánh giá sao, Blog & Coupon (5 Stored Procedures)',
    ],
    [
        'category' => 'SPRINT 1 CORE',
        'code'     => 'TV3-CSRF',
        'file'     => 'test_booking_flow_csrf.php',
        'name'     => 'End-to-End Booking Flow tích hợp CSRF Protection',
    ],

    // --- SPRINT 2: TƯƠNG TÁC NÂNG CAO & HOÀN THIỆN LUỒNG ---
    [
        'category' => 'SPRINT 2 NÂNG CAO',
        'code'     => 'TV1-05',
        'file'     => 'test_sprint2_tv1_05.php',
        'name'     => 'Bảo mật RBAC & CsrfMiddleware (Guest, Tourist, Host, Admin)',
    ],
    [
        'category' => 'SPRINT 2 NÂNG CAO',
        'code'     => 'TV1-06',
        'file'     => 'test_sprint2_tv1_06.php',
        'name'     => 'Quản trị User & Khóa/Mở khóa tài khoản (dhs_sp_users_set_status)',
    ],
    [
        'category' => 'SPRINT 2 NÂNG CAO',
        'code'     => 'TV2-05',
        'file'     => 'test_sprint2_tv2_05.php',
        'name'     => 'Host duyệt đơn & Giữ chỗ 15 phút (ndtt_sp_bookings_approve)',
    ],
    [
        'category' => 'SPRINT 2 NÂNG CAO',
        'code'     => 'TV3-05',
        'file'     => 'test_sprint2_tv3_05.php',
        'name'     => 'Thanh toán MoMo Sandbox QR & Webhook IPN HMAC-SHA256',
    ],
    [
        'category' => 'SPRINT 2 NÂNG CAO',
        'code'     => 'TV3-06',
        'file'     => 'test_sprint2_tv3_06.php',
        'name'     => 'Hóa đơn điện tử (E-Receipt) & Xử lý Hoàn tiền Hủy tour T24',
    ],
    [
        'category' => 'SPRINT 2 NÂNG CAO',
        'code'     => 'TV4-05',
        'file'     => 'test_sprint2_tv4_05.php',
        'name'     => 'Kênh Hỏi - Đáp 1 cấp AJAX & Host bản địa phản hồi',
    ],
    [
        'category' => 'SPRINT 2 NÂNG CAO',
        'code'     => 'TV4-06',
        'file'     => 'test_sprint2_tv4_06.php',
        'name'     => 'Đánh giá 1-5 sao sau tour & Quản trị Cẩm nang Blog Admin',
    ],

    // --- CẢI TIẾN HỆ THỐNG SAU KIỂM THỬ THỰC TẾ ---
    [
        'category' => 'CẢI TIẾN HỆ THỐNG',
        'code'     => 'PHASE-1',
        'file'     => 'test_phase1_improvements.php',
        'name'     => 'Minh bạch VAT 8%, Căn đều nút thanh toán & Trạng thái cọc Host',
    ],
    [
        'category' => 'CẢI TIẾN HỆ THỐNG',
        'code'     => 'PHASE-2',
        'file'     => 'test_phase2_reviews.php',
        'name'     => 'Khối Đánh giá tour công khai & Nhãn bảo chứng Đã tự trải nghiệm',
    ],
    [
        'category' => 'CẢI TIẾN HỆ THỐNG',
        'code'     => 'PHASE-5',
        'file'     => 'test_phase5_host_services.php',
        'name'     => 'Cải tiến Form Đăng Tour Host: Geocoding tự động & Upload ảnh trạm',
    ],
    [
        'category' => 'CẢI TIẾN HỆ THỐNG',
        'code'     => 'PHASE-3',
        'file'     => 'test_phase3_community_posts.php',
        'name'     => 'Bản tin cộng đồng mở 3 vai trò & Tích hợp tin tức RSS VnExpress',
    ],
    [
        'category' => 'CẢI TIẾN HỆ THỐNG',
        'code'     => 'PHASE-4',
        'file'     => 'test_phase4_profile_enhancements.php',
        'name'     => 'Hồ sơ người dùng (Giới tính, Ngày sinh), Bản in đơn & Hiện mật khẩu',
    ],
    [
        'category' => 'CẢI TIẾN HỆ THỐNG',
        'code'     => 'PHASE-6',
        'file'     => 'test_phase6_slider_cards_banner.php',
        'name'     => 'Range Slider ngân sách, Đồng bộ Card tour & Slider Banner thumbnails',
    ],
    [
        'category' => 'QUẢN TRỊ ADMIN',
        'code'     => 'ADMIN-MOD',
        'file'     => 'test_admin_service_moderation.php',
        'name'     => 'Quản trị và Phê duyệt Trải nghiệm mới (Tour Moderation & Approval)',
    ],
    [
        'category' => 'SPRINT 3 TÍCH HỢP',
        'code'     => 'SPRINT-3',
        'file'     => 'test_sprint3_integration.php',
        'name'     => 'Tích hợp Tổng thể: Lazy Expiration (INT-01) & Custom Errors/Toasts (INT-02)',
    ],
];

echo "\n" . str_repeat('=', 84) . "\n";
echo "      ONLINETRAVELLER - TOÀN BỘ BỘ KIỂM THỬ TỰ ĐỘNG HỆ THỐNG (MASTER RUNNER)      \n";
echo str_repeat('=', 84) . "\n";
echo "Thời gian bắt đầu: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Binary:       " . $phpBinary . "\n";
echo "Tổng số test suite: " . count($testSuites) . " bộ kiểm thử\n";
echo str_repeat('-', 84) . "\n\n";

$suiteResults = [];
$totalSuitesPassed = 0;
$totalSuitesFailed = 0;
$grandTotalTests = 0;
$grandPassedTests = 0;
$overallStartTime = microtime(true);

foreach ($testSuites as $idx => $suite) {
    $filePath = __DIR__ . '/' . $suite['file'];
    $suiteNum = $idx + 1;
    echo sprintf("[%02d/%02d] Đang chạy: %-8s | %s ...\n", $suiteNum, count($testSuites), $suite['code'], $suite['name']);

    if (!is_file($filePath)) {
        echo "       [ERROR] Không tìm thấy file: {$suite['file']}\n";
        $suiteResults[] = [
            'code'     => $suite['code'],
            'name'     => $suite['name'],
            'file'     => $suite['file'],
            'status'   => 'MISSING',
            'duration' => 0,
            'passed'   => 0,
            'total'    => 0,
            'output'   => 'File not found',
        ];
        $totalSuitesFailed++;
        continue;
    }

    $startTime = microtime(true);
    $cmd = escapeshellarg($phpBinary) . ' ' . escapeshellcmd($filePath);
    $output = [];
    $returnVar = 0;

    exec($cmd . ' 2>&1', $output, $returnVar);
    $duration = microtime(true) - $startTime;
    $outputStr = implode("\n", $output);

    // Extract test counts if available
    $passedCount = 0;
    $totalCount = 0;
    
    // Pattern 1: KẾT QUẢ KIỂM THỬ: X / Y TESTS PASSED
    if (preg_match('/KẾT QUẢ KIỂM THỬ:\s*(\d+)\s*\/\s*(\d+)\s*TESTS PASSED/u', $outputStr, $m)) {
        $passedCount = (int)$m[1];
        $totalCount  = (int)$m[2];
    }
    // Pattern 2: X / Y test cases đạt chuẩn
    elseif (preg_match('/(\d+)\s*\/\s*(\d+)\s*test cases/ui', $outputStr, $m)) {
        $passedCount = (int)$m[1];
        $totalCount  = (int)$m[2];
    }
    // Pattern 3: count [PASS] lines
    else {
        $passedCount = preg_match_all('/\[PASS\]/u', $outputStr);
        $failedCount = preg_match_all('/\[FAIL\]/u', $outputStr);
        $totalCount = $passedCount + $failedCount;
        if ($totalCount === 0 && $returnVar === 0) {
            // Implicit pass with single assertion
            $passedCount = 1;
            $totalCount = 1;
        }
    }

    $isSuccess = ($returnVar === 0) && (!str_contains($outputStr, '[FAIL]') && !str_contains($outputStr, 'Fatal error') && !str_contains($outputStr, 'Parse error'));

    if ($isSuccess) {
        $totalSuitesPassed++;
        $grandTotalTests += $totalCount;
        $grandPassedTests += $passedCount;
        echo sprintf("       ==> [PASS] %d/%d assertions đạt chuẩn (%.2fs)\n\n", $passedCount, $totalCount, $duration);
    } else {
        $totalSuitesFailed++;
        $grandTotalTests += $totalCount;
        $grandPassedTests += $passedCount;
        echo sprintf("       ==> [FAIL] Có lỗi xảy ra trong suite này (%.2fs)\n\n", $duration);
    }

    $suiteResults[] = [
        'code'     => $suite['code'],
        'name'     => $suite['name'],
        'file'     => $suite['file'],
        'status'   => $isSuccess ? 'PASS' : 'FAIL',
        'duration' => $duration,
        'passed'   => $passedCount,
        'total'    => $totalCount,
    ];
}

$totalDuration = microtime(true) - $overallStartTime;

// ==============================================================================
// BẢNG TỔNG KẾT KẾT QUẢ TẤT CẢ TEST SUITES
// ==============================================================================
echo "\n" . str_repeat('=', 84) . "\n";
echo "                         BẢNG TỔNG KẾT KẾT QUẢ KIỂM THỬ                         \n";
echo str_repeat('=', 84) . "\n";
echo sprintf("%-8s | %-44s | %-8s | %-8s | %s\n", "MÃ", "TÊN BỘ KIỂM THỬ", "ASSERT", "THỜI GIAN", "TRẠNG THÁI");
echo str_repeat('-', 84) . "\n";

foreach ($suiteResults as $r) {
    $statusText = $r['status'] === 'PASS' ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m";
    // Windows fallback for ANSI
    if (PHP_OS_FAMILY === 'Windows') {
        $statusText = $r['status'] === 'PASS' ? "[PASS]" : "[FAIL]";
    }
    $assertText = "{$r['passed']}/{$r['total']}";
    $timeText = sprintf("%.2fs", $r['duration']);
    $shortName = mb_strimwidth($r['name'], 0, 43, '...');
    echo sprintf("%-8s | %-44s | %-8s | %-8s | %s\n", $r['code'], $shortName, $assertText, $timeText, $statusText);
}

echo str_repeat('=', 84) . "\n";
echo sprintf("Tổng số Suite kiểm thử:   %d (Thành công: %d, Thất bại: %d)\n", count($testSuites), $totalSuitesPassed, $totalSuitesFailed);
echo sprintf("Tổng số Assertions test:  %d / %d PASSED (Tỷ lệ: %.1f%%)\n", $grandPassedTests, $grandTotalTests, ($grandTotalTests > 0 ? ($grandPassedTests / $grandTotalTests) * 100 : 0));
echo sprintf("Tổng thời gian thực thi:  %.2f giây\n", $totalDuration);

if ($totalSuitesFailed === 0) {
    echo "\n>>> XÁC NHẬN: TẤT CẢ CÁC BỘ KIỂM THỬ CỦA HỆ THỐNG ĐÃ ĐẠT CHUẨN 100%! <<<\n";
    echo "Hệ thống OnlineTraveller đã sẵn sàng cho bước tinh chỉnh UI và bước vào Sprint 3!\n";
} else {
    echo "\n>>> CẢNH BÁO: CÓ {$totalSuitesFailed} BỘ TEST KHÔNG ĐẠT. VUI LÒNG KIỂM TRA LẠI CHI TIẾT BÊN TRÊN! <<<\n";
}
echo str_repeat('=', 84) . "\n\n";

exit($totalSuitesFailed === 0 ? 0 : 1);
