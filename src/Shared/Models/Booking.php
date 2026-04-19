<?php
namespace Shared\Models;
 
class Booking extends BaseModel {
    protected $table = 'bookings_booking';
    protected $primaryKey = 'MaDatPhong';
 
    /**
     * Tìm bookings của guest
     */
    public function getByGuestId($guestId, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            // Fix: Đổi guest_id thành MaKhachHang (tên column trong DB)
            $sql = "SELECT * FROM {$this->table} WHERE MaKhachHang = {$guestId} LIMIT {$perPage} OFFSET {$offset}";
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
            // Fix: Đổi status thành TrangThai (tên column trong DB)
            $sql = "SELECT * FROM {$this->table} WHERE TrangThai = '{$status}' LIMIT {$perPage} OFFSET {$offset}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching bookings by status: " . $e->getMessage());
        }
    }
 
    /**
     * Tìm bookings theo room type
     */
    public function getByRoomType($roomType, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} WHERE MaLoaiPhong = '{$roomType}' LIMIT {$perPage} OFFSET {$offset}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching bookings by room type: " . $e->getMessage());
        }
    }
 
    /**
     * Count bookings theo status (để hỗ trợ pagination)
     */
    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE TrangThai = '{$status}'";
            $result = $this->dbInstance->select($sql);
            return $result[0]['total'] ?? 0;
        } catch (\Exception $e) {
            throw new \Exception("Error counting bookings: " . $e->getMessage());
        }
    }
}
?>