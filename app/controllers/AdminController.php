<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Database;
use Core\Request;
use App\Models\User;
use Exception;

class AdminController extends BaseController
{
    private User $userModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->userModel = new User();
    }

    public function dashboard(): void
    {
        $db = Database::getInstance();

        $stats = [
            'users' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_users")['c'],
            'services' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_services")['c'],
            'pending_services' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_services WHERE status = 'pending'")['c'],
            'categories' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_categories")['c'],
            'bookings' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_bookings")['c'],
        ];

        $recentServices = $db->fetchAll("
            SELECT s.*, c.name AS category_name, u.name AS host_name
            FROM tbl_services s
            JOIN tbl_categories c ON s.category_id = c.id
            JOIN tbl_users u ON s.host_id = u.id
            ORDER BY s.created_at DESC
            LIMIT 10
        ");

        $this->view('admin/dashboard', [
            'title' => 'Bảng Điều Khiển Quản Trị Hệ Thống (Admin) — OnlineTraveller',
            'stats' => $stats,
            'recentServices' => $recentServices,
            'user' => $this->user(),
        ]);
    }

    /**
     * Admin User Management: List, search and filter users.
     */
    public function users(Request $request): void
    {
        $role = $request->query('role');
        $role = !empty($role) && in_array($role, ['tourist', 'host', 'admin'], true) ? (string)$role : null;

        $status = $request->query('status');
        $status = !empty($status) && in_array($status, ['active', 'inactive', 'locked'], true) ? (string)$status : null;

        $search = $request->query('q');
        $search = !empty($search) ? trim((string)$search) : null;

        $page = max(1, (int)$request->query('page', 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $totalUsers = $this->userModel->countUsers($role, $status, $search);
        $users = $this->userModel->getAllUsers($role, $status, $search, $perPage, $offset);
        $stats = $this->userModel->getUserStats();
        $totalPages = max(1, (int)ceil($totalUsers / $perPage));

        $this->view('admin/users/index', [
            'title' => 'Quản Lý Người Dùng & Phân Quyền — OnlineTraveller Admin',
            'users' => $users,
            'totalUsers' => $totalUsers,
            'stats' => $stats,
            'filters' => [
                'role' => $role,
                'status' => $status,
                'search' => $search,
                'page' => $page,
                'totalPages' => $totalPages,
            ],
            'currentUser' => $this->user(),
        ]);
    }

    /**
     * Admin Lock / Unlock User Account via dhs_sp_users_set_status.
     */
    public function setUserStatus(Request $request, string $id): void
    {
        $admin = $this->user();
        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
                return;
            }
            $this->flash('danger', 'Bạn không có quyền thực hiện thao tác này.');
            $this->redirect('/login');
            return;
        }

        $adminId = (int)$admin['id'];
        $targetUserId = (int)$id;
        $newStatus = (string)$request->input('status', 'active');

        if (!in_array($newStatus, ['active', 'locked', 'inactive'], true)) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Trạng thái không hợp lệ.'], 400);
                return;
            }
            $this->flash('danger', 'Trạng thái không hợp lệ.');
            $this->redirect('/admin/users');
            return;
        }

        // Prevent admin from locking their own account
        if ($targetUserId === $adminId && $newStatus === 'locked') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Bạn không thể tự khóa tài khoản quản trị của chính mình!'], 400);
                return;
            }
            $this->flash('danger', 'Bạn không thể tự khóa tài khoản quản trị của chính mình!');
            $this->redirect('/admin/users');
            return;
        }

        // Check if user exists
        $targetUser = $this->userModel->getById($targetUserId);
        if (!$targetUser) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Người dùng không tồn tại.'], 404);
                return;
            }
            $this->flash('danger', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        try {
            $updated = $this->userModel->setStatus($adminId, $targetUserId, $newStatus);

            $statusText = match($newStatus) {
                'locked' => 'đã khóa tài khoản thành công',
                'active' => 'đã kích hoạt / mở khóa tài khoản thành công',
                'inactive' => 'đã chuyển tài khoản sang trạng thái chưa kích hoạt',
                default => 'đã cập nhật trạng thái tài khoản',
            };

            $message = "Người dùng {$targetUser['name']} (ID #{$targetUserId}) {$statusText}.";

            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'message' => $message,
                    'user' => $updated,
                    'status' => $newStatus,
                ]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect('/admin/users');
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()], 500);
                return;
            }
            $this->flash('danger', 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage());
            $this->redirect('/admin/users');
            return;
        }
    }
}

if (!class_exists('AdminController', false)) {
    class_alias(AdminController::class, 'AdminController');
}
