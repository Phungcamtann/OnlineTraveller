<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Services\AuthService;
use Exception;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->authService = new AuthService();
    }

    public function loginForm(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        $this->view('auth/login', ['title' => 'Đăng Nhập — OnlineTraveller']);
    }

    public function login(Request $request): void
    {
        $email = (string)$request->post('email', '');
        $password = (string)$request->post('password', '');

        try {
            $user = $this->authService->authenticate($email, $password);
            $this->flash('success', 'Chào mừng ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . ' đã quay trở lại!');

            if ($user['role'] === 'admin') {
                $this->redirect('/admin');
            } elseif ($user['role'] === 'host') {
                $this->redirect('/host');
            } else {
                $this->redirect('/');
            }
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/login');
        }
    }

    public function registerForm(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        $this->view('auth/register', ['title' => 'Đăng Ký Tài Khoản — OnlineTraveller']);
    }

    public function register(Request $request): void
    {
        $name = (string)$request->post('name', '');
        $email = (string)$request->post('email', '');
        $phone = (string)$request->post('phone', '');
        $password = (string)$request->post('password', '');
        $role = (string)$request->post('role', 'tourist');

        try {
            $newUser = $this->authService->register($name, $email, $password, $phone, $role);
            $this->flash('success', 'Đăng ký tài khoản thành công!');
            $this->redirect($newUser['role'] === 'host' ? '/host' : '/');
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/register');
        }
    }

    /**
     * Display user profile and Local Host story editor.
     */
    public function profile(): void
    {
        $currentUser = $this->user();
        if (!$currentUser) {
            $this->redirect('/login');
        }

        $freshUser = $this->authService->getUser((int)$currentUser['id']) ?? $currentUser;

        $this->view('profile/index', [
            'title' => 'Hồ Sơ Của Tôi — OnlineTraveller',
            'user' => $freshUser,
        ]);
    }

    /**
     * Update user profile, living area, and host bio.
     */
    public function updateProfile(Request $request): void
    {
        $currentUser = $this->user();
        if (!$currentUser) {
            $this->redirect('/login');
        }

        $name = (string)$request->post('name', '');
        $phone = (string)$request->post('phone', '');
        $livingArea = (string)$request->post('living_area', '');
        $bio = (string)$request->post('bio', '');

        try {
            $this->authService->updateProfile((int)$currentUser['id'], $name, $phone, $livingArea, $bio);
            $this->flash('success', 'Thông tin hồ sơ và câu chuyện bản địa đã được cập nhật thành công!');
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
        }

        $this->redirect('/profile');
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->flash('info', 'Bạn đã đăng xuất thành công.');
        $this->redirect('/login');
    }
}

if (!class_exists('AuthController', false)) {
    class_alias(AuthController::class, 'AuthController');
}
