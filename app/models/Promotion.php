<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

/**
 * Promotion Model: Manages discount coupons, campaigns, and service applicability.
 */
class Promotion extends BaseModel
{
    /**
     * Find promotion by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM tbl_promotions WHERE id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Find promotion by promo code.
     */
    public function getByCode(string $code): ?array
    {
        $sql = "SELECT * FROM tbl_promotions WHERE UPPER(code) = UPPER(?) LIMIT 1";
        return $this->fetchOne($sql, [trim($code)]);
    }

    /**
     * Find active promotion by code that is currently valid within start/end dates.
     */
    public function getActiveByCode(string $code): ?array
    {
        $sql = "
            SELECT * FROM tbl_promotions 
            WHERE UPPER(code) = UPPER(?) 
              AND is_active = 1 
              AND NOW(6) BETWEEN starts_at AND ends_at
            LIMIT 1
        ";
        return $this->fetchOne($sql, [trim($code)]);
    }

    /**
     * Get all active promotions valid for a specific tour service.
     */
    public function getActiveForService(int $serviceId): array
    {
        $sql = "
            SELECT p.*
            FROM tbl_promotions p
            JOIN tbl_promotion_services ps ON p.id = ps.promotion_id
            WHERE ps.service_id = ?
              AND p.is_active = 1
              AND NOW(6) BETWEEN p.starts_at AND p.ends_at
            ORDER BY p.discount_percent DESC, p.id DESC
        ";
        return $this->fetchAll($sql, [$serviceId]);
    }

    /**
     * Get the best (highest discount percent) active promotion for a tour service.
     */
    public function getBestPromotionForService(int $serviceId): ?array
    {
        $promotions = $this->getActiveForService($serviceId);
        return $promotions[0] ?? null;
    }

    /**
     * Check if a promo code is valid for a specific tour service.
     */
    public function isValidForService(string $code, int $serviceId): ?array
    {
        $promo = $this->getActiveByCode($code);
        if (!$promo) {
            return null;
        }

        $sql = "
            SELECT 1 FROM tbl_promotion_services 
            WHERE promotion_id = ? AND service_id = ?
            LIMIT 1
        ";
        $attached = $this->fetchOne($sql, [(int)$promo['id'], $serviceId]);

        return $attached ? $promo : null;
    }

    /**
     * Get all promotions (used in Admin management).
     */
    public function getAll(): array
    {
        $sql = "
            SELECT p.*, COUNT(ps.service_id) AS services_count
            FROM tbl_promotions p
            LEFT JOIN tbl_promotion_services ps ON p.id = ps.promotion_id
            GROUP BY p.id
            ORDER BY p.id DESC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Save promotion (create or update) via Stored Procedure pct_sp_promotions_save.
     */
    public function save(
        int $adminId,
        ?int $id,
        string $code,
        string $title,
        float $discountPercent,
        string $startsAt,
        string $endsAt,
        int $isActive = 1
    ): array {
        $rows = $this->callProcedure('pct_sp_promotions_save', [
            $adminId,
            $id ?? 0,
            strtoupper(trim($code)),
            trim($title),
            $discountPercent,
            $startsAt,
            $endsAt,
            $isActive,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Attach a promotion to a tour service.
     */
    public function attachService(int $promotionId, int $serviceId): bool
    {
        $sql = "INSERT IGNORE INTO tbl_promotion_services (promotion_id, service_id) VALUES (?, ?)";
        return $this->execute($sql, [$promotionId, $serviceId]) >= 0;
    }

    /**
     * Detach a promotion from a tour service.
     */
    public function detachService(int $promotionId, int $serviceId): bool
    {
        $sql = "DELETE FROM tbl_promotion_services WHERE promotion_id = ? AND service_id = ?";
        return $this->execute($sql, [$promotionId, $serviceId]) > 0;
    }

    /**
     * Get all services linked to a promotion.
     */
    public function getLinkedServices(int $promotionId): array
    {
        $sql = "
            SELECT s.id, s.title, s.slug, s.base_price, s.price_mode, s.location
            FROM tbl_services s
            JOIN tbl_promotion_services ps ON s.id = ps.service_id
            WHERE ps.promotion_id = ?
            ORDER BY s.id ASC
        ";
        return $this->fetchAll($sql, [$promotionId]);
    }
}

if (!class_exists('Promotion', false)) {
    class_alias(Promotion::class, 'Promotion');
}
