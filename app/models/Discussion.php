<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;
use RuntimeException;

/**
 * Discussion Model: 1-level tour inquiries and host replies (tbl_discussions).
 * Uses Stored Procedure pct_sp_discussions_create for creation.
 */
class Discussion extends BaseModel
{
    /**
     * Create discussion topic or 1-level reply via pct_sp_discussions_create.
     */
    public function createDiscussion(int $userId, int $serviceId, string $message, ?int $parentId = null): array
    {
        $message = trim($message);
        if ($message === '') {
            throw new RuntimeException('Nội dung thảo luận không được để trống.');
        }

        if ($parentId !== null && $parentId > 0) {
            $parent = $this->getById($parentId);
            if (!$parent) {
                throw new RuntimeException('Không tìm thấy chủ đề gốc để trả lời.');
            }
            if ((int)$parent['service_id'] !== $serviceId) {
                throw new RuntimeException('MISMATCH_DISCUSSION_PARENT');
            }
            if ((int)$parent['is_visible'] !== 1) {
                throw new RuntimeException('Chủ đề đã bị ẩn, không thể gửi câu trả lời.');
            }
            // Enforce single-level hierarchy: reply cannot be attached to another reply
            if (!empty($parent['parent_id'])) {
                throw new RuntimeException('Hệ thống chỉ hỗ trợ thảo luận 1 cấp (không thể trả lời lồng nhau).');
            }
        }

        $rows = $this->callProcedure('pct_sp_discussions_create', [
            $userId,
            $serviceId,
            $parentId && $parentId > 0 ? $parentId : null,
            $message,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Get discussion by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT d.*, u.name AS user_name, u.avatar AS user_avatar, u.role AS user_role
            FROM tbl_discussions d
            JOIN tbl_users u ON d.user_id = u.id
            WHERE d.id = ?
            LIMIT 1
        ";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Get discussions grouped by root questions and their 1-level replies for a tour service.
     *
     * @param int $serviceId ID gói tour
     * @param bool $onlyVisible Chỉ lấy câu hỏi hiển thị công khai
     * @return array Danh sách câu hỏi kèm mảng 'replies' lồng 1 cấp
     */
    public function getDiscussionsByService(int $serviceId, bool $onlyVisible = true): array
    {
        // 1. Get root discussions (parent_id IS NULL)
        $sqlRoots = "
            SELECT 
                d.*,
                u.name AS user_name,
                u.avatar AS user_avatar,
                u.role AS user_role,
                u.living_area AS user_living_area
            FROM tbl_discussions d
            JOIN tbl_users u ON d.user_id = u.id
            WHERE d.service_id = ? AND d.parent_id IS NULL
        ";
        if ($onlyVisible) {
            $sqlRoots .= " AND d.is_visible = 1";
        }
        $sqlRoots .= " ORDER BY d.created_at DESC";

        $roots = $this->fetchAll($sqlRoots, [$serviceId]);
        if (empty($roots)) {
            return [];
        }

        $rootIds = array_column($roots, 'id');
        $placeholders = implode(',', array_fill(0, count($rootIds), '?'));

        // 2. Get replies for these root questions
        $sqlReplies = "
            SELECT 
                d.*,
                u.name AS user_name,
                u.avatar AS user_avatar,
                u.role AS user_role,
                u.living_area AS user_living_area
            FROM tbl_discussions d
            JOIN tbl_users u ON d.user_id = u.id
            WHERE d.parent_id IN ({$placeholders})
        ";
        if ($onlyVisible) {
            $sqlReplies .= " AND d.is_visible = 1";
        }
        $sqlReplies .= " ORDER BY d.created_at ASC";

        $replies = $this->fetchAll($sqlReplies, $rootIds);

        // Group replies by parent_id
        $repliesByParent = [];
        foreach ($replies as $reply) {
            $parentId = (int)$reply['parent_id'];
            $repliesByParent[$parentId][] = $reply;
        }

        // Attach replies to each root
        foreach ($roots as &$root) {
            $rootId = (int)$root['id'];
            $root['replies'] = $repliesByParent[$rootId] ?? [];
            $root['replies_count'] = count($root['replies']);
        }
        unset($root);

        return $roots;
    }

    /**
     * Hide discussion (author or admin).
     */
    public function hideDiscussion(int $id, int $actorId, bool $isAdmin = false): bool
    {
        $discussion = $this->getById($id);
        if (!$discussion) {
            return false;
        }

        if (!$isAdmin && (int)$discussion['user_id'] !== $actorId) {
            throw new RuntimeException('FORBIDDEN');
        }

        $sql = "UPDATE tbl_discussions SET is_visible = 0 WHERE id = ?";
        return $this->execute($sql, [$id]) > 0;
    }

    /**
     * Count visible discussions for a service.
     */
    public function countByService(int $serviceId): int
    {
        $sql = "SELECT COUNT(*) AS c FROM tbl_discussions WHERE service_id = ? AND is_visible = 1";
        return (int)($this->fetchOne($sql, [$serviceId])['c'] ?? 0);
    }
}

if (!class_exists('Discussion', false)) {
    class_alias(Discussion::class, 'Discussion');
}
