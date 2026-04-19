<?php
class connectDB {
    protected $con;

    public function __construct() {
        $host = "127.0.0.1";       
        $user = "root";
        $pass = "";
        $db   = "web_hotel_mngt";   // TÊN DATABASE
        $port = 3307;               // CỔNG MySQL CỦA BẠN

        $this->con = mysqli_connect($host, $user, $pass, $db, $port);

        if (!$this->con) {
            die("Kết nối DB thất bại! (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
        }

        mysqli_set_charset($this->con, "utf8mb4");
    }

    public function select($sql, $params = []) {
        $stmt = $this->con->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function selectOne($sql, $params = []) {
        $stmt = $this->con->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public function execute($sql, $params = []) {
        $stmt = $this->con->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }

    public function insertId() {
        return $this->con->insert_id;
    }

    public function close() {
        if ($this->con) {
            $this->con->close();
        }
    }
}
