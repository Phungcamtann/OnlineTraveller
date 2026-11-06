<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$pdo = \Core\Database::getConnection();

echo "=== RE-IMPORTING ALL STORED PROCEDURES WITH NEW MEMBER PREFIXES ===\n\n";

// 1. Drop old unprefixed procedures
$oldProcs = [
    'sp_users_register',
    'sp_users_find_login',
    'sp_users_update_profile',
    'sp_users_set_status',
    'sp_categories_save',
    'sp_services_create',
    'sp_services_update',
    'sp_services_moderate',
    'sp_accommodations_save',
    'sp_schedules_create',
    'sp_schedules_cancel',
    'sp_bookings_quote',
    'sp_bookings_create',
    'sp_bookings_approve',
    'sp_bookings_cancel',
    'sp_payments_apply_verified',
    'sp_refunds_create_and_approve',
    'sp_discussions_create',
    'sp_reviews_create',
    'sp_posts_save',
    'sp_posts_delete',
    'sp_promotions_save',
];

echo "1. Dropping old unprefixed procedures...\n";
foreach ($oldProcs as $proc) {
    $pdo->exec("DROP PROCEDURE IF EXISTS `{$proc}`");
}
echo "   Done dropping old procedures.\n\n";

// 2. Import 4 SQL procedure files
$sqlFiles = [
    '01_users_categories.sql' => 'TV1 (Dương Hoàng Sâm: dhs_sp_*)',
    '02_services_bookings.sql' => 'TV2 (Phạm Thiên Long: ptl_sp_*)',
    '03_promotions_payments.sql' => 'TV3 (Nguyễn Duy Thành Tài: ndtt_sp_*)',
    '04_community_content.sql' => 'TV4 (Phùng Cẩm Tân: pct_sp_*)',
];

echo "2. Importing prefixed procedure definitions...\n";
$baseDir = dirname(__DIR__) . '/database/procedures/';

foreach ($sqlFiles as $file => $label) {
    $filePath = str_replace('\\', '/', $baseDir . $file);
    echo "   -> Importing {$label} from {$file}...\n";
    $cmd = "mysql.exe --default-character-set=utf8mb4 -P 3307 -u root online_traveller -e \"SOURCE {$filePath}\"";
    exec($cmd, $output, $returnCode);
    if ($returnCode !== 0) {
        throw new \RuntimeException("Failed to import {$file}: " . implode("\n", $output));
    }
}
echo "   Import completed successfully!\n\n";

// 3. Verify in database
echo "3. Verifying routines in MariaDB (online_traveller):\n";
$routines = $pdo->query("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = 'PROCEDURE' ORDER BY ROUTINE_NAME")->fetchAll(PDO::FETCH_COLUMN);

echo "Total procedures found: " . count($routines) . "\n";
foreach ($routines as $r) {
    echo " - {$r}\n";
}

echo "\nRe-import finished successfully!\n";
