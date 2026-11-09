<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;
use App\Models\User;

/**
 * Authentication Middleware: Verifies user session and active status.
 */
class AuthMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;
        if (empty($user)) {
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Vui lòng đăng nhập để tiếp tục.'], 401);
                return null;
            }

            $_SESSION['flash']['warning'][] = 'Vui lòng đăng nhập để tiếp tục.';
            (new Response())->redirect('/login');
            return null;
        }

        // Verify account active status (refresh from DB if available)
        $status = $user['status'] ?? 'active';
        $userId = (int)($user['id'] ?? 0);
        if ($userId > 0 && class_exists(User::class)) {
            try {
                $dbUser = (new User())->getById($userId);
                if ($dbUser) {
                    $status = $dbUser['status'] ?? $status;
                    $_SESSION['user']['status'] = $status;
                    $_SESSION['user']['role'] = $dbUser['role'] ?? ($_SESSION['user']['role'] ?? '');
                }
            } catch (\Throwable $e) {
                // If database query fails, fall back to current session status
            }
        }

        if ($status === 'locked' || $status === 'inactive') {
            unset($_SESSION['user']);
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Tài khoản của bạn đã bị khóa hoặc chưa kích hoạt.'], 403);
                return null;
            }

            $_SESSION['flash']['danger'][] = 'Tài khoản của bạn đã bị khóa bởi Quản trị viên.';
            (new Response())->redirect('/login');
            return null;
        }

        return $next($request);
    }
}

if (!class_exists('AuthMiddleware', false)) {
    class_alias(AuthMiddleware::class, 'AuthMiddleware');
}
