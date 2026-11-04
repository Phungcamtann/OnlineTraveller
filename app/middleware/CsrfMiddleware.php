<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;
use SecurityHelper;

class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        // CSRF verification applies to modifying HTTP methods (POST, PUT, DELETE, PATCH)
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $token = $request->csrfToken();

            if (!SecurityHelper::verifyCsrfToken($token)) {
                if ($request->isAjax()) {
                    (new Response())->json(['error' => 'Phiên làm việc hoặc mã bảo mật (CSRF) không hợp lệ. Vui lòng tải lại trang.'], 419);
                }

                http_response_code(419);
                echo "419 Page Expired: Mã bảo mật (CSRF Token) không khớp hoặc phiên làm việc đã hết hạn. Vui lòng quay lại và tải lại trang.";
                exit;
            }
        }

        return $next($request);
    }
}

if (!class_exists('CsrfMiddleware', false)) {
    class_alias(CsrfMiddleware::class, 'CsrfMiddleware');
}
