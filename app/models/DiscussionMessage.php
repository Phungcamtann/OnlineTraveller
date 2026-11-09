<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

/**
 * DiscussionMessage Model: Helper for discussion replies and threads (tbl_discussions).
 */
class DiscussionMessage extends BaseModel
{
    /**
     * Get all replies for a parent discussion topic.
     */
    public function getReplies(int $parentId): array
    {
        $sql = "
            SELECT d.*, u.name AS user_name, u.avatar AS user_avatar, u.role AS user_role
            FROM tbl_discussions d
            JOIN tbl_users u ON d.user_id = u.id
            WHERE d.parent_id = ? AND d.is_visible = 1
            ORDER BY d.created_at ASC
        ";
        return $this->fetchAll($sql, [$parentId]);
    }
}

if (!class_exists('DiscussionMessage', false)) {
    class_alias(DiscussionMessage::class, 'DiscussionMessage');
}
