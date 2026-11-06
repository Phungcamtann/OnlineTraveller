<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

class Category extends BaseModel
{
    /**
     * Get all categories with count of associated services/tours.
     */
    public function getAllWithTourCount(): array
    {
        $sql = "
            SELECT c.*, COUNT(s.id) AS services_count
            FROM tbl_categories c
            LEFT JOIN tbl_services s ON c.id = s.category_id
            GROUP BY c.id
            ORDER BY c.id ASC
        ";
        return $this->fetchAll($sql);
    }

    /**
     * Get only active categories for public menus & search filters.
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM tbl_categories WHERE is_active = 1 ORDER BY id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Get single category by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM tbl_categories WHERE id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Get single category by slug.
     */
    public function getBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM tbl_categories WHERE slug = ? LIMIT 1";
        return $this->fetchOne($sql, [$slug]);
    }

    /**
     * Save category (Insert if $id is null/0, Update if $id > 0) via dhs_sp_categories_save.
     */
    public function save(int $adminId, ?int $id, string $name, string $slug, ?string $description, ?string $icon, int $isActive = 1): ?array
    {
        $rows = $this->callProcedure('dhs_sp_categories_save', [
            $adminId,
            $id ?? 0,
            $name,
            $slug,
            $description ?? '',
            $icon ?? 'bi-tag',
            $isActive,
        ]);

        return empty($rows) ? null : $rows[0];
    }

    /**
     * Helper to create SEO-friendly Vietnamese URL slug.
     */
    public static function slugify(string $text): string
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];

        $str = mb_strtolower(trim($text), 'UTF-8');
        foreach ($unicode as $nonAccent => $accent) {
            $str = preg_replace("/({$accent})/u", $nonAccent, $str);
        }

        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
        $str = preg_replace('/[\s-]+/', '-', $str);
        return trim((string)$str, '-');
    }
}

if (!class_exists('Category', false)) {
    class_alias(Category::class, 'Category');
}
