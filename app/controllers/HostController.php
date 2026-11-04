<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use Core\Database;

class HostController extends BaseController
{
    public function dashboard(): void
    {
        $user = $this->user();
        $db = Database::getInstance();

        // Fetch host's services
        $hostId = (int)($user['id'] ?? 0);
        $services = $db->fetchAll("
            SELECT s.*, c.name AS category_name, acc.homestay_name,
                   (SELECT COUNT(*) FROM tbl_service_images WHERE service_id = s.id) AS stops_count
            FROM tbl_services s
            JOIN tbl_categories c ON s.category_id = c.id
            LEFT JOIN tbl_service_accommodations acc ON s.id = acc.service_id
            WHERE s.host_id = ?
            ORDER BY s.created_at DESC
        ", [$hostId]);

        $this->view('host/dashboard', [
            'title' => 'Bảng Quản Lý Dân Bản Địa (Host) — OnlineTraveller',
            'services' => $services,
            'user' => $user,
        ]);
    }
}

if (!class_exists('HostController', false)) {
    class_alias(HostController::class, 'HostController');
}
