<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Database;

class AdminController extends BaseController
{
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
}

if (!class_exists('AdminController', false)) {
    class_alias(AdminController::class, 'AdminController');
}
