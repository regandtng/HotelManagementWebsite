<?php
namespace Shared\Models;

class Account extends BaseModel {
    protected $table = 'authentication_admin';
    protected $primaryKey = 'MaAdmin';  // Correct primary key

    /**
     * Tìm account theo username
     */
    public function findByUsername($username) {
        return $this->firstWhere('TenDangNhap', $username);
    }

    /**
     * Tìm account theo email
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$email]);
        } catch (\Exception $e) {
            throw new \Exception("Error finding account: " . $e->getMessage());
        }
    }
}
?>
