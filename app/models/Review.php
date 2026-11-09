<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;
use RuntimeException;

/**
 * Review Model: Post-trip star ratings and reviews (tbl_reviews).
 * Uses Stored Procedure pct_sp_reviews_create for persistence.
 */
class Review extends BaseModel
{
    /**
     * Submit a review for a completed tour booking via pct_sp_reviews_create.
     */
    public function createReview(int $touristId, int $bookingId, int $rating, string $comment): array
    {
        if ($rating < 1 || $rating > 5) {
            throw new RuntimeException('Số sao đánh giá phải từ 1 đến 5 sao.');
        }

        $comment = trim($comment);
        if ($comment === '') {
            throw new RuntimeException('Vui lòng nhập nhận xét về chuyến đi của bạn.');
        }

        // Verify booking status
        $booking = $this->fetchOne("SELECT tourist_id, service_id, status FROM tbl_bookings WHERE id = ?", [$bookingId]);
        if (!$booking) {
            throw new RuntimeException('Không tìm thấy đơn đặt tour yêu cầu.');
        }

        if ((int)$booking['tourist_id'] !== $touristId) {
            throw new RuntimeException('FORBIDDEN_NOT_BOOKING_OWNER');
        }

        if ($booking['status'] !== 'completed') {
            throw new RuntimeException('Bạn chỉ có thể đánh giá sau khi chuyến đi đã hoàn thành (completed).');
        }

        // Check if already reviewed
        $existing = $this->getByBookingId($bookingId);
        if ($existing) {
            throw new RuntimeException('Bạn đã gửi đánh giá cho chuyến đi này rồi.');
        }

        $rows = $this->callProcedure('pct_sp_reviews_create', [
            $touristId,
            $bookingId,
            $rating,
            $comment,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Get review by booking ID.
     */
    public function getByBookingId(int $bookingId): ?array
    {
        $sql = "
            SELECT r.*, u.name AS tourist_name, u.avatar AS tourist_avatar
            FROM tbl_reviews r
            JOIN tbl_users u ON r.tourist_id = u.id
            WHERE r.booking_id = ?
            LIMIT 1
        ";
        return $this->fetchOne($sql, [$bookingId]);
    }

    /**
     * Get review by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT r.*, u.name AS tourist_name, u.avatar AS tourist_avatar, s.title AS service_title
            FROM tbl_reviews r
            JOIN tbl_users u ON r.tourist_id = u.id
            JOIN tbl_services s ON r.service_id = s.id
            WHERE r.id = ?
            LIMIT 1
        ";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Get visible reviews for a service.
     */
    public function getByService(int $serviceId, int $limit = 10, int $offset = 0): array
    {
        $sql = "
            SELECT r.*, u.name AS tourist_name, u.avatar AS tourist_avatar
            FROM tbl_reviews r
            JOIN tbl_users u ON r.tourist_id = u.id
            WHERE r.service_id = ? AND r.is_visible = 1
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ";
        return $this->fetchAll($sql, [$serviceId, $limit, $offset]);
    }

    /**
     * Get review stats for a service (average rating and total count).
     */
    public function getServiceRatingStats(int $serviceId): array
    {
        $sql = "
            SELECT 
                COALESCE(ROUND(AVG(rating), 1), 5.0) AS avg_rating,
                COUNT(id) AS reviews_count,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS count_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS count_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS count_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS count_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS count_1
            FROM tbl_reviews
            WHERE service_id = ? AND is_visible = 1
        ";
        return $this->fetchOne($sql, [$serviceId]) ?? [
            'avg_rating' => 5.0,
            'reviews_count' => 0,
        ];
    }
}

if (!class_exists('Review', false)) {
    class_alias(Review::class, 'Review');
}
