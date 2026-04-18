<?php
namespace Shared\Models;

class Guest extends BaseModel {
    protected $table = 'hotels_guests';

    /**
     * Tìm guests theo tên (search)
     */
    public function search($searchTerm, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $searchParam = "%{$searchTerm}%";
            $sql = "SELECT * FROM {$this->table} 
                    WHERE name LIKE '{$searchParam}' 
                    OR email LIKE '{$searchParam}' 
                    OR phone LIKE '{$searchParam}'
                    LIMIT {$perPage} OFFSET {$offset}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error searching guests: " . $e->getMessage());
        }
    }

    /**
     * Đếm guests từ search term
     */
    public function searchCount($searchTerm) {
        try {
            $searchParam = "%{$searchTerm}%";
            $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                    WHERE name LIKE '{$searchParam}' 
                    OR email LIKE '{$searchParam}' 
                    OR phone LIKE '{$searchParam}'";
            $result = $this->dbInstance->selectOne($sql);
            return $result['count'] ?? 0;
        } catch (\Exception $e) {
            throw new \Exception("Error counting search: " . $e->getMessage());
        }
    }

    /**
     * Tìm guest theo email
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = '{$email}'";
            return $this->dbInstance->selectOne($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error finding guest by email: " . $e->getMessage());
        }
    }
}
?>
