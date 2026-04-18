<?php
namespace Shared\Models;

class BaseModel {
    protected $db;
    protected $dbInstance;
    protected $table = '';
    protected $primaryKey = 'id';

    public function __construct() {
        // Get database connection
        if (!class_exists('connectDB')) {
            require_once __DIR__ . '/../../MVC/Core/connectDB.php';
        }
        
        // Get the connection from connectDB
        $this->dbInstance = new \connectDB();
        // Accessing the protected property via reflection or just using methods from connectDB
    }

    /**
     * Get mysqli connection from protected property
     */
    protected function getConnection() {
        // Use reflection to access the protected $con property
        $reflection = new \ReflectionClass($this->dbInstance);
        $property = $reflection->getProperty('con');
        $property->setAccessible(true);
        return $property->getValue($this->dbInstance);
    }

    /**
     * Lấy tất cả records
     */
    public function all() {
        try {
            $sql = "SELECT * FROM {$this->table}";
            $result = $this->dbInstance->select($sql);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error fetching from {$this->table}: " . $e->getMessage());
        }
    }

    /**
     * Tìm 1 record theo ID
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function find($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $result = $this->dbInstance->selectOne($sql, [$id]);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error finding record: " . $e->getMessage());
        }
    }

    /**
     * Lấy records với pagination
     */
    public function paginate($page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} LIMIT ? OFFSET ?";
            $result = $this->dbInstance->select($sql, [$perPage, $offset]);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error paginating records: " . $e->getMessage());
        }
    }

    /**
     * Lấy tổng số records
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $result = $this->dbInstance->selectOne($sql);
            return $result['count'] ?? 0;
        } catch (\Exception $e) {
            throw new \Exception("Error counting records: " . $e->getMessage());
        }
    }

    /**
     * Thêm record mới
     */
    public function create($data) {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = str_repeat('?,', count($data) - 1) . '?';

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $this->dbInstance->execute($sql, array_values($data));

            // Get the last inserted ID
            $lastId = $this->dbInstance->insertId();
            return $this->find($lastId);
        } catch (\Exception $e) {
            throw new \Exception("Error creating record: " . $e->getMessage());
        }
    }

    /**
     * Cập nhật record
     */
    public function update($id, $data) {
        try {
            $columns = array_keys($data);
            $placeholders = implode(' = ?, ', $columns) . ' = ?';

            $sql = "UPDATE {$this->table} SET {$placeholders} WHERE {$this->primaryKey} = ?";
            $params = array_values($data);
            $params[] = $id;

            $this->dbInstance->execute($sql, $params);
            return $this->find($id);
        } catch (\Exception $e) {
            throw new \Exception("Error updating record: " . $e->getMessage());
        }
    }

    /**
     * Xóa record
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            return $this->dbInstance->execute($sql, [$id]);
        } catch (\Exception $e) {
            throw new \Exception("Error deleting record: " . $e->getMessage());
        }
    }

    /**
     * Tìm records theo condition
     */
    public function where($column, $condition, $value = null) {
        // Support: where('name', 'John') or where('age', '>', 18)
        if ($value === null) {
            $value = $condition;
            $condition = '=';
        }

        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$column} {$condition} ?";
            return $this->dbInstance->select($sql, [$value]);
        } catch (\Exception $e) {
            throw new \Exception("Error in where clause: " . $e->getMessage());
        }
    }

    /**
     * Tìm record đầu tiên theo condition
     */
    public function firstWhere($column, $condition, $value = null) {
        if ($value === null) {
            $value = $condition;
            $condition = '=';
        }

        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$column} {$condition} ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$value]);
        } catch (\Exception $e) {
            throw new \Exception("Error in firstWhere clause: " . $e->getMessage());
        }
    }

    /**
     * Raw query execution
     */
    public function query($sql, $params = []) {
        try {
            return $this->dbInstance->select($sql, $params);
        } catch (\Exception $e) {
            throw new \Exception("Error executing query: " . $e->getMessage());
        }
    }
}
?>
