<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$config = require dirname(__DIR__) . '/bootstrap/app.php';
// TODO: Query unresolved MoMo attempts and process verified results through PaymentService.
fwrite(STDERR, "reconcile_payments: not implemented.\n");
exit(1);
