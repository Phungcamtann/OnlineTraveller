<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Discussion;
use App\Models\Service;
use Exception;

/**
 * DiscussionController: Single-level tour inquiries and host replies via AJAX (TV4-05).
 */
class DiscussionController extends BaseController
{
    private Discussion $discussionModel;
    private Service $serviceModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->discussionModel = new Discussion();
        $this->serviceModel = new Service();
    }

    /**
     * List discussions for a service (GET /services/{id}/discussions).
     */
    public function index(Request $request, string $id): void
    {
        $serviceId = (int)$id;
        $discussions = $this->discussionModel->getDiscussionsByService($serviceId);

        $this->response->json([
            'success' => true,
            'discussions' => $discussions,
            'count' => count($discussions),
        ]);
    }

    /**
     * Post a new inquiry or reply (POST /discussions).
     */
    public function store(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Vui lòng đăng nhập để gửi câu hỏi hoặc phản hồi.'], 401);
                return;
            }
            $this->flash('warning', 'Vui lòng đăng nhập để gửi câu hỏi.');
            $this->redirect('/login');
            return;
        }

        // Check account lock
        if (($user['status'] ?? '') === 'locked') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Tài khoản của bạn đã bị khóa, không thể gửi thảo luận.'], 403);
                return;
            }
            $this->flash('danger', 'Tài khoản của bạn đã bị khóa.');
            $this->redirect('/login');
            return;
        }

        $userId = (int)$user['id'];
        $serviceId = (int)$request->input('service_id', 0);
        $message = (string)$request->input('message', '');
        $parentIdRaw = $request->input('parent_id');
        $parentId = ($parentIdRaw !== null && $parentIdRaw !== '' && (int)$parentIdRaw > 0) ? (int)$parentIdRaw : null;

        if ($serviceId <= 0) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Gói trải nghiệm không hợp lệ.'], 400);
                return;
            }
            $this->flash('danger', 'Gói trải nghiệm không hợp lệ.');
            $this->redirect('/services');
            return;
        }

        $service = $this->serviceModel->getById($serviceId);
        if (!$service) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Gói trải nghiệm không tồn tại.'], 404);
                return;
            }
            $this->flash('danger', 'Gói trải nghiệm không tồn tại.');
            $this->redirect('/services');
            return;
        }

        if (trim($message) === '') {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Nội dung câu hỏi không được để trống.'], 400);
                return;
            }
            $this->flash('danger', 'Nội dung câu hỏi không được để trống.');
            $this->redirect('/services/' . $serviceId . '#discussions');
            return;
        }

        try {
            $created = $this->discussionModel->createDiscussion($userId, $serviceId, $message, $parentId);

            $isReply = ($parentId !== null && $parentId > 0);
            $msgSuccess = $isReply ? 'Phản hồi câu hỏi thành công!' : 'Gửi câu hỏi thành công!';

            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'message' => $msgSuccess,
                    'discussion' => $created,
                    'is_reply' => $isReply,
                ]);
                return;
            }

            $this->flash('success', $msgSuccess);
            $this->redirect('/services/' . $serviceId . '#discussions');
            return;
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            if ($request->isAjax()) {
                $this->response->json(['error' => $errorMsg], 400);
                return;
            }
            $this->flash('danger', $errorMsg);
            $this->redirect('/services/' . $serviceId . '#discussions');
            return;
        }
    }
}

if (!class_exists('DiscussionController', false)) {
    class_alias(DiscussionController::class, 'DiscussionController');
}
