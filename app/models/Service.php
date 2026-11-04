<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

class Service extends BaseModel
{
    /**
     * Get all approved experiences with host info, category, and average rating.
     */
    public function getAllApproved(?int $categoryId = null, ?string $keyword = null, int $limit = 12): array
    {
        $sql = "
            SELECT 
                s.id, s.host_id, s.category_id, s.title, s.slug, s.description,
                s.price_mode, s.base_price, s.min_price, s.max_price,
                s.location, s.latitude, s.longitude, s.duration_days, s.banner_image, s.status,
                c.name AS category_name, c.icon AS category_icon,
                u.name AS host_name, u.living_area AS host_living_area, u.avatar AS host_avatar,
                COALESCE(AVG(r.rating), 5.0) AS avg_rating,
                COUNT(r.id) AS reviews_count,
                acc.homestay_name
            FROM tbl_services s
            JOIN tbl_categories c ON s.category_id = c.id
            JOIN tbl_users u ON s.host_id = u.id
            LEFT JOIN tbl_reviews r ON s.id = r.service_id AND r.is_visible = 1
            LEFT JOIN tbl_service_accommodations acc ON s.id = acc.service_id
            WHERE s.status = 'approved'
        ";

        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND s.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($keyword)) {
            $sql .= " AND (s.title LIKE ? OR s.location LIKE ?)";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        $sql .= " GROUP BY s.id ORDER BY avg_rating DESC, s.created_at DESC LIMIT " . (int)$limit;

        return $this->fetchAll($sql, $params);
    }

    /**
     * Get the top-rated experience to feature in the Hero Banner.
     */
    public function getHeroFeatured(): ?array
    {
        $items = $this->getAllApproved(null, null, 1);
        return empty($items) ? null : $items[0];
    }

    /**
     * Get single experience detail by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT 
                s.*,
                c.name AS category_name, c.icon AS category_icon,
                u.name AS host_name, u.email AS host_email, u.phone AS host_phone,
                u.living_area AS host_living_area, u.bio AS host_bio, u.avatar AS host_avatar,
                COALESCE(AVG(r.rating), 5.0) AS avg_rating,
                COUNT(r.id) AS reviews_count,
                acc.homestay_name, acc.address AS homestay_address,
                acc.room_type AS homestay_room_type, acc.nights AS homestay_nights, acc.notes AS homestay_notes
            FROM tbl_services s
            JOIN tbl_categories c ON s.category_id = c.id
            JOIN tbl_users u ON s.host_id = u.id
            LEFT JOIN tbl_reviews r ON s.id = r.service_id AND r.is_visible = 1
            LEFT JOIN tbl_service_accommodations acc ON s.id = acc.service_id
            WHERE s.id = ?
            GROUP BY s.id
            LIMIT 1
        ";

        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Get 3D Virtual Tour Stops for Three.js Z-Scroll.
     */
    public function getStops(int $serviceId): array
    {
        $sql = "
            SELECT id, service_id, image_url, caption, story_text, video_url, sort_order
            FROM tbl_service_images
            WHERE service_id = ?
            ORDER BY sort_order ASC
        ";

        return $this->fetchAll($sql, [$serviceId]);
    }

    /**
     * Get active categories.
     */
    public function getCategories(): array
    {
        $sql = "SELECT id, name, slug, description, icon FROM tbl_categories WHERE is_active = 1 ORDER BY id ASC";
        return $this->fetchAll($sql);
    }
}

if (!class_exists('Service', false)) {
    class_alias(Service::class, 'Service');
}
