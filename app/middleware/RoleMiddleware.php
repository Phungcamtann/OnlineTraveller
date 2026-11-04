<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;

class RoleMiddleware implements Middleware
{
    private array $allowedRoles;

    public function __construct(string ...$roles)
    {
        $this->allowedRoles = $roles;
    }

    public function handle(Request $request, Closure $next): mixed
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Chưa đăng nhập.'], 401);
            }
            (new Response())->redirect('/login');
        }

        $role = $user['role'] ?? '';
        if (!empty($this->allowedRoles) && !in_array($role, $this->allowedRoles, true)) {
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
            }

            http_response_code(403);
            $errorView = dirname(__DIR__, 2) . '/app/views/errors/403.php';
            if (is_file($errorView)) {
                include $errorView;
                exit;
            }
            echo "403 Forbidden: Bạn không có quyền truy cập trang này.";
            exit;
        }

        return $next($request);
    }
}

if (!class_exists('RoleMiddleware', false)) {
    class_alias(RoleMiddleware::class, 'RoleMiddleware');
}
