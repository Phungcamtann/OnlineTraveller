<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;
use SecurityHelper;

/**
 * CSRF Protection Middleware:
 * Inspects modifying HTTP methods (POST, PUT, DELETE, PATCH)
 * for a valid CSRF token in body fields (_csrf_token, csrf_token)
 * or headers (X-CSRF-Token, X-XSRF-Token).
 * 
 * Rejects invalid or expired requests with HTTP 419 (JSON or custom view).
 */
class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        // CSRF verification applies to modifying HTTP methods (POST, PUT, DELETE, PATCH)
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $token = $request->csrfToken();

            if (!SecurityHelper::verifyCsrfToken($token)) {
                if ($request->isAjax()) {
                    (new Response())->json([
                        'error' => 'Phiên làm việc hoặc mã bảo mật (CSRF) không hợp lệ. Vui lòng tải lại trang.',
                        'code' => 419,
                    ], 419);
                    return null;
                }

                http_response_code(419);
                $errorView = dirname(__DIR__, 2) . '/app/views/errors/419.php';
                if (is_file($errorView)) {
                    include $errorView;
                    if (!Response::$preventExit) {
                        exit;
                    }
                    return null;
                }
                echo "419 Page Expired: Mã bảo mật (CSRF Token) không khớp hoặc phiên làm việc đã hết hạn. Vui lòng quay lại và tải lại trang.";
                if (!Response::$preventExit) {
                    exit;
                }
                return null;
            }
        }

        return $next($request);
    }
}

if (!class_exists('CsrfMiddleware', false)) {
    class_alias(CsrfMiddleware::class, 'CsrfMiddleware');
}
