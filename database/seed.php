<?php

declare(strict_types=1);

/**
 * Reseeds the database with clean UTF-8 sample data.
 * Usage: php database/seed.php
 */

require_once __DIR__ . '/../bootstrap/app.php';

$pdo = \Core\Database::getConnection();

echo "Re-seeding OnlineTraveller database with UTF-8 sample data...\n";

$seedFile = __DIR__ . '/seed_data.sql';
if (!is_file($seedFile)) {
    die("Error: seed_data.sql not found at {$seedFile}\n");
}

$sql = file_get_contents($seedFile);

// Remove UTF-8 BOM if present
$sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);

try {
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec($sql);
    echo "Seed data imported successfully!\n";
} catch (PDOException $e) {
    die("Database reseed error: " . $e->getMessage() . "\n");
}

// Verification query
$cats = $pdo->query("SELECT id, name, description FROM tbl_categories")->fetchAll();
echo "\n--- VERIFICATION: CATEGORIES ---\n";
foreach ($cats as $c) {
    echo "ID {$c['id']}: {$c['name']} (desc: " . mb_substr($c['description'] ?? '', 0, 35) . "...)\n";
}

$users = $pdo->query("SELECT id, name, email FROM tbl_users")->fetchAll();
echo "\n--- VERIFICATION: USERS ---\n";
foreach ($users as $u) {
    echo "ID {$u['id']}: {$u['name']} ({$u['email']})\n";
}

$tours = $pdo->query("SELECT id, title, location FROM tbl_services")->fetchAll();
echo "\n--- VERIFICATION: TOURS ---\n";
foreach ($tours as $t) {
    echo "ID {$t['id']}: {$t['title']} [{$t['location']}]\n";
}
