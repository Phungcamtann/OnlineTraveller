<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;
use RuntimeException;

/**
 * Post Model: Travel blog guides & news articles (tbl_posts).
 * Uses Stored Procedures pct_sp_posts_save and pct_sp_posts_delete.
 */
class Post extends BaseModel
{
    /**
     * Save (insert or update) article via Stored Procedure pct_sp_posts_save.
     */
    public function savePost(
        int $adminId,
        ?int $id,
        string $title,
        string $slug,
        string $content,
        ?string $thumbnail = null,
        int $isPublished = 1
    ): array {
        $title = trim($title);
        $slug = trim($slug);
        $content = trim($content);

        if ($title === '') {
            throw new RuntimeException('Tiêu đề bài viết không được để trống.');
        }

        if ($slug === '') {
            $slug = $this->generateUniqueSlug($title, $id);
        }

        $rows = $this->callProcedure('pct_sp_posts_save', [
            $adminId,
            $id && $id > 0 ? $id : null,
            $title,
            $slug,
            $content,
            $thumbnail,
            $isPublished,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Create community article for any authenticated user (Tourist, Host, Admin).
     */
    public function createCommunityPost(
        int $userId,
        string $title,
        ?string $slug = null,
        string $content = '',
        ?string $thumbnail = null
    ): array {
        $title = trim($title);
        $content = trim($content);

        if (mb_strlen($title, 'UTF-8') < 5) {
            throw new RuntimeException('Tiêu đề bài viết phải có ít nhất 5 ký tự.');
        }

        if (mb_strlen($content, 'UTF-8') < 20) {
            throw new RuntimeException('Nội dung bài viết phải có ít nhất 20 ký tự để chia sẻ trải nghiệm.');
        }

        $slug = !empty($slug) ? trim($slug) : $this->generateUniqueSlug($title);

        $rows = $this->callProcedure('pct_sp_community_posts_create', [
            $userId,
            $title,
            $slug,
            $content,
            $thumbnail,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Handle article thumbnail upload.
     *
     * @param array $file $_FILES['thumbnail_file']
     * @return string Relative URL path to saved image
     * @throws RuntimeException
     */
    public function handleThumbnailUpload(array $file): string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Dữ liệu tệp ảnh tải lên không hợp lệ.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Lỗi tải tệp lên máy chủ: Mã lỗi ' . $file['error']);
        }

        // Max 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Dung lượng ảnh vượt quá giới hạn cho phép (tối đa 5MB).');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!array_key_exists($mimeType, $allowedMimes)) {
            throw new RuntimeException('Định dạng ảnh không được hỗ trợ. Vui lòng chọn ảnh JPG, PNG hoặc WEBP.');
        }

        $ext = $allowedMimes[$mimeType];
        $filename = 'post_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

        $projectRoot = dirname(__DIR__, 2);
        $targetDir = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'posts';

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new RuntimeException('Không thể khởi tạo thư mục lưu trữ ảnh bài viết.');
            }
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;
        if (!@move_uploaded_file($file['tmp_name'], $targetPath) && !@copy($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Lưu tệp ảnh lên máy chủ thất bại.');
        }

        return '/assets/images/posts/' . $filename;
    }

    /**
     * Delete article via Stored Procedure pct_sp_posts_delete.
     */
    public function deletePost(int $adminId, int $id): bool
    {
        $rows = $this->callProcedure('pct_sp_posts_delete', [
            $adminId,
            $id,
        ]);

        return !empty($rows);
    }

    /**
     * Get post by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT p.*, u.name AS author_name, u.avatar AS author_avatar, u.role AS author_role
            FROM tbl_posts p
            JOIN tbl_users u ON p.author_id = u.id
            WHERE p.id = ?
            LIMIT 1
        ";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Get post by slug.
     */
    public function getBySlug(string $slug): ?array
    {
        $sql = "
            SELECT p.*, u.name AS author_name, u.avatar AS author_avatar, u.role AS author_role
            FROM tbl_posts p
            JOIN tbl_users u ON p.author_id = u.id
            WHERE p.slug = ?
            LIMIT 1
        ";
        return $this->fetchOne($sql, [trim($slug)]);
    }

    /**
     * Get published posts for public blog.
     */
    public function getAllPublished(int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT p.*, u.name AS author_name, u.avatar AS author_avatar, u.role AS author_role
            FROM tbl_posts p
            JOIN tbl_users u ON p.author_id = u.id
            WHERE p.is_published = 1
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ";
        return $this->fetchAll($sql, [$limit, $offset]);
    }

    /**
     * Get all posts for admin management with optional publish filter.
     */
    public function getAllPosts(?int $isPublished = null, int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT p.*, u.name AS author_name
            FROM tbl_posts p
            JOIN tbl_users u ON p.author_id = u.id
        ";
        $params = [];
        if ($isPublished !== null) {
            $sql .= " WHERE p.is_published = ?";
            $params[] = $isPublished;
        }
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->fetchAll($sql, $params);
    }

    /**
     * Increment views count when article is viewed.
     */
    public function incrementViews(int $id): bool
    {
        $sql = "UPDATE tbl_posts SET views_count = views_count + 1 WHERE id = ?";
        return $this->execute($sql, [$id]) > 0;
    }

    /**
     * Generate unique slug from title.
     */
    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        // Simple Vietnamese transliteration for slugs
        $str = mb_strtolower($title, 'UTF-8');
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];
        foreach ($unicode as $nonAccent => $accent) {
            $str = preg_replace("/($accent)/i", $nonAccent, $str);
        }
        $slug = preg_replace('/[^a-z0-9\s-]/', '', (string)$str);
        $slug = preg_replace('/[\s-]+/', '-', (string)$slug);
        $slug = trim((string)$slug, '-');

        if ($slug === '') {
            $slug = 'bai-viet-' . time();
        }

        $baseSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM tbl_posts WHERE slug = ?";
            $params = [$slug];
            if ($ignoreId !== null && $ignoreId > 0) {
                $sql .= " AND id != ?";
                $params[] = $ignoreId;
            }
            $exists = $this->fetchOne($sql, $params);
            if (!$exists) {
                break;
            }
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get blog statistics for Admin.
     */
    public function getStats(): array
    {
        $sql = "
            SELECT 
                COUNT(*) AS total_posts,
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) AS published_count,
                SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) AS draft_count,
                COALESCE(SUM(views_count), 0) AS total_views
            FROM tbl_posts
        ";
        return $this->fetchOne($sql) ?? [
            'total_posts' => 0,
            'published_count' => 0,
            'draft_count' => 0,
            'total_views' => 0,
        ];
    }
}

if (!class_exists('Post', false)) {
    class_alias(Post::class, 'Post');
}
