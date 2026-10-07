<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$config = require dirname(__DIR__) . '/bootstrap/app.php';
// TODO: Expire unpaid bookings and release held seats once through BookingService.
fwrite(STDERR, "expire_bookings: not implemented.\n");
exit(1);
