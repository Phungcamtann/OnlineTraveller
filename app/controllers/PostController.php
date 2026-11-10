<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Post;
use App\Integrations\RssClient;
use Exception;

/**
 * PostController: Travel articles, community stories and Admin blog management (TV4-06 & Phase 3).
 */
class PostController extends BaseController
{
    private Post $postModel;
    private RssClient $rssClient;

    public function __construct(?Request $request = null, ?RssClient $rssClient = null)
    {
        parent::__construct($request);
        $this->postModel = new Post();
        $this->rssClient = $rssClient ?? new RssClient();
    }

    /**
     * Public travel articles & RSS news feed (GET /posts).
     */
    public function index(Request $request): void
    {
        $communityPosts = $this->postModel->getAllPublished(30);

        try {
            $rssFeed = $this->rssClient->getFeed(12);
        } catch (\Throwable $e) {
            $rssFeed = [];
        }

        // 1. Format community posts
        $formattedCommunity = [];
        foreach ($communityPosts as $p) {
            $formattedCommunity[] = [
                'id' => (int)$p['id'],
                'type' => 'community',
                'title' => (string)$p['title'],
                'slug' => (string)$p['slug'],
                'link' => '/posts/' . $p['slug'],
                'content' => (string)$p['content'],
                'thumbnail' => !empty($p['thumbnail']) ? $p['thumbnail'] : '/assets/images/default-blog.jpg',
                'author_name' => $p['author_name'] ?? 'Thành viên',
                'author_role' => $p['author_role'] ?? 'tourist',
                'author_avatar' => $p['author_avatar'] ?? null,
                'views_count' => (int)($p['views_count'] ?? 0),
                'created_at' => (string)$p['created_at'],
                'source_label' => 'Cộng đồng OnlineTraveller',
                'is_external' => false,
            ];
        }

        // 2. Format RSS news items
        $formattedRss = [];
        foreach ($rssFeed as $r) {
            $formattedRss[] = [
                'id' => null,
                'type' => 'rss',
                'title' => (string)$r['title'],
                'slug' => null,
                'link' => (string)$r['link'],
                'content' => (string)($r['summary'] ?? ''),
                'thumbnail' => !empty($r['thumbnail']) ? $r['thumbnail'] : 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop',
                'author_name' => 'VnExpress Du Lịch',
                'author_role' => 'rss',
                'author_avatar' => null,
                'views_count' => 0,
                'created_at' => (string)($r['pub_date'] ?? date('d/m/Y')),
                'source_label' => 'Nguồn: VnExpress Du Lịch',
                'is_external' => true,
            ];
        }

        // 3. Filter by tab
        $currentTab = $request->query('tab', 'all');
        if ($currentTab === 'community') {
            $allItems = $formattedCommunity;
        } elseif ($currentTab === 'rss') {
            $allItems = $formattedRss;
        } else {
            // Interleave both streams cleanly
            $allItems = [];
            $maxLen = max(count($formattedCommunity), count($formattedRss));
            for ($i = 0; $i < $maxLen; $i++) {
                if (isset($formattedCommunity[$i])) $allItems[] = $formattedCommunity[$i];
                if (isset($formattedRss[$i])) $allItems[] = $formattedRss[$i];
            }
        }

        // 4. Pagination (6 items per page)
        $perPage = 6;
        $totalItems = count($allItems);
        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        $currentPage = max(1, min($totalPages, (int)$request->query('page', 1)));
        $offset = ($currentPage - 1) * $perPage;
        $pagedItems = array_slice($allItems, $offset, $perPage);

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->response->json([
                'success' => true, 
                'posts' => $pagedItems,
                'total' => $totalItems,
                'page' => $currentPage,
                'total_pages' => $totalPages
            ]);
            return;
        }

        $this->view('posts/index', [
            'title' => 'Bản Tin Cộng Đồng & Tin Tức Du Lịch — OnlineTraveller',
            'posts' => $pagedItems,
            'currentTab' => $currentTab,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalCount' => $totalItems,
            'communityCount' => count($formattedCommunity),
            'rssCount' => count($formattedRss),
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
     * Create article form (GET /posts/create or GET /admin/posts/create).
     * Accessible by Tourist, Host and Admin.
     */
    public function create(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->flash('danger', 'Vui lòng đăng nhập để chia sẻ câu chuyện của bạn.');
            $this->redirect('/login');
            return;
        }

        $isAdminUrl = str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin');

        if ($isAdminUrl && $user['role'] === 'admin') {
            $this->view('admin/posts/form', [
                'title' => 'Thêm Mới Bài Viết Cẩm Nang — Admin',
                'post' => null,
                'user' => $user,
            ]);
            return;
        }

        $this->view('posts/create', [
            'title' => 'Chia Sẻ Trải Nghiệm Du Lịch Bản Địa — OnlineTraveller',
            'post' => null,
            'user' => $user,
        ]);
    }

    /**
     * Store new article (POST /posts or POST /admin/posts).
     * Tourist, Host and Admin can post!
     */
    public function store(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Vui lòng đăng nhập để đăng bài viết.'], 401);
                return;
            }
            $this->flash('danger', 'Vui lòng đăng nhập để chia sẻ bài viết.');
            $this->redirect('/login');
            return;
        }

        $title = trim((string)$request->input('title', ''));
        $slug = trim((string)$request->input('slug', ''));
        $content = trim((string)$request->input('content', ''));
        $thumbnail = trim((string)$request->input('thumbnail', ''));
        $isPublished = (int)$request->input('is_published', 1);

        $isAdminContext = str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin');

        // Check if admin permission required ONLY if submitted through /admin/posts
        if ($isAdminContext && ($user['role'] ?? '') !== 'admin') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Bạn không có quyền quản trị.'], 403);
                return;
            }
            $this->flash('danger', 'Bạn không có quyền quản trị.');
            $this->redirect('/login');
            return;
        }

        // Handle thumbnail file upload if present
        if (!empty($_FILES['thumbnail_file']['name'])) {
            try {
                $thumbnail = $this->postModel->handleThumbnailUpload($_FILES['thumbnail_file']);
            } catch (Exception $e) {
                if ($request->isAjax()) {
                    $this->response->json(['error' => $e->getMessage()], 400);
                    return;
                }
                $this->flash('danger', $e->getMessage());
                $this->redirect($isAdminContext ? '/admin/posts/create' : '/posts/create');
                return;
            }
        }

        try {
            if ($isAdminContext) {
                $saved = $this->postModel->savePost(
                    (int)$user['id'],
                    null,
                    $title,
                    $slug,
                    $content,
                    $thumbnail ?: null,
                    $isPublished
                );
            } else {
                $saved = $this->postModel->createCommunityPost(
                    (int)$user['id'],
                    $title,
                    $slug ?: null,
                    $content,
                    $thumbnail ?: null
                );
            }

            $message = "Đã xuất bản bài viết \"{$saved['title']}\" thành công!";
            if ($request->isAjax()) {
                $this->response->json(['success' => true, 'message' => $message, 'post' => $saved]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect($isAdminContext ? '/admin/posts' : '/posts/' . $saved['slug']);
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => $e->getMessage()], 400);
                return;
            }
            $this->flash('danger', $e->getMessage());
            $this->redirect($isAdminContext ? '/admin/posts/create' : '/posts/create');
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
