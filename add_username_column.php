<?php
$con = mysqli_connect("127.0.0.1", "root", "", "web_hotel_mngt", 3307);
if (!$con) {
    echo "Kết nối thất bại: " . mysqli_connect_error();
    exit;
}

// Kiểm tra cột TenDangNhap đã tồn tại chưa
$checkCol = mysqli_query($con, "SHOW COLUMNS FROM hotels_guests LIKE 'TenDangNhap'");
if (mysqli_num_rows($checkCol) > 0) {
    echo "Cột TenDangNhap đã tồn tại";
} else {
    // Thêm cột nếu chưa có
    if (mysqli_query($con, "ALTER TABLE hotels_guests ADD COLUMN TenDangNhap VARCHAR(100) UNIQUE AFTER MaKhachHang")) {
        echo "Thêm cột TenDangNhap thành công";
    } else {
        echo "Lỗi: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>
