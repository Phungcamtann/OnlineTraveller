<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Post;
use Exception;

/**
 * PostController: Travel articles, guides and Admin blog management (TV4-06).
 */
class PostController extends BaseController
{
    private Post $postModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->postModel = new Post();
    }

    /**
     * Public travel articles listing (GET /posts).
     */
    public function index(Request $request): void
    {
        $posts = $this->postModel->getAllPublished(12);

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->response->json(['success' => true, 'posts' => $posts]);
            return;
        }

        $this->view('posts/index', [
            'title' => 'Cẩm Nang & Tin Tức Du Lịch Bản Địa — OnlineTraveller',
            'posts' => $posts,
            'user' => $this->user(),
        ]);
    }

    /**
     * Public travel article detail (GET /posts/{slug}).
     */
    public function show(Request $request, string $slug): void
    {
        $post = is_numeric($slug) 
            ? $this->postModel->getById((int)$slug) 
            : $this->postModel->getBySlug($slug);

        if (!$post || ((int)$post['is_published'] !== 1 && ($this->user()['role'] ?? '') !== 'admin')) {
            $this->response->setStatusCode(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy bài viết']);
            return;
        }

        // Increment view count
        $this->postModel->incrementViews((int)$post['id']);

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->response->json(['success' => true, 'post' => $post]);
            return;
        }

        $this->view('posts/show', [
            'title' => $post['title'] . ' — Cẩm Nang Du Lịch',
            'post' => $post,
            'user' => $this->user(),
        ]);
    }

    /**
     * Admin articles management (GET /admin/posts).
     */
    public function adminIndex(Request $request): void
    {
        $status = $request->query('status');
        $isPublished = ($status === 'published') ? 1 : (($status === 'draft') ? 0 : null);

        $posts = $this->postModel->getAllPosts($isPublished);
        $stats = $this->postModel->getStats();

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->response->json(['success' => true, 'posts' => $posts, 'stats' => $stats]);
            return;
        }

        $this->view('admin/posts/index', [
            'title' => 'Quản Lý Bài Viết Cẩm Nang — Admin',
            'posts' => $posts,
            'stats' => $stats,
            'currentStatus' => $status,
            'user' => $this->user(),
        ]);
    }

    /**
     * Admin create article form (GET /admin/posts/create).
     */
    public function create(Request $request): void
    {
        $this->view('admin/posts/form', [
            'title' => 'Thêm Mới Bài Viết Cẩm Nang — Admin',
            'post' => null,
            'user' => $this->user(),
        ]);
    }

    /**
     * Admin store new article (POST /admin/posts).
     */
    public function store(Request $request): void
    {
        $admin = $this->user();
        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
                return;
            }
            $this->flash('danger', 'Bạn không có quyền quản trị.');
            $this->redirect('/login');
            return;
        }

        $title = (string)$request->input('title', '');
        $slug = (string)$request->input('slug', '');
        $content = (string)$request->input('content', '');
        $thumbnail = $request->input('thumbnail');
        $isPublished = (int)$request->input('is_published', 1);

        try {
            $saved = $this->postModel->savePost(
                (int)$admin['id'],
                null,
                $title,
                $slug,
                $content,
                $thumbnail ? (string)$thumbnail : null,
                $isPublished
            );

            $message = "Đã xuất bản bài viết \"{$saved['title']}\" thành công!";
            if ($request->isAjax()) {
                $this->response->json(['success' => true, 'message' => $message, 'post' => $saved]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect('/admin/posts');
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => $e->getMessage()], 400);
                return;
            }
            $this->flash('danger', $e->getMessage());
            $this->redirect('/admin/posts/create');
            return;
        }
    }

    /**
     * Admin edit article form (GET /admin/posts/{id}/edit).
     */
    public function edit(Request $request, string $id): void
    {
        $post = $this->postModel->getById((int)$id);
        if (!$post) {
            $this->flash('danger', 'Không tìm thấy bài viết yêu cầu.');
            $this->redirect('/admin/posts');
            return;
        }

        $this->view('admin/posts/form', [
            'title' => 'Chỉnh Sửa Bài Viết #' . $post['id'] . ' — Admin',
            'post' => $post,
            'user' => $this->user(),
        ]);
    }

    /**
     * Admin update article (POST /admin/posts/{id}/edit).
     */
    public function update(Request $request, string $id): void
    {
        $admin = $this->user();
        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Bạn không có quyền quản trị.'], 403);
                return;
            }
            $this->flash('danger', 'Bạn không có quyền quản trị.');
            $this->redirect('/login');
            return;
        }

        $postId = (int)$id;
        $title = (string)$request->input('title', '');
        $slug = (string)$request->input('slug', '');
        $content = (string)$request->input('content', '');
        $thumbnail = $request->input('thumbnail');
        $isPublished = (int)$request->input('is_published', 1);

        try {
            $updated = $this->postModel->savePost(
                (int)$admin['id'],
                $postId,
                $title,
                $slug,
                $content,
                $thumbnail ? (string)$thumbnail : null,
                $isPublished
            );

            $message = "Đã cập nhật bài viết \"{$updated['title']}\" thành công!";
            if ($request->isAjax()) {
                $this->response->json(['success' => true, 'message' => $message, 'post' => $updated]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect('/admin/posts');
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => $e->getMessage()], 400);
                return;
            }
            $this->flash('danger', $e->getMessage());
            $this->redirect('/admin/posts/' . $postId . '/edit');
            return;
        }
    }

    /**
     * Admin delete article (POST /admin/posts/{id}/delete).
     */
    public function destroy(Request $request, string $id): void
    {
        $admin = $this->user();
        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Bạn không có quyền quản trị.'], 403);
                return;
            }
            $this->flash('danger', 'Bạn không có quyền quản trị.');
            $this->redirect('/login');
            return;
        }

        $postId = (int)$id;

        try {
            $this->postModel->deletePost((int)$admin['id'], $postId);

            $message = 'Đã xóa bài viết thành công.';
            if ($request->isAjax()) {
                $this->response->json(['success' => true, 'message' => $message]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect('/admin/posts');
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => $e->getMessage()], 400);
                return;
            }
            $this->flash('danger', $e->getMessage());
            $this->redirect('/admin/posts');
            return;
        }
    }
}

if (!class_exists('PostController', false)) {
    class_alias(PostController::class, 'PostController');
}
