<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;
use App\Models\Category;

/**
 * Service Model: Tours & local cultural experiences.
 * Uses Stored Procedures for write operations and optimized queries for reads.
 */
class Service extends BaseModel
{
    /**
     * Get all approved experiences with host info, category, homestay and average rating.
     */
    public function getAllApproved(
        ?int $categoryId = null,
        ?string $keyword = null,
        int $limit = 12,
        ?int $maxPrice = null,
        ?int $minPrice = null
    ): array {
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

        if ($minPrice !== null && $minPrice > 0) {
            $sql .= " AND s.base_price >= ?";
            $params[] = $minPrice;
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $sql .= " AND s.base_price <= ?";
            $params[] = $maxPrice;
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
     * Get single experience detail by unique slug.
     */
    public function getBySlug(string $slug): ?array
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
            WHERE s.slug = ?
            GROUP BY s.id
            LIMIT 1
        ";

        return $this->fetchOne($sql, [$slug]);
    }

    /**
     * Get list of tours managed by a specific Host.
     */
    public function getByHost(int $hostId, ?string $status = null): array
    {
        $sql = "
            SELECT 
                s.*,
                c.name AS category_name, c.icon AS category_icon,
                acc.homestay_name,
                (SELECT COUNT(*) FROM tbl_service_schedules sch WHERE sch.service_id = s.id) AS schedules_count,
                (SELECT COUNT(*) FROM tbl_bookings b WHERE b.service_id = s.id AND b.status IN ('confirmed', 'completed')) AS bookings_count,
                COALESCE(AVG(r.rating), 5.0) AS avg_rating,
                COUNT(r.id) AS reviews_count
            FROM tbl_services s
            JOIN tbl_categories c ON s.category_id = c.id
            LEFT JOIN tbl_service_accommodations acc ON s.id = acc.service_id
            LEFT JOIN tbl_reviews r ON s.id = r.service_id AND r.is_visible = 1
            WHERE s.host_id = ?
        ";

        $params = [$hostId];

        if ($status !== null && $status !== '') {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        $sql .= " GROUP BY s.id ORDER BY s.created_at DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Create a new tour package via Stored Procedure ptl_sp_services_create.
     */
    public function create(
        int $hostId,
        int $categoryId,
        string $title,
        string $slug,
        string $description,
        string $priceMode,
        float $basePrice,
        float $minPrice,
        float $maxPrice,
        string $location,
        ?float $latitude,
        ?float $longitude,
        int $durationDays,
        ?string $bannerImage
    ): array {
        $rows = $this->callProcedure('ptl_sp_services_create', [
            $hostId,
            $categoryId,
            $title,
            $slug,
            $description,
            $priceMode,
            $basePrice,
            $minPrice,
            $maxPrice,
            $location,
            $latitude,
            $longitude,
            $durationDays,
            $bannerImage,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Update an existing tour package via Stored Procedure ptl_sp_services_update.
     */
    public function update(
        int $hostId,
        int $id,
        int $categoryId,
        string $title,
        string $slug,
        string $description,
        string $priceMode,
        float $basePrice,
        float $minPrice,
        float $maxPrice,
        string $location,
        ?float $latitude,
        ?float $longitude,
        int $durationDays,
        ?string $bannerImage
    ): array {
        $rows = $this->callProcedure('ptl_sp_services_update', [
            $hostId,
            $id,
            $categoryId,
            $title,
            $slug,
            $description,
            $priceMode,
            $basePrice,
            $minPrice,
            $maxPrice,
            $location,
            $latitude,
            $longitude,
            $durationDays,
            $bannerImage,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Admin review / moderate tour status via Stored Procedure ptl_sp_services_moderate.
     */
    public function moderate(int $adminId, int $id, string $status, ?string $rejectionReason = null): array
    {
        $rows = $this->callProcedure('ptl_sp_services_moderate', [
            $adminId,
            $id,
            $status,
            $rejectionReason,
        ]);

        return $rows[0] ?? [];
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
     * Add a 3D stop image / panorama stop to tour.
     */
    public function addStop(
        int $serviceId,
        string $imageUrl,
        string $caption = '',
        ?string $storyText = null,
        ?string $videoUrl = null,
        int $sortOrder = 0
    ): int {
        $sql = "
            INSERT INTO tbl_service_images (service_id, image_url, caption, story_text, video_url, sort_order, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(6))
        ";
        $this->db->query($sql, [$serviceId, $imageUrl, $caption, $storyText, $videoUrl, $sortOrder]);
        return (int)$this->db->getPdo()->lastInsertId();
    }

    /**
     * Delete a single 3D stop image.
     */
    public function deleteStop(int $stopId, int $serviceId): bool
    {
        $sql = "DELETE FROM tbl_service_images WHERE id = ? AND service_id = ?";
        $stmt = $this->db->query($sql, [$stopId, $serviceId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Clear all 3D stops of a tour.
     */
    public function clearStops(int $serviceId): bool
    {
        $sql = "DELETE FROM tbl_service_images WHERE service_id = ?";
        $stmt = $this->db->query($sql, [$serviceId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Generate an SEO-friendly and unique slug for a tour title.
     */
    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Category::slugify($title);
        if ($baseSlug === '') {
            $baseSlug = 'tour-' . time();
        }

        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM tbl_services WHERE slug = ?";
            $params = [$slug];
            if ($ignoreId !== null && $ignoreId > 0) {
                $sql .= " AND id != ?";
                $params[] = $ignoreId;
            }

            $exists = $this->fetchOne($sql, $params);
            if (!$exists) {
                return $slug;
            }

            $counter++;
            $slug = "{$baseSlug}-{$counter}";
        }
    }

    /**
     * Get active categories for tour dropdown.
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
