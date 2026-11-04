<?php

declare(strict_types=1);

use Core\Router;
use Core\Request;
use Core\Response;

return function (Router $router): void {
    // 1. MoMo Return URL (Tourist browser redirect)
    $router->get('/payments/callback', function (Request $request) {
        $resultCode = $request->query('resultCode');
        $orderId = $request->query('orderId');

        (new Response())->html("
            <div style='font-family: sans-serif; text-align: center; padding: 50px;'>
                <h2>Kết quả thanh toán MoMo Sandbox</h2>
                <p>Mã đơn hàng: <strong>" . htmlspecialchars((string)$orderId) . "</strong></p>
                <p>Mã phản hồi: <strong>" . htmlspecialchars((string)$resultCode) . "</strong> (" . ($resultCode === '0' ? 'Thành công' : 'Chưa hoàn tất') . ")</p>
                <a href='/' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #0284c7; color: white; text-decoration: none; border-radius: 50px;'>Về trang chủ OnlineTraveller</a>
            </div>
        ");
    });

    // 2. MoMo IPN (Server-to-server webhook)
    $router->post('/payments/ipn', function (Request $request) {
        // MoMo server sends JSON payload
        $payload = $request->json();
        
        // Return acknowledgment to MoMo
        (new Response())->json([
            'message' => 'IPN received',
            'resultCode' => 0,
        ]);
    });
};
