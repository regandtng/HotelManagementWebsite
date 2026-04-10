# 📚 Giải Thích Về 2 Loại Controllers

## 🎯 Để Dễ Hiểu

Hãy tưởng tượng Hotel Management là một **nhà hàng**:

- **MVC/Controllers/** = **Nhân viên phục vụ bàn** (gọi là "Walter")
- **src/Api/Controllers/** = **Người nhận lệnh điện thoại** (gọi là "John")

### Walter (MVC/Controllers/)
- Phục vụ **khách ngồi trong nhà hàng**
- Đưa **thực đơn in giấy** cho khách
- Khách **nói bằng miệng**, Walter **viết vào giấy**
- Mang đơn ra bếp, nấu xong, **mang bát ăn sôi nước** trực tiếp lên bàn
- Khách **nhìn thấy, ăn trực tiếp** (HTML - hiển thị trên trình duyệt)

### John (src/Api/Controllers/)
- Nhận lệnh **qua điện thoại hoặc tin nhắn**
- Ghi chép lại thông tin thành **dạng danh sách (JSON)**
- Không quan tâm **khách ở đâu, làm gì**
- Chỉ **ghi đúng, ghi đủ thông tin** (name, price, quantity...)
- Người nhận đơn dùng **ứng dụng điện thoại** để xem thông tin (JSON - dữ liệu thô)

---

## 💡 Sự Khác Nhau Chi Tiết

### 1️⃣ **MVC/Controllers/ = Giao Diện Web (Website)**

**Mục đích:** Hiển thị dữ liệu dạng **trang web đẹp** cho người dùng xem trên trình duyệt

**Ví dụ thực tế:**
```
Bạn vào trang http://localhost/Hotel_Management_Website?controller=DepartmentController
→ Mở ra trang web gồm:
  - Logo, menu, nút bấm
  - Bảng danh sách bộ phận với màu xanh, hình ảnh
  - Form thêm/sửa/xóa bộ phận
  - Nút submit, nút hủy
  (Tất cả đều là **HTML, CSS, JavaScript** - người dùng thấy được)
```

**Cách hoạt động:**
```php
public function index() {
    $departments = $model->getAll();
    // Truyền dữ liệu vào template HTML
    $this->view("Pages/Department", ["departments" => $departments]);
}
```
↓
```html
<!-- Trả về dạng HTML -->
<html>
  <body>
    <table>
      <tr><td>Bộ phận IT</td><td>Sửa | Xóa</td></tr>
      <tr><td>Bộ phận HR</td><td>Sửa | Xóa</td></tr>
    </table>
  </body>
</html>
```

---

### 2️⃣ **src/Api/Controllers/ = API RESTful (Dịch Vụ Web)**

**Mục đích:** Cung cấp **dữ liệu dạng JSON** để các ứng dụng khác (điện thoại, web khác, ...) có thể sử dụng

**Ví dụ thực tế:**
```
Ứng dụng điện thoại gọi API:
GET http://localhost/api/v1/departments
→ Nhận được dữ liệu Json:
{
  "status": 200,
  "data": [
    {"id": 1, "name": "Bộ phận IT", "salary": 5000000},
    {"id": 2, "name": "Bộ phận HR", "salary": 4000000}
  ],
  "message": "Success"
}
```
(Dữ liệu **thô**, ứng dụng điện thoại tự xử lý hiển thị)

**Cách hoạt động:**
```php
public function index() {
    $page = $this->getPage();
    $depts = $deptModel->paginate($page, $perPage);
    // Trả về JSON
    return $this->response->paginate($depts);
}
```
↓
```json
{
  "status": 200,
  "data": [
    {"id": 1, "name": "Bộ phận IT"},
    {"id": 2, "name": "Bộ phận HR"}
  ],
  "pagination": {"page": 1, "per_page": 10, "total": 2}
}
```

---

## 📌 Bảng So Sánh Đơn Giản

| Điểm | MVC/Controllers | src/Api/Controllers |
|------|-----------------|-------------------|
| **Cho ai** | Người dùng xem website | Ứng dụng khác lấy dữ liệu |
| **Trả về** | Trang web HTML đẹp | Dữ liệu JSON thô |
| **Giao diện** | Có button, form, menu | Không cần giao diện |
| **Ví dụ request** | Click chuột, nhập form | Gọi API từ điện thoại |
| **Error** | Hiển thị thông báo lỗi trên trang | Trả JSON lỗi |
| **Kiểu request** | GET/POST thông thường | GET/POST/PUT/DELETE chuẩn RESTful |

---

## 🏗️ Sơ Đồ: Quy Trình Xử Lý

### Khi Người Dùng Vào Website:
```
1. Bạn vào: http://localhost?controller=DepartmentController
2. index.php nhận request
3. Gọi MVC/Controllers/DepartmentController::index()
4. DepartmentController lấy dữ liệu từ database
5. Ghép vào template HTML (Pages/Department.php)
6. Trả về trang web đẹp
7. Bạn thấy trang web trên trình duyệt ✅
```

### Khi Ứng Dụng Điện Thoại Gọi API:
```
1. App gửi: GET http://localhost/api/v1/departments
2. .htaccess chuyển hướng sang api.php
3. api.php gọi Router
4. Router tìm ranh src/Api/Controllers/DepartmentController::index()
5. DepartmentController lấy dữ liệu từ database
6. Format thành JSON + HTTP Status Code
7. Trả về JSON
8. App nhận được dữ liệu, app tự hiển thị ✅
```

---

## ✅ Kết Luận

**Cần cả 2 loại controllers:**

- **MVC/Controllers/** → Cho website UI (admin xem/quản lý)
- **src/Api/Controllers/** → Cho API (ứng dụng khác dùng)

Chúng **không xung đột**, mà là **bổ sung nhau**:
- Người dùng web: dùng website giao diện đẹp
- Developer khác: dùng API để lấy dữ liệu

**Ví dụ thực tế:**
```
Ứng dụng quản lý khách sạn:
├── Website (dùng MVC/Controllers/)
│   ├── Admin xem danh sách bộ phận
│   ├── Admin thêm nhân viên
│   └── Admin xem báo cáo
│
└── Mobile App (dùng src/Api/Controllers/)
    ├── Nhân viên checkin qua app
    ├── Khách book phòng qua app
    └── App lấy dữ liệu từ API
```

**Nếu chỉ có MVC:** Mobile app không có dữ liệu
**Nếu chỉ có API:** Website không có giao diện

---

## 🔧 Trong Project Này

**Trước cải tiến:**
- ❌ Có 2 ApiController → Confusing
- ❌ .htaccess không route đúng → API không hoạt động

**Sau cải tiến:**
- ✅ Xóa ApiController cũ ở MVC/
- ✅ Sửa .htaccess route `/api` → `api.php`
- ✅ Dùng src/Api/Controllers/ cho API
- ✅ Dùng MVC/Controllers/ cho website

**Bây giờ:**
- Website: http://localhost → index.php → MVC/Controllers/
- API: http://localhost/api/v1/... → api.php → src/Api/Controllers/

Mỗi cái làm việc của nó, không xung đột! 🎉
