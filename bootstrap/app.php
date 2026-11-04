<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);

// 1. Require autoloader and register
require_once $projectRoot . '/core/Autoloader.php';
\Core\Autoloader::register($projectRoot);

// 2. Load configuration files
$config = [];
foreach (['app', 'database', 'payment', 'integrations'] as $name) {
    $configFile = $projectRoot . '/config/' . $name . '.php';
    if (is_file($configFile)) {
        $config[$name] = require $configFile;
    }
}

if (is_file($projectRoot . '/config/local.php')) {
    $local = require $projectRoot . '/config/local.php';
    if (is_array($local)) {
        $config = array_replace_recursive($config, $local);
    }
}

// 3. Set timezone and error reporting
$timezone = $config['app']['timezone'] ?? 'Asia/Ho_Chi_Minh';
date_default_timezone_set($timezone);

$env = $config['app']['env'] ?? 'development';
if ($env === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// 4. Initialize session if running in web context
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

// 5. Initialize database connection singleton
if (!empty($config['database'])) {
    \Core\Database::init($config['database']);
}

// 6. Initialize Router and register application routes
$router = new \Core\Router();

// Load web, api, and callback routes
$webRoutes = require $projectRoot . '/routes/web.php';
if (is_callable($webRoutes)) {
    $webRoutes($router);
}

$apiRoutes = require $projectRoot . '/routes/api.php';
if (is_callable($apiRoutes)) {
    $apiRoutes($router);
}

$callbackRoutes = require $projectRoot . '/routes/callbacks.php';
if (is_callable($callbackRoutes)) {
    $callbackRoutes($router);
}

return [
    'config' => $config,
    'router' => $router,
];
