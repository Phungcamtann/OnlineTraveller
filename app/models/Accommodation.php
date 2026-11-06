<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

/**
 * Accommodation Model: Homestay lodging linked to tours (1-1 relation).
 * Supports homestay name, address, room type, nights count, and notes.
 */
class Accommodation extends BaseModel
{
    /**
     * Get homestay accommodation attached to a tour service.
     */
    public function getByServiceId(int $serviceId): ?array
    {
        $sql = "
            SELECT 
                service_id, homestay_name, address, room_type, nights, notes, created_at
            FROM tbl_service_accommodations
            WHERE service_id = ?
            LIMIT 1
        ";

        return $this->fetchOne($sql, [$serviceId]);
    }

    /**
     * Save or update Homestay accommodation via Stored Procedure sp_accommodations_save.
     */
    public function save(
        int $hostId,
        int $serviceId,
        string $homestayName,
        string $address,
        string $roomType = 'Phòng Tiêu Chuẩn',
        int $nights = 1,
        ?string $notes = null
    ): array {
        $rows = $this->callProcedure('sp_accommodations_save', [
            $hostId,
            $serviceId,
            $homestayName,
            $address,
            $roomType,
            $nights,
            $notes,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Remove homestay accommodation from a tour package.
     */
    public function delete(int $serviceId): bool
    {
        $sql = "DELETE FROM tbl_service_accommodations WHERE service_id = ?";
        $stmt = $this->db->query($sql, [$serviceId]);
        return $stmt->rowCount() > 0;
    }
}

if (!class_exists('Accommodation', false)) {
    class_alias(Accommodation::class, 'Accommodation');
}
