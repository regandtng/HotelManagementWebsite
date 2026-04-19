# Hướng Dẫn Setup Postman & Các Lỗi Đã Fix

## 📋 Tóm tắt Các Vấn Đề & Fix

### ✅ **Vấn đã được sửa:**

#### 1️⃣ **Prepared Statements không hoạt động** (CRITICAL)
- **Vấn đề**: `connectDB.php` chỉ nhận SQL string, không hỗ trợ parameters
- **Lỗi**: Tất cả database queries không hoạt động
- **Fix**: Thêm `prepare()` và `bind_param()` vào connectDB.php

#### 2️⃣ **Missing method insertId()** 
- **Vấn đề**: `BaseModel::create()` gọi `$dbInstance->insertId()` nhưng không tồn tại
- **Fix**: Thêm method `insertId()` vào connectDB.php

#### 3️⃣ **.htaccess RewriteBase sai**
- **Vấn đề**: `RewriteBase /HOTEL_MANAGEMENT_website/` (chữ thường)
- **Thực tế**: Folder là `/HotelManagementWebsite/` (PascalCase)
- **Lỗi**: API trả về 404 Not Found
- **Fix**: Sửa RewriteBase thành `/HotelManagementWebsite/`

---

## 🔍 **Vấn đề Khác Cần Check**

### ⚠️ **Username/Password không khớp** 
API trả về: `"Tên đăng nhập hoặc mật khẩu không đúng"`

**Nguyên nhân có thể:**
1. User "admin" không tồn tại trong database `authentication_admin`
2. Field name không khớp:
   - AuthController tìm: `TenDangNhap` (field trong bảng)
   - Nhưng bảng có thể chứa: `TenTaiKhoan` (field khác)
3. Mật khẩu được mã hóa khác

**Cần Check:**
```bash
# SSH vào MySQL hoặc PhpMyAdmin:
SELECT * FROM authentication_admin;
```

Xem các columns có:
- ✅ `TenDangNhap` (username column) - hoặc `TenTaiKhoan`?
- ✅ `MatKhau` (password column) - plain hoặc hashed?
- ✅ Có user "admin" tồn tại không?

---

## 🚀 **Bước 1: Import Postman Collection & Environment**

### Postman Collection
File: `Hotel_Management_API.postman_collection.json`
- Đã có hết endpoints
- Variables: `{{base_url}}`, `{{jwt_token}}`

### Postman Environment
File: `postman/Hotel_Management_Environment.json`
- **Tạo mới** (nếu chưa có)
- Chứa biến:
  ```json
  {
    "base_url": "http://localhost/HotelManagementWebsite",
    "api_base": "{{base_url}}/api/v1",
    "jwt_token": "",
    "admin_username": "admin",
    "admin_password": "password123"
  }
  ```

**Step:**
1. Mở Postman
2. **File** → **Import**
3. Chọn collection file → **Import**
4. **File** → **Import**
5. Chọn environment file → **Import**
6. Chọn environment ở dropdown: **"Hotel Management - Local"**

---

## 🔐 **Bước 2: Đăng Nhập & Lấy JWT Token**

### Test Login Endpoint

**Endpoint:**
```
POST http://localhost/HotelManagementWebsite/api/v1/auth/login
```

**Body (JSON):**
```json
{
  "username": "admin",
  "password": "password123"
}
```

**Expected Response (Success):**
```json
{
  "status": "success",
  "message": "Đăng nhập thành công",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "username": "admin",
      "role": "admin"
    }
  }
}
```

**Nếu gặp lỗi:**
```json
{
  "status": "error",
  "message": "Tên đăng nhập hoặc mật khẩu không đúng",
  "errors": null
}
```

**Giải pháp:**
1. **Check database** - User "admin" có tồn tại không?
2. **Check field name** - Có phải `TenDangNhap` không? (có thể là `TenTaiKhoan`)
3. **Check password** - Có plain hoặc hashed?

---

## 🔑 **Bước 3: Auto-Save JWT Token (Optional)**

Thêm script vào **Login** request → **Tests** tab:

```javascript
if (pm.response.code === 200) {
  const jsonData = pm.response.json();
  pm.environment.set("jwt_token", jsonData.data.token);
  console.log("✅ Token saved:", jsonData.data.token);
}
```

Lúc này **mỗi login sẽ tự lưu token vào environment**.

---

## 📡 **Bước 4: Test API Endpoints**

Tất cả endpoints (trừ login) **đều yêu cầu JWT token**:

**Header tự động:**
```
Authorization: Bearer {{jwt_token}}
```

### Các Endpoints Chính:

```
GET    /api/v1/guests              - Lấy danh sách khách
POST   /api/v1/guests              - Tạo khách mới
GET    /api/v1/guests/:id          - Chi tiết khách
PUT    /api/v1/guests/:id          - Cập nhật khách
DELETE /api/v1/guests/:id          - Xóa khách

GET    /api/v1/rooms               - Lấy danh sách phòng
POST   /api/v1/rooms               - Tạo phòng mới
GET    /api/v1/rooms/:id           - Chi tiết phòng
...

GET    /api/v1/bookings            - Lấy danh sách đặt phòng
POST   /api/v1/bookings            - Tạo đặt phòng mới
...
```

### Test Ví Dụ:

**GET /api/v1/guests**
- **Quey params**: `page=1`, `per_page=10`, `search=john` (optional)
- Response: Danh sách khách hàng có phân trang

**POST /api/v1/guests**
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+84912345678",
    "address": "123 Main St"
  }
  ```
- Response: Guest vừa tạo

---

## ⚙️ **Fix Verification Checklist**

- [x] **.htaccess** RewriteBase sửa thành `/HotelManagementWebsite/`
- [x] **connectDB.php** - Thêm prepared statements + `insertId()` method
- [ ] **Database** - Check user "admin" tồn tại, field name đúng
- [ ] **Login** - Thử login, lấy JWT token
- [ ] **Other endpoints** - Thử GET /api/v1/guests (yêu cầu token)

---

## 🐛 **Debug Tips**

### Xem chi tiết lỗi:
```php
// Trong api.php, tạm bật error display:
ini_set('display_errors', 1);
```

### Kiểm tra Request Method:
```php
// Xem method trong API
echo $request->getMethod();  // GET, POST, PUT, DELETE?
echo $request->getPath();    // /api/v1/guests?
```

### Kiểm tra JWT Token:
```php
// Trong AuthMiddleware
$token = JWT::getTokenFromHeader();
$decoded = JWT::decode($token);
var_dump($decoded);
```

---

## 📝 **Notes**

- **Database connection**: Port 3307 (XAMPP custom config)
- **JWT Secret**: `your-secret-key-change-this-in-production` (cần thay đổi!)
- **Token expiry**: 24 hours (từ issued time)
- **CORS**: Enabled cho tất cả origins
- **Response format**: Always JSON

---

**Status**: ✅ API routing fixed & working
**Next**: Fix database field mapping & test login
