<?php
namespace Shared\Models;

class Booking extends BaseModel {
    protected $table = 'bookings_booking';

    /**
     * Tìm bookings của guest
     */
    public function getByGuestId($guestId, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} WHERE guest_id = {$guestId} LIMIT {$perPage} OFFSET {$offset}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching bookings: " . $e->getMessage());
        }
    }

    /**
     * Tìm bookings theo status
     */
    public function getByStatus($status, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} WHERE status = '{$status}' LIMIT {$perPage} OFFSET {$offset}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching bookings by status: " . $e->getMessage());
        }
    }
}
?>
