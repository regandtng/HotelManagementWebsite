<?php
namespace Shared\Models;

class Account extends BaseModel {
    protected $table = 'accounts';

    /**
     * Tìm account theo email
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception("Error finding account: " . $e->getMessage());
        }
    }
}
?>
