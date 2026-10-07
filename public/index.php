<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/bootstrap/app.php';

// TODO: Dispatch all page, AJAX and callback requests through Router.
// Until routing and middleware exist, expose no business actions.
http_response_code(503);
header('Content-Type: text/plain; charset=utf-8');
echo "OnlineTraveller: bộ khung đã sẵn sàng; chức năng ứng dụng chưa triển khai.\n";
