<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['user'])) {
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Vui lòng đăng nhập để tiếp tục.'], 401);
            }

            $_SESSION['flash']['warning'][] = 'Vui lòng đăng nhập để tiếp tục.';
            (new Response())->redirect('/login');
        }

        return $next($request);
    }
}

if (!class_exists('AuthMiddleware', false)) {
    class_alias(AuthMiddleware::class, 'AuthMiddleware');
}
