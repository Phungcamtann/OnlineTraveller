<?php

declare(strict_types=1);

use Core\Router;
use App\Controllers\PaymentController;

return function (Router $router): void {
    // 1. MoMo Return URL (Tourist browser redirect)
    $router->get('/payments/callback', [PaymentController::class, 'callback']);

    // 2. MoMo IPN (Server-to-server webhook - HMAC-SHA256 verified)
    $router->post('/payments/ipn', [PaymentController::class, 'ipn']);
};
