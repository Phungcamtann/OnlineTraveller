<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Database;
use Core\Request;
use Core\Response;
use App\Models\User;
use App\Models\Refund;
use App\Models\Service;
use Exception;

class AdminController extends BaseController
{
    private User $userModel;
    private Refund $refundModel;
    private Service $serviceModel;

    public function __construct(
        ?Request $request = null,
        ?Response $response = null,
        ?User $userModel = null,
        ?Refund $refundModel = null,
        ?Service $serviceModel = null
    ) {
        parent::__construct($request, $response);
        $this->userModel = $userModel ?? new User();
        $this->refundModel = $refundModel ?? new Refund();
        $this->serviceModel = $serviceModel ?? new Service();
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

    /**
     * Admin view and manage cancellation refund requests (GET /admin/refunds).
     */
    public function refunds(Request $request): void
    {
        $status = $request->query('status');
        $refunds = $this->refundModel->getAllRefunds($status);
        $stats = $this->refundModel->getStats();

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->response->json([
                'success' => true,
                'refunds' => $refunds,
                'stats' => $stats,
            ]);
            return;
        }

        $this->view('admin/refunds/index', [
            'title' => 'Quản Lý Hồ Sơ Hoàn Tiền — Admin',
            'refunds' => $refunds,
            'stats' => $stats,
            'currentStatus' => $status,
            'user' => $this->user(),
        ]);
    }

    /**
     * Admin approves a refund request (POST /admin/refunds/{id}/approve).
     */
    public function approveRefund(Request $request, string $id): void
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

        $refundId = (int)$id;
        $refund = $this->refundModel->getById($refundId);
        if (!$refund) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Không tìm thấy hồ sơ hoàn tiền.'], 404);
                return;
            }
            $this->flash('danger', 'Không tìm thấy hồ sơ hoàn tiền.');
            $this->redirect('/admin/refunds');
            return;
        }

        $adminNote = (string)$request->input('admin_note', 'Quản trị viên đã phê duyệt hoàn tiền MoMo Sandbox.');

        try {
            $this->refundModel->updateStatus($refundId, 'approved', $adminNote);

            $message = "Đã phê duyệt hoàn tiền thành công cho hồ sơ #{$refund['refund_code']}.";
            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'message' => $message,
                    'refund_id' => $refundId,
                ]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect('/admin/refunds');
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Lỗi khi phê duyệt: ' . $e->getMessage()], 500);
                return;
            }
            $this->flash('danger', 'Lỗi khi phê duyệt: ' . $e->getMessage());
            $this->redirect('/admin/refunds');
            return;
        }
    }

    /**
     * Admin Service Management: List, search, filter and moderate tours.
     */
    public function services(Request $request): void
    {
        $admin = $this->user();
        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
                return;
            }
            $this->flash('danger', 'Bạn không có quyền truy cập khu vực quản trị.');
            $this->redirect('/login');
            return;
        }

        $db = Database::getInstance();
        $status = $request->query('status');
        $status = !empty($status) && in_array($status, ['pending', 'approved', 'rejected', 'draft', 'archived'], true) ? (string)$status : null;
        $search = $request->query('q');
        $search = !empty($search) ? trim((string)$search) : null;

        $sql = "
            SELECT s.*, c.name AS category_name, u.name AS host_name, u.email AS host_email, u.phone AS host_phone
            FROM tbl_services s
            JOIN tbl_categories c ON s.category_id = c.id
            JOIN tbl_users u ON s.host_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if ($status !== null) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        if ($search !== null) {
            $sql .= " AND (s.title LIKE ? OR s.location LIKE ? OR u.name LIKE ?)";
            $wildcard = "%{$search}%";
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY s.created_at DESC";

        $services = $db->fetchAll($sql, $params);

        $counts = [
            'all' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_services")['c'],
            'pending' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_services WHERE status = 'pending'")['c'],
            'approved' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_services WHERE status = 'approved'")['c'],
            'rejected' => (int)$db->fetchOne("SELECT COUNT(*) AS c FROM tbl_services WHERE status = 'rejected'")['c'],
        ];

        $this->view('admin/services/index', [
            'title' => 'Quản Lý & Kiểm Duyệt Trải Nghiệm — Admin',
            'services' => $services,
            'counts' => $counts,
            'currentStatus' => $status,
            'search' => $search,
            'user' => $this->user(),
        ]);
    }

    /**
     * Admin moderate tour status: approve, reject, or archive (POST /admin/services/{id}/moderate).
     */
    public function moderateService(Request $request, string $id): void
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
        $serviceId = (int)$id;
        $status = (string)$request->input('status', 'approved');
        $rejectionReason = $request->input('rejection_reason');
        $rejectionReason = !empty($rejectionReason) ? trim((string)$rejectionReason) : null;

        if (!in_array($status, ['approved', 'rejected', 'pending', 'archived'], true)) {
            $this->flash('danger', 'Trạng thái kiểm duyệt không hợp lệ.');
            $this->redirect('/admin');
            return;
        }

        try {
            $updated = $this->serviceModel->moderate($adminId, $serviceId, $status, $rejectionReason);

            $actionText = match($status) {
                'approved' => 'đã phê duyệt mở bán thành công',
                'rejected' => 'đã từ chối phê duyệt',
                'pending' => 'đã đưa về trạng thái chờ duyệt',
                'archived' => 'đã lưu trữ tour',
                default => 'đã cập nhật trạng thái',
            };

            $message = "Gói trải nghiệm #{$serviceId} {$actionText}!";

            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'message' => $message,
                    'service' => $updated,
                ]);
                return;
            }

            $this->flash('success', $message);
            
            // Redirect back to referring page or services list
            $referer = $_SERVER['HTTP_REFERER'] ?? '/admin';
            $this->redirect($referer);
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Lỗi khi kiểm duyệt: ' . $e->getMessage()], 500);
                return;
            }
            $this->flash('danger', 'Lỗi khi kiểm duyệt tour: ' . $e->getMessage());
            $this->redirect('/admin');
            return;
        }
    }

    /**
     * Admin Banner Management: Choose 4 approved tours to feature on homepage banner and exploration.
     */
    public function banner(Request $request): void
    {
        $admin = $this->user();
        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            $this->flash('danger', 'Bạn không có quyền truy cập khu vực quản trị.');
            $this->redirect('/login');
            return;
        }

        $db = Database::getInstance();
        $services = $db->fetchAll("
            SELECT s.*, c.name AS category_name, u.name AS host_name
            FROM tbl_services s
            JOIN tbl_categories c ON s.category_id = c.id
            JOIN tbl_users u ON s.host_id = u.id
            WHERE s.status = 'approved'
            ORDER BY s.is_banner DESC, s.id ASC
        ");

        $currentBannerTours = array_values(array_filter($services, fn($s) => !empty($s['is_banner'])));

        $this->view('admin/services/banner', [
            'title' => 'Quản Lý Banner Trang Chủ — OnlineTraveller Admin',
            'services' => $services,
            'currentBannerTours' => $currentBannerTours,
            'user' => $admin,
        ]);
    }

    /**
     * Save 4 selected tours for homepage banner.
     */
    public function updateBanner(Request $request): void
    {
        $admin = $this->user();
        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            $this->flash('danger', 'Bạn không có quyền thực hiện thao tác này.');
            $this->redirect('/login');
            return;
        }

        $selectedIds = $request->input('banner_tour_ids', []);
        if (!is_array($selectedIds)) {
            $selectedIds = [];
        }

        if (count($selectedIds) > 4) {
            $this->flash('warning', 'Hệ thống chỉ cho phép chọn tối đa 4 tour cho Banner. 4 tour đầu tiên đã được lưu.');
            $selectedIds = array_slice($selectedIds, 0, 4);
        } elseif (count($selectedIds) === 0) {
            $this->flash('warning', 'Bạn chưa tick chọn tour nào. Vui lòng tick chọn từ 1 đến 4 tour.');
            $this->redirect('/admin/banner');
            return;
        }

        $this->serviceModel->updateBannerServices($selectedIds);

        $this->flash('success', 'Đã cập nhật thành công 4 tour lên Banner và vùng Khám phá Trang chủ!');
        $this->redirect('/admin/banner');
    }
}

if (!class_exists('AdminController', false)) {
    class_alias(AdminController::class, 'AdminController');
}
