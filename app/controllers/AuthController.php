<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\User;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->userModel = new User();
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
        $email = trim((string)$request->post('email', ''));
        $password = (string)$request->post('password', '');

        if ($email === '' || $password === '') {
            $this->flash('danger', 'Vui lòng nhập đầy đủ email và mật khẩu.');
            $this->redirect('/login');
        }

        $user = $this->userModel->findLogin($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->flash('danger', 'Email hoặc mật khẩu không chính xác.');
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            $this->flash('danger', 'Tài khoản của bạn đang bị khóa hoặc chưa kích hoạt.');
            $this->redirect('/login');
        }

        // Exclude password_hash from session
        unset($user['password_hash']);
        $_SESSION['user'] = $user;

        $this->flash('success', 'Chào mừng ' . $user['name'] . ' đã quay trở lại!');

        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        } elseif ($user['role'] === 'host') {
            $this->redirect('/host');
        } else {
            $this->redirect('/');
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
        $name = trim((string)$request->post('name', ''));
        $email = trim((string)$request->post('email', ''));
        $phone = trim((string)$request->post('phone', ''));
        $password = (string)$request->post('password', '');
        $role = (string)$request->post('role', 'tourist');

        if (!in_array($role, ['tourist', 'host'], true)) {
            $role = 'tourist';
        }

        if ($name === '' || $email === '' || $password === '') {
            $this->flash('danger', 'Vui lòng điền đầy đủ các thông tin bắt buộc.');
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Định dạng email không hợp lệ.');
            $this->redirect('/register');
        }

        $existing = $this->userModel->findLogin($email);
        if ($existing) {
            $this->flash('danger', 'Email này đã được sử dụng. Vui lòng chọn email khác.');
            $this->redirect('/register');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $newUser = $this->userModel->register($email, $name, $passwordHash, $phone, $role);
            if ($newUser) {
                unset($newUser['password_hash']);
                $_SESSION['user'] = $newUser;
                $this->flash('success', 'Đăng ký tài khoản thành công!');
                $this->redirect($role === 'host' ? '/host' : '/');
            }
        } catch (\Exception $e) {
            $this->flash('danger', 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage());
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['user']);
        }
        $this->flash('info', 'Bạn đã đăng xuất thành công.');
        $this->redirect('/login');
    }
}

if (!class_exists('AuthController', false)) {
    class_alias(AuthController::class, 'AuthController');
}
