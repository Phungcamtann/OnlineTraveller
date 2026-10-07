<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$config = [];
foreach (['app', 'database', 'payment', 'integrations'] as $name) {
    $config[$name] = require $projectRoot . '/config/' . $name . '.php';
}
if (is_file($projectRoot . '/config/local.php')) {
    $local = require $projectRoot . '/config/local.php';
    if (!is_array($local)) {
        throw new RuntimeException('Local configuration must return an array.');
    }
    $config = array_replace_recursive($config, $local);
}
date_default_timezone_set($config['app']['timezone']);

// TODO: Register Autoloader; initialize secure browser sessions and Router.
// TODO: Load web.php, api.php and callbacks.php through the same Router.
// CLI jobs must use configuration/models/services without dispatching HTTP routes.
return $config;
