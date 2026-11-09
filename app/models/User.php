<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    /**
     * Find user for login verification using dhs_sp_users_find_login.
     */
    public function findLogin(string $email): ?array
    {
        return $this->callProcedureOne('dhs_sp_users_find_login', [$email]);
    }

    /**
     * Register a new user using dhs_sp_users_register.
     */
    public function register(string $email, string $name, string $passwordHash, string $phone, string $role = 'tourist'): ?array
    {
        return $this->callProcedureOne('dhs_sp_users_register', [$email, $name, $passwordHash, $phone, $role]);
    }

    /**
     * Update user profile using dhs_sp_users_update_profile.
     */
    public function updateProfile(int $userId, string $name, string $phone, ?string $livingArea, ?string $bio, ?string $avatar = null): ?array
    {
        return $this->callProcedureOne('dhs_sp_users_update_profile', [$userId, $name, $phone, $livingArea ?? '', $bio ?? '', $avatar]);
    }

    /**
     * Admin set user status using dhs_sp_users_set_status.
     */
    public function setStatus(int $adminId, int $userId, string $status): ?array
    {
        return $this->callProcedureOne('dhs_sp_users_set_status', [$adminId, $userId, $status]);
    }

    /**
     * Get user by ID.
     */
    public function getById(int $id): ?array
    {
        return $this->fetchOne("SELECT id, role, name, email, phone, avatar, living_area, bio, status, created_at FROM tbl_users WHERE id = ?", [$id]);
    }

    /**
     * Get paginated and filtered list of users for Admin management.
     */
    public function getAllUsers(?string $role = null, ?string $status = null, ?string $search = null, int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT id, role, name, email, phone, avatar, living_area, bio, status, created_at, updated_at FROM tbl_users WHERE 1=1";
        $params = [];

        if (!empty($role) && in_array($role, ['tourist', 'host', 'admin'], true)) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }

        if (!empty($status) && in_array($status, ['active', 'inactive', 'locked'], true)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $wildcard = '%' . trim($search) . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        return $this->fetchAll($sql, $params);
    }

    /**
     * Count users matching filter conditions.
     */
    public function countUsers(?string $role = null, ?string $status = null, ?string $search = null): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM tbl_users WHERE 1=1";
        $params = [];

        if (!empty($role) && in_array($role, ['tourist', 'host', 'admin'], true)) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }

        if (!empty($status) && in_array($status, ['active', 'inactive', 'locked'], true)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $wildcard = '%' . trim($search) . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $row = $this->fetchOne($sql, $params);
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Get aggregate statistics on users.
     */
    public function getUserStats(): array
    {
        $total = (int)($this->fetchOne("SELECT COUNT(*) as c FROM tbl_users")['c'] ?? 0);
        $hosts = (int)($this->fetchOne("SELECT COUNT(*) as c FROM tbl_users WHERE role = 'host'")['c'] ?? 0);
        $tourists = (int)($this->fetchOne("SELECT COUNT(*) as c FROM tbl_users WHERE role = 'tourist'")['c'] ?? 0);
        $admins = (int)($this->fetchOne("SELECT COUNT(*) as c FROM tbl_users WHERE role = 'admin'")['c'] ?? 0);
        $locked = (int)($this->fetchOne("SELECT COUNT(*) as c FROM tbl_users WHERE status = 'locked'")['c'] ?? 0);
        $active = (int)($this->fetchOne("SELECT COUNT(*) as c FROM tbl_users WHERE status = 'active'")['c'] ?? 0);

        return [
            'total' => $total,
            'hosts' => $hosts,
            'tourists' => $tourists,
            'admins' => $admins,
            'locked' => $locked,
            'active' => $active,
        ];
    }
}

if (!class_exists('User', false)) {
    class_alias(User::class, 'User');
}
