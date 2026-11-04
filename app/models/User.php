<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    /**
     * Find user for login verification using sp_users_find_login.
     */
    public function findLogin(string $email): ?array
    {
        return $this->callProcedureOne('sp_users_find_login', [$email]);
    }

    /**
     * Register a new user using sp_users_register.
     */
    public function register(string $email, string $name, string $passwordHash, string $phone, string $role = 'tourist'): ?array
    {
        return $this->callProcedureOne('sp_users_register', [$email, $name, $passwordHash, $phone, $role]);
    }

    /**
     * Update user profile using sp_users_update_profile.
     */
    public function updateProfile(int $userId, string $name, string $phone, ?string $livingArea, ?string $bio, ?string $avatar = null): ?array
    {
        return $this->callProcedureOne('sp_users_update_profile', [$userId, $name, $phone, $livingArea ?? '', $bio ?? '', $avatar]);
    }

    /**
     * Admin set user status using sp_users_set_status.
     */
    public function setStatus(int $adminId, int $userId, string $status): ?array
    {
        return $this->callProcedureOne('sp_users_set_status', [$adminId, $userId, $status]);
    }

    /**
     * Get user by ID.
     */
    public function getById(int $id): ?array
    {
        return $this->fetchOne("SELECT id, role, name, email, phone, avatar, living_area, bio, status, created_at FROM tbl_users WHERE id = ?", [$id]);
    }
}

if (!class_exists('User', false)) {
    class_alias(User::class, 'User');
}
