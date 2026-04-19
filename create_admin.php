<?php
$con = mysqli_connect("127.0.0.1", "root", "", "web_hotel_mngt", 3307);
if (!$con) {
    echo "Kết nối thất bại: " . mysqli_connect_error();
    exit;
}

// Thêm tài khoản admin test
$sql = "INSERT INTO authentication_admin (TenDangNhap, MatKhau) VALUES ('admin', '123')
        ON DUPLICATE KEY UPDATE MatKhau = '123'";

if (mysqli_query($con, $sql)) {
    echo "Thêm/Cập nhật tài khoản admin thành công!<br>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>123</strong>";
} else {
    echo "Lỗi: " . mysqli_error($con);
}

mysqli_close($con);
?>
