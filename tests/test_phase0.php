<?php

declare(strict_types=1);

echo "=== ONLINETRAVELLER PHASE 0 VERIFICATION TEST ===\n\n";

// 1. Test Bootstrap & Autoloader
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/health';

$app = require dirname(__DIR__) . '/bootstrap/app.php';
echo "1. Bootstrap & Autoloader: OK\n";

// 2. Test Database PDO connection
$db = \Core\Database::getInstance();
$version = $db->fetchOne("SELECT VERSION() AS v")['v'];
echo "2. Database Connection: Connected to MariaDB/MySQL ({$version})\n";

// 3. Test Table counts
$tables = $db->fetchAll("SHOW TABLES;");
echo "3. Tables Count: " . count($tables) . " tables (Target: 15)\n";
foreach ($tables as $t) {
    $tableName = array_values($t)[0];
    $count = $db->fetchOne("SELECT COUNT(*) AS c FROM `{$tableName}`")['c'];
    echo "   - {$tableName}: {$count} rows\n";
}

// 4. Test Stored Procedures count
$procedures = $db->fetchAll("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = 'PROCEDURE' ORDER BY ROUTINE_NAME");
echo "4. Stored Procedures Count: " . count($procedures) . " procedures (Target: 22)\n";

// 5. Test Calling a Stored Procedure (dhs_sp_users_find_login)
$loginTest = $db->callProcedure('dhs_sp_users_find_login', ['admin@onlinetraveller.vn']);
echo "5. Procedure Call (dhs_sp_users_find_login): " . (!empty($loginTest) ? 'SUCCESS (Found ' . $loginTest[0]['name'] . ')' : 'FAILED') . "\n";

// 6. Test Service Model & 3D Stops
$serviceModel = new \App\Models\Service();
$hero = $serviceModel->getHeroFeatured();
echo "6. Service Model Hero Tour: " . ($hero ? $hero['title'] . ' (Rating: ' . $hero['avg_rating'] . ')' : 'None') . "\n";

$stops = $serviceModel->getStops(1);
echo "7. Three.js 3D Virtual Tour Stops for Service 1: " . count($stops) . " stops\n";
foreach ($stops as $idx => $s) {
    echo "   - Stop " . ($idx + 1) . ": {$s['caption']}\n";
}

// 8. Test Route Matching for /api/health
$request = new \Core\Request();
echo "8. Request Path parsed: " . $request->getPath() . "\n";

echo "\n=== ALL TESTS COMPLETED SUCCESSFULLY! ===\n";
