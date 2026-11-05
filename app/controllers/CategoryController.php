<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Category;
use Exception;

class CategoryController extends BaseController
{
    private Category $categoryModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->categoryModel = new Category();
    }

    /**
     * List all tour categories with actions.
     */
    public function index(): void
    {
        $categories = $this->categoryModel->getAllWithTourCount();

        $this->view('admin/categories/index', [
            'title' => 'Quản Lý Danh Mục Trải Nghiệm — Admin',
            'categories' => $categories,
            'user' => $this->user(),
        ]);
    }

    /**
     * Show form to create a new category.
     */
    public function create(): void
    {
        $this->view('admin/categories/create', [
            'title' => 'Thêm Danh Mục Trải Nghiệm Mới — Admin',
            'user' => $this->user(),
        ]);
    }

    /**
     * Store new category via sp_categories_save.
     */
    public function store(Request $request): void
    {
        $admin = $this->user();
        $adminId = (int)($admin['id'] ?? 0);

        $name = trim((string)$request->post('name', ''));
        $slug = trim((string)$request->post('slug', ''));
        $description = trim((string)$request->post('description', ''));
        $icon = trim((string)$request->post('icon', 'bi-tag'));
        $isActive = $request->post('is_active') ? 1 : 0;

        if ($name === '') {
            $this->flash('danger', 'Tên danh mục không được để trống.');
            $this->redirect('/admin/categories/create');
        }

        if ($slug === '') {
            $slug = Category::slugify($name);
        } else {
            $slug = Category::slugify($slug);
        }

        // Check duplicate slug
        $existing = $this->categoryModel->getBySlug($slug);
        if ($existing) {
            $slug .= '-' . time();
        }

        try {
            $this->categoryModel->save($adminId, 0, $name, $slug, $description, $icon, $isActive);
            $this->flash('success', "Thêm danh mục '{$name}' thành công!");
            $this->redirect('/admin/categories');
        } catch (Exception $e) {
            $this->flash('danger', 'Lỗi khi lưu danh mục: ' . $e->getMessage());
            $this->redirect('/admin/categories/create');
        }
    }

    /**
     * Show form to edit existing category.
     */
    public function edit(Request $request, string $id): void
    {
        $categoryId = (int)$id;
        $category = $this->categoryModel->getById($categoryId);

        if (!$category) {
            $this->flash('danger', 'Không tìm thấy danh mục yêu cầu.');
            $this->redirect('/admin/categories');
        }

        $this->view('admin/categories/edit', [
            'title' => 'Chỉnh Sửa Danh Mục: ' . $category['name'],
            'category' => $category,
            'user' => $this->user(),
        ]);
    }

    /**
     * Update category via sp_categories_save.
     */
    public function update(Request $request, string $id): void
    {
        $admin = $this->user();
        $adminId = (int)($admin['id'] ?? 0);
        $categoryId = (int)$id;

        $category = $this->categoryModel->getById($categoryId);
        if (!$category) {
            $this->flash('danger', 'Không tìm thấy danh mục.');
            $this->redirect('/admin/categories');
        }

        $name = trim((string)$request->post('name', ''));
        $slug = trim((string)$request->post('slug', ''));
        $description = trim((string)$request->post('description', ''));
        $icon = trim((string)$request->post('icon', 'bi-tag'));
        $isActive = $request->post('is_active') ? 1 : 0;

        if ($name === '') {
            $this->flash('danger', 'Tên danh mục không được để trống.');
            $this->redirect("/admin/categories/{$categoryId}/edit");
        }

        if ($slug === '') {
            $slug = Category::slugify($name);
        } else {
            $slug = Category::slugify($slug);
        }

        // Check duplicate slug with another category
        $existing = $this->categoryModel->getBySlug($slug);
        if ($existing && (int)$existing['id'] !== $categoryId) {
            $slug .= '-' . $categoryId;
        }

        try {
            $this->categoryModel->save($adminId, $categoryId, $name, $slug, $description, $icon, $isActive);
            $this->flash('success', "Cập nhật danh mục '{$name}' thành công!");
            $this->redirect('/admin/categories');
        } catch (Exception $e) {
            $this->flash('danger', 'Lỗi khi cập nhật danh mục: ' . $e->getMessage());
            $this->redirect("/admin/categories/{$categoryId}/edit");
        }
    }

    /**
     * Toggle active / hidden status of category.
     */
    public function toggle(Request $request, string $id): void
    {
        $admin = $this->user();
        $adminId = (int)($admin['id'] ?? 0);
        $categoryId = (int)$id;

        $category = $this->categoryModel->getById($categoryId);
        if (!$category) {
            $this->flash('danger', 'Không tìm thấy danh mục.');
            $this->redirect('/admin/categories');
        }

        $newStatus = ((int)$category['is_active'] === 1) ? 0 : 1;
        $statusLabel = $newStatus === 1 ? 'hiển thị' : 'ẩn';

        try {
            $this->categoryModel->save(
                $adminId,
                $categoryId,
                $category['name'],
                $category['slug'],
                $category['description'],
                $category['icon'],
                $newStatus
            );
            $this->flash('success', "Đã chuyển danh mục '{$category['name']}' sang trạng thái {$statusLabel}!");
        } catch (Exception $e) {
            $this->flash('danger', 'Lỗi thao tác: ' . $e->getMessage());
        }

        $this->redirect('/admin/categories');
    }
}

if (!class_exists('CategoryController', false)) {
    class_alias(CategoryController::class, 'CategoryController');
}
