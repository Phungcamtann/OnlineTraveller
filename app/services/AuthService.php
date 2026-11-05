<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use RuntimeException;

/**
 * Authentication and authorization service: registration, login, session,
 * and user profile updating via Stored Procedures.
 */
class AuthService
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->userModel = $userModel ?? new User();
    }

    /**
     * Authenticate user credentials.
     *
     * @return array Authenticated user record without password hash
     * @throws RuntimeException If authentication fails
     */
    public function authenticate(string $email, string $password): array
    {
        $cleanEmail = trim($email);
        if ($cleanEmail === '' || $password === '') {
            throw new RuntimeException('Vui lòng nhập đầy đủ email và mật khẩu.');
        }

        $user = $this->userModel->findLogin($cleanEmail);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new RuntimeException('Email hoặc mật khẩu không chính xác.');
        }

        if (($user['status'] ?? '') !== 'active') {
            throw new RuntimeException('Tài khoản của bạn đang bị khóa hoặc chưa kích hoạt.');
        }

        unset($user['password_hash']);

        // Establish session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['user'] = $user;

        return $user;
    }

    /**
     * Register a new user account (Tourist or Local Host).
     *
     * @throws RuntimeException If validation fails
     */
    public function register(string $name, string $email, string $password, string $phone = '', string $role = 'tourist'): array
    {
        $cleanName = trim($name);
        $cleanEmail = trim($email);
        $cleanPhone = trim($phone);

        if ($cleanName === '' || $cleanEmail === '' || $password === '') {
            throw new RuntimeException('Vui lòng điền đầy đủ các thông tin bắt buộc.');
        }

        if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Định dạng email không hợp lệ.');
        }

        if (strlen($password) < 6) {
            throw new RuntimeException('Mật khẩu phải có độ dài tối thiểu 6 ký tự.');
        }

        if (!in_array($role, ['tourist', 'host'], true)) {
            $role = 'tourist';
        }

        $existing = $this->userModel->findLogin($cleanEmail);
        if ($existing) {
            throw new RuntimeException('Email này đã được sử dụng. Vui lòng chọn email khác.');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $newUser = $this->userModel->register($cleanEmail, $cleanName, $passwordHash, $cleanPhone, $role);

        if (!$newUser) {
            throw new RuntimeException('Không thể tạo tài khoản. Vui lòng thử lại sau.');
        }

        unset($newUser['password_hash']);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['user'] = $newUser;

        return $newUser;
    }

    /**
     * Update user profile, living area, and host bio.
     *
     * @throws RuntimeException If update fails
     */
    public function updateProfile(int $userId, string $name, string $phone, ?string $livingArea, ?string $bio, ?string $avatar = null): array
    {
        $cleanName = trim($name);
        $cleanPhone = trim($phone);
        $cleanArea = trim((string)$livingArea);
        $cleanBio = trim((string)$bio);

        if ($cleanName === '') {
            throw new RuntimeException('Họ và tên không được để trống.');
        }

        $updated = $this->userModel->updateProfile($userId, $cleanName, $cleanPhone, $cleanArea, $cleanBio, $avatar);

        if (!$updated) {
            throw new RuntimeException('Không thể cập nhật hồ sơ người dùng.');
        }

        // Update current session user data
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (isset($_SESSION['user']) && (int)$_SESSION['user']['id'] === $userId) {
            $_SESSION['user'] = array_merge($_SESSION['user'], $updated);
        }

        return $updated;
    }

    /**
     * Get user profile details by ID.
     */
    public function getUser(int $userId): ?array
    {
        return $this->userModel->getById($userId);
    }

    /**
     * Terminate user session.
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['user']);
        }
    }
}

if (!class_exists('AuthService', false)) {
    class_alias(AuthService::class, 'AuthService');
}
