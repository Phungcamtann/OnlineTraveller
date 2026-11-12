<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap/app.php';

$request = new \Core\Request();

try {
    $app['router']->dispatch($request);
} catch (\Throwable $e) {
    http_response_code(500);

    if ($request->isAjax()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => 500,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $isDev = ($app['config']['app']['env'] ?? 'development') === 'development';
    $errorMessage = $isDev ? ($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()) : null;

    $errorView = dirname(__DIR__) . '/app/views/errors/500.php';
    if (is_file($errorView)) {
        include $errorView;
        exit;
    }

    header('Content-Type: text/plain; charset=utf-8');
    echo "500 Server Error: " . $e->getMessage();
    exit;
}
