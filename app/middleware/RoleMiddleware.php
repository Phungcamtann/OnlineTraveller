<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;
use Core\Response;
use App\Models\User;

/**
 * RBAC RoleMiddleware: Enforces role permissions and account status verification.
 * 
 * - Unauthenticated users are rejected with HTTP 401 (AJAX) or redirected to /login (Web).
 * - Locked/Inactive accounts have their session revoked and are denied with HTTP 403 (AJAX) or redirected to /login with flash error (Web).
 * - Authenticated users lacking required roles receive HTTP 403 (AJAX JSON or 403 error page).
 */
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
                    $user = $_SESSION['user'];
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

        // Verify user role
        $role = $user['role'] ?? '';
        if (!empty($this->allowedRoles) && !in_array($role, $this->allowedRoles, true)) {
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
                return null;
            }

            http_response_code(403);
            $errorView = dirname(__DIR__, 2) . '/app/views/errors/403.php';
            if (is_file($errorView)) {
                include $errorView;
                if (!Response::$preventExit) {
                    exit;
                }
                return null;
            }
            echo "403 Forbidden: Bạn không có quyền truy cập trang này.";
            if (!Response::$preventExit) {
                exit;
            }
            return null;
        }

        return $next($request);
    }
}

if (!class_exists('RoleMiddleware', false)) {
    class_alias(RoleMiddleware::class, 'RoleMiddleware');
}
